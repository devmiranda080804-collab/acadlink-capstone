<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\CourseMaterial;
use App\Models\RepositoryDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentRepositoryController extends Controller
{
    // Auto-detect ang school year base sa petsa
    protected function schoolYear(Carbon $date): string
    {
        $month = $date->month;
        $year  = $date->year;
        // Aug (8) pataas = simula ng bagong SY
        if ($month >= 8) {
            return $year . '-' . ($year + 1);
        }
        return ($year - 1) . '-' . $year;
    }

    // Auto-detect ang semester base sa buwan
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

        // 1. Approved templates
        Template::with('faculty')->where('status', 'approved')->get()->each(function ($t) use (&$documents) {
            $documents->push([
                'id'        => $t->id,
                'source'    => 'template',
                'title'     => $t->title,
                'type'      => ucwords(str_replace('_', ' ', $t->type)),
                'program'   => $t->program,
                'uploader'  => $t->faculty->name ?? 'Unknown',
                'file_type' => $t->file_type,
                'file_url'  => Storage::url($t->file_path),
                'version'   => 'v1.0',
                'date'      => $t->updated_at,
                'can_delete'=> false,
            ]);
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

        // I-group by school year → semester
        $tree = [];
        foreach ($documents as $doc) {
            $sy  = $this->schoolYear($doc['date']);
            $sem = $this->semester($doc['date']);
            $tree[$sy][$sem][] = $doc;
        }

        // I-sort ang years, pinakabago muna
        krsort($tree);

        // Ayusin ang semester order sa loob ng bawat year
        $semOrder = ['First Semester', 'Second Semester', 'Summer'];
        foreach ($tree as $sy => $sems) {
            $ordered = [];
            foreach ($semOrder as $s) {
                if (isset($sems[$s])) $ordered[$s] = $sems[$s];
            }
            $tree[$sy] = $ordered;
        }

        return view('secretary.document-repository', compact('tree'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|string|max:255',
            'program'  => 'nullable|in:BSA,BSMA,BSOA',
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