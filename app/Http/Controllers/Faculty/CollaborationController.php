<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\CollaborativeDocument;
use App\Models\CollaborativeDocumentViewer;
use App\Models\Course;
use App\Models\DocumentVersion;
use App\Models\ProgramAssignment;
use App\Models\User;
use App\Services\GoogleDocsService;
use Illuminate\Http\Request;

class CollaborationController extends Controller
{
    // Verify that this faculty member is actually assigned to teach this course
    protected function authorizeCourse(Course $course): void
    {
        abort_unless(
            ProgramAssignment::where('course_id', $course->id)->where('faculty_id', auth()->id())->exists(),
            403
        );
    }

    // Verify that this faculty member is actually assigned to teach the document's course
    protected function authorizeDocument(CollaborativeDocument $document): void
    {
        $this->authorizeCourse($document->course);
    }

    // Every faculty member ever assigned to teach this course — the people
    // who should all get edit access, so the same course stays aligned
    // across sections/instructors
    protected function instructorsFor(Course $course, User $alwaysInclude)
    {
        $facultyIds = ProgramAssignment::where('course_id', $course->id)
            ->distinct()
            ->pluck('faculty_id')
            ->push($alwaysInclude->id)
            ->unique();

        return User::whereIn('id', $facultyIds)->get();
    }

    // List of documents for a course
    public function index(Course $course)
    {
        $this->authorizeCourse($course);

        $documents = $course->collaborativeDocuments()
            ->with('creator')
            ->latest('updated_at')
            ->get();

        return response()->json($documents);
    }

    // Create a new document — creates the real Google Doc, then shares it
    // with every instructor who has taught this course
    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $request->validate(['title' => 'required|string|max:255']);

        $me = auth()->user();
        abort_unless($me->google_email, 422, 'Add your Google email to your account first — ask your Program Head, Secretary, or Admin to set it.');

        $google = new GoogleDocsService();
        $googleDocId = $google->createDocument($request->title);

        foreach ($this->instructorsFor($course, $me) as $instructor) {
            if ($instructor->google_email) {
                $google->shareWithEmail($googleDocId, $instructor->google_email);
            }
        }

        $document = CollaborativeDocument::create([
            'course_id'     => $course->id,
            'created_by'    => $me->id,
            'title'         => $request->title,
            'google_doc_id' => $googleDocId,
        ]);

        return response()->json($document);
    }

    // Document metadata + the Google Docs edit link — editing itself happens
    // live inside Google Docs, not in this app. Also tracks that this user just
    // opened it ("who's active") and, if the last saved snapshot is stale, takes
    // a fresh one automatically — an honest, in-app approximation of the
    // manuscript's "automatic saving" / "who's editing" claims, since the real
    // editing happens inside Google Docs where this app has no live visibility.
    public function show(CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);

        CollaborativeDocumentViewer::updateOrCreate(
            ['collaborative_document_id' => $document->id, 'user_id' => auth()->id()],
            ['last_opened_at' => now()]
        );

        // Best-effort: a transient Google API hiccup shouldn't break viewing the
        // document. The explicit snapshot() endpoint below still surfaces failures,
        // since there the user asked for a save and deserves to know it didn't happen.
        $latestVersion = $document->versions()->first();
        if (!$latestVersion || $latestVersion->created_at->lt(now()->subMinutes(5))) {
            try {
                $this->takeSnapshot($document);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $activeSince = now()->subMinutes(30);

        return response()->json([
            'id'              => $document->id,
            'title'           => $document->title,
            'google_edit_url' => $document->google_edit_url,
            'creator'         => $document->creator?->name,
            'updated_at'      => $document->updated_at->toIso8601String(),
            'viewers'         => $document->viewers()->with('user')->get()->map(fn($v) => [
                'name'           => $v->user->name,
                'last_opened_at' => $v->last_opened_at->toIso8601String(),
                'active_now'     => $v->last_opened_at->gte($activeSince),
            ]),
            'versions' => $document->versions()->with('editor')->get()->map(fn($v) => [
                'id'         => $v->id,
                'editor'     => $v->editor?->name,
                'created_at' => $v->created_at->toIso8601String(),
                'preview'    => \Illuminate\Support\Str::limit($v->content, 200),
            ]),
        ]);
    }

    // Explicit "Save Version Now" action — same export+snapshot logic as the
    // throttled automatic one in show(), just user-triggered and unthrottled.
    public function snapshot(CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);
        $saved = $this->takeSnapshot($document);

        return response()->json([
            'status'  => $saved ? 'snapshotted' : 'unchanged',
            'message' => $saved ? null : 'No changes since the last saved version.',
        ]);
    }

    // Returns true if a new version was actually saved, false if skipped
    // because the content hasn't changed since the last saved version — this
    // is what keeps "version history" meaning real edits, not just repeated
    // views of an unchanged document.
    private function takeSnapshot(CollaborativeDocument $document): bool
    {
        abort_unless($document->google_doc_id, 422, 'This document has no linked Google Doc.');

        $content = (new GoogleDocsService())->exportPlainText($document->google_doc_id);

        $latest = $document->versions()->first();
        if ($latest && $latest->content === $content) {
            return false;
        }

        DocumentVersion::create([
            'document_id' => $document->id,
            'edited_by'   => auth()->id(),
            'content'     => $content,
            'created_at'  => now(),
        ]);

        return true;
    }

    // Re-share the document with anyone newly assigned to teach this course
    // since it was created (e.g. a new section's instructor)
    public function resync(CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);
        abort_unless($document->google_doc_id, 422, 'This document has no linked Google Doc.');

        $google = new GoogleDocsService();
        foreach ($this->instructorsFor($document->course, auth()->user()) as $instructor) {
            if ($instructor->google_email) {
                $google->shareWithEmail($document->google_doc_id, $instructor->google_email);
            }
        }

        return response()->json(['status' => 'resynced']);
    }

    // Only the document's creator can delete it — a shared doc shouldn't
    // disappear on other instructors without warning
    public function destroy(CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);
        abort_unless($document->created_by === auth()->id(), 403, 'Only the person who created this document can delete it.');

        if ($document->google_doc_id) {
            (new GoogleDocsService())->deleteDocument($document->google_doc_id);
        }

        $document->delete();

        return response()->json(['status' => 'deleted']);
    }
}
