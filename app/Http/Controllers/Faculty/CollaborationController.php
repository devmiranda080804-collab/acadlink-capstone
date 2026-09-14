<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\CollaborativeDocument;
use App\Models\Course;
use App\Models\ProgramAssignment;
use App\Models\User;
use App\Services\GoogleDocsService;
use Illuminate\Http\Request;

class CollaborationController extends Controller
{
    // Verify that the course belongs to the faculty member's program
    protected function authorizeCourse(Course $course): void
    {
        abort_unless($course->program === auth()->user()->program, 403);
    }

    // Verify that the document belongs to the faculty member's program
    protected function authorizeDocument(CollaborativeDocument $document): void
    {
        abort_unless($document->course->program === auth()->user()->program, 403);
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
    // live inside Google Docs, not in this app
    public function show(CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);

        return response()->json([
            'id'             => $document->id,
            'title'          => $document->title,
            'google_edit_url' => $document->google_edit_url,
            'creator'        => $document->creator?->name,
            'updated_at'     => $document->updated_at->toIso8601String(),
        ]);
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
