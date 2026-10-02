<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\TemplateDocument;
use App\Models\CourseMaterial;
use App\Models\RepositoryDocument;
use App\Support\Programs;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentRepositoryController extends Controller
{
    // Auto-detect the school year based on the date
    protected function schoolYear(Carbon $date): string
    {
        $month = $date->month;
        $year  = $date->year;
        // Aug (8) onwards = start of a new SY
        if ($month >= 8) {
            return $year . '-' . ($year + 1);
        }
        return ($year - 1) . '-' . $year;
    }

    // Auto-detect the semester based on the month
    protected function semester(Carbon $date): string
    {
        $month = $date->month;
        if ($month >= 8 && $month <= 12) return 'First Semester';
        if ($month >= 1 && $month <= 5)  return 'Second Semester';
        return 'Summer'; // Jun-Jul
    }

    public function index(Request $request)
    {
        $documents = collect();

        // 1. Forwarded templates — the current pipeline is Admin creates ->
        // Secretary forwards -> Program Head distributes per program. A
        // forwarded template counts as "official" here since forwarding is
        // this Secretary's own action; one row per program it serves, since
        // a single master template can be shared across several programs.
        TemplateDocument::with(['creator', 'programs'])
            ->whereNotNull('forwarded_at')
            ->get()
            ->each(function ($t) use (&$documents) {
                foreach ($t->programs as $p) {
                    $documents->push([
                        'id'        => $t->id,
                        'source'    => 'template',
                        'title'     => $t->title,
                        'type'      => ucwords(str_replace('_', ' ', $t->type)),
                        'program'   => $p->program,
                        'uploader'  => $t->creator->name ?? 'Unknown',
                        'file_type' => $t->file_type,
                        'file_url'  => Storage::url($t->file_path),
                        'version'   => 'v1.0',
                        'date'      => $p->distributed_at ?? $t->forwarded_at,
                        'can_delete'=> false,
                    ]);
                }
            });

        // 2. Course materials
        CourseMaterial::with(['course', 'uploader'])->get()->each(function ($m) use (&$documents) {
            $documents->push([
                'id'        => $m->id,
                'source'    => 'material',
                'title'     => $m->title,
                'type'      => 'Course Material',
                'program'   => $m->course->program ?? '—',
                'uploader'  => $m->uploader->name ?? 'Unknown',
                'file_type' => $m->file_type,
                'file_url'  => Storage::url($m->file_path),
                'version'   => $m->version,
                'date'      => $m->created_at,
                'can_delete'=> false,
            ]);
        });

        // 3. Manual uploads
        RepositoryDocument::with('uploader')->get()->each(function ($d) use (&$documents) {
            $documents->push([
                'id'        => $d->id,
                'source'    => 'upload',
                'title'     => $d->title,
                'type'      => ucwords(str_replace('_', ' ', $d->doc_type)),
                'program'   => $d->program ?? 'General',
                'uploader'  => $d->uploader->name ?? 'Unknown',
                'file_type' => $d->file_type,
                'file_url'  => Storage::url($d->file_path),
                'version'   => 'v1.0',
                'date'      => $d->created_at,
                'can_delete'=> true,
            ]);
        });

        // Group by school year → semester
        $tree = [];
        foreach ($documents as $doc) {
            $sy  = $this->schoolYear($doc['date']);
            $sem = $this->semester($doc['date']);
            $tree[$sy][$sem][] = $doc;
        }

        // Sort the years, newest first
        krsort($tree);

        // Arrange the semester order within each year, and sort each
        // semester's own documents newest-first — grouping alone left them
        // in whatever order the three sources (templates/materials/uploads)
        // happened to be queried in, not by date.
        $semOrder = ['First Semester', 'Second Semester', 'Summer'];
        foreach ($tree as $sy => $sems) {
            $ordered = [];
            foreach ($semOrder as $s) {
                if (isset($sems[$s])) {
                    usort($sems[$s], fn($a, $b) => $b['date'] <=> $a['date']);
                    $ordered[$s] = $sems[$s];
                }
            }
            $tree[$sy] = $ordered;
        }

        return view('secretary.document-repository', compact('tree'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'program'  => 'nullable|in:' . implode(',', Programs::codes()),
            'doc_type' => 'required|string|max:50',
            'file'     => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('repository', 'public');

        RepositoryDocument::create([
            'uploaded_by' => auth()->id(),
            'title'       => $request->title,
            'program'     => $request->program,
            'doc_type'    => $request->doc_type,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_type'   => $file->getClientOriginalExtension(),
            'file_size'   => $file->getSize(),
        ]);

        return back()->with('success', 'Document uploaded to repository.');
    }

    public function destroy(RepositoryDocument $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document removed from repository.');
    }
}