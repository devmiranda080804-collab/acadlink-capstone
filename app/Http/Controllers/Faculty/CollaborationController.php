<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\CollaborativeDocument;
use App\Models\Course;
use App\Models\DocumentVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class CollaborationController extends Controller
{
    // Verify na ang course ay nasa program ng faculty
    protected function authorizeCourse(Course $course): void
    {
        abort_unless($course->program === auth()->user()->program, 403);
    }

    // Verify na ang document ay nasa program ng faculty
    protected function authorizeDocument(CollaborativeDocument $document): void
    {
        abort_unless($document->course->program === auth()->user()->program, 403);
    }

    // Listahan ng documents para sa isang course
    public function index(Course $course)
    {
        $this->authorizeCourse($course);

        $documents = $course->collaborativeDocuments()
            ->with('lastEditor')
            ->latest('updated_at')
            ->get();

        return response()->json($documents);
    }

    // Gumawa ng bagong document
    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $request->validate(['title' => 'required|string|max:255']);

        $document = CollaborativeDocument::create([
            'course_id'  => $course->id,
            'created_by' => auth()->id(),
            'title'      => $request->title,
            'content'    => '',
        ]);

        return response()->json($document);
    }

    // Kunin ang laman ng isang document (para sa polling)
    public function show(CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);

        return response()->json([
            'id'             => $document->id,
            'title'          => $document->title,
            'content'        => $document->content,
            'updated_at'     => $document->updated_at->toIso8601String(),
            'last_edited_by' => $document->lastEditor?->name,
        ]);
    }

    // I-save ang laman (auto-save)
    public function update(Request $request, CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);

        $request->validate([
            'content'      => 'nullable|string',
            'save_version' => 'nullable|boolean',
        ]);

        $document->update([
            'content'        => $request->content ?? '',
            'last_edited_by' => auth()->id(),
        ]);

        // Gumawa ng version snapshot kapag hiningi (hindi kada keystroke)
        if ($request->boolean('save_version')) {
            DocumentVersion::create([
                'document_id' => $document->id,
                'edited_by'   => auth()->id(),
                'content'     => $request->content ?? '',
                'created_at'  => now(),
            ]);
        }

        return response()->json([
            'status'     => 'saved',
            'updated_at' => $document->fresh()->updated_at->toIso8601String(),
        ]);
    }

    // Version history
    public function versions(CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);

        $versions = $document->versions()->with('editor')->take(20)->get()->map(function ($v) {
            return [
                'id'         => $v->id,
                'editor'     => $v->editor?->name ?? 'Unknown',
                'created_at' => $v->created_at->diffForHumans(),
                'preview'    => \Str::limit(strip_tags($v->content), 60),
            ];
        });

        return response()->json($versions);
    }

    // I-restore ang isang lumang version
    public function restoreVersion(CollaborativeDocument $document, DocumentVersion $version)
    {
        $this->authorizeDocument($document);
        abort_unless($version->document_id === $document->id, 404);

        $document->update([
            'content'        => $version->content,
            'last_edited_by' => auth()->id(),
        ]);

        return response()->json(['status' => 'restored', 'content' => $version->content]);
    }

    // Presence — sino ang currently editing
    public function heartbeat(CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);

        DB::table('document_presence')->updateOrInsert(
            ['document_id' => $document->id, 'user_id' => auth()->id()],
            ['last_seen_at' => now()]
        );

        // Kunin lahat ng active sa huling 10 segundo
        $active = DB::table('document_presence')
            ->join('users', 'users.id', '=', 'document_presence.user_id')
            ->where('document_presence.document_id', $document->id)
            ->where('document_presence.last_seen_at', '>=', now()->subSeconds(10))
            ->pluck('users.name');

        return response()->json(['active' => $active]);
    }
        public function export(CollaborativeDocument $document)
    {
        $this->authorizeDocument($document);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Title
        $section->addText($document->title, ['bold' => true, 'size' => 16]);
        $section->addTextBreak(1);

        // Content — hatiin sa paragraphs para maganda ang spacing
        $lines = explode("\n", $document->content ?? '');
        foreach ($lines as $line) {
            $section->addText($line, ['size' => 12]);
        }

        $filename = preg_replace('/[^a-z0-9]/i', '_', $document->title) . '.docx';
        $tempFile = storage_path('app/' . $filename);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}