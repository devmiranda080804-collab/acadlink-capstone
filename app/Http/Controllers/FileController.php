<?php

namespace App\Http\Controllers;

use App\Models\ContentModule;
use App\Models\CourseMaterial;
use App\Models\CourseTopic;
use App\Models\RepositoryDocument;
use App\Models\SharedResource;
use App\Models\Submission;
use App\Models\TemplateDocument;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

// One way to open every uploaded file in the system: a viewer page that previews it,
// the raw file shown inline (never downloaded), and a separate download. Each source
// keeps the same visibility rules its own page already applies.
class FileController extends Controller
{
    const PREVIEW_IMAGES = ['png', 'jpg', 'jpeg', 'gif', 'webp'];

    public function show(string $source, int $id)
    {
        $file = $this->resolve($source, $id);

        $type = strtolower($file['type']);
        $previewAs = match (true) {
            $type === 'pdf' => 'pdf',
            $type === 'docx' => 'docx',
            in_array($type, self::PREVIEW_IMAGES, true) => 'image',
            default => 'none',
        };

        return view('files.viewer', [
            'file'        => $file,
            'previewAs'   => $previewAs,
            'rawUrl'      => url("/files/{$source}/{$id}/raw"),
            'downloadUrl' => url("/files/{$source}/{$id}/download"),
        ]);
    }

    public function raw(string $source, int $id)
    {
        $file = $this->resolve($source, $id);

        return Storage::disk('public')->response($file['path'], $file['name'], [
            'Content-Disposition' => 'inline; filename="' . addslashes($file['name']) . '"',
            'Cache-Control'       => 'private, no-store',
        ]);
    }

    public function download(string $source, int $id)
    {
        $file = $this->resolve($source, $id);

        return Storage::disk('public')->download($file['path'], $file['name']);
    }

    // Finds the file, checks the current user may see it, and returns its details.
    protected function resolve(string $source, int $id): array
    {
        $user = auth()->user();

        [$record, $allowed, $title, $uploader, $date] = match ($source) {
            'template' => $this->template($id, $user),
            'material' => $this->material($id, $user),
            'topic' => $this->topic($id, $user),
            'shared' => $this->shared($id, $user),
            'repository' => $this->repository($id, $user),
            'submission' => $this->submission($id, $user),
            'module' => $this->module($id, $user),
            default => abort(404),
        };

        abort_unless($allowed, 403, 'You do not have access to this file.');

        $path = $source === 'topic' ? $record->module_path : $record->file_path;
        $name = $source === 'topic' ? $record->module_file_name : $record->file_name;
        abort_unless($path && Storage::disk('public')->exists($path), 404, 'This file could not be found. It may have been removed.');

        $type = $record->file_type ?? pathinfo($name ?: $path, PATHINFO_EXTENSION);

        return [
            'path'     => $path,
            'name'     => $name ?: basename($path),
            'type'     => $type,
            'size'     => Storage::disk('public')->size($path),
            'title'    => $title,
            'uploader' => $uploader,
            'date'     => $date,
        ];
    }

    protected function isStaff(User $user): bool
    {
        return in_array($user->role, ['admin', 'secretary'], true);
    }

    protected function template(int $id, User $user): array
    {
        $t = TemplateDocument::with(['creator', 'programs'])->findOrFail($id);
        $allowed = $this->isStaff($user) || ($user->program && $t->isDistributedTo($user->program));

        return [$t, $allowed, $t->title, $t->creator->name ?? null, $t->created_at];
    }

    protected function material(int $id, User $user): array
    {
        $m = CourseMaterial::with(['course', 'uploader'])->findOrFail($id);
        $allowed = $this->isStaff($user) || ($m->course && $user->program === $m->course->program);

        return [$m, $allowed, $m->title, $m->uploader->name ?? null, $m->created_at];
    }

    protected function topic(int $id, User $user): array
    {
        $t = CourseTopic::with(['course', 'creator'])->findOrFail($id);
        $allowed = $this->isStaff($user) || ($t->course && $user->program === $t->course->program);

        return [$t, $allowed, $t->topic . ' (module)', $t->creator->name ?? null, $t->updated_at];
    }

    protected function shared(int $id, User $user): array
    {
        $r = SharedResource::with('sharer')->findOrFail($id);
        $allowed = $user->role === 'admin' || $user->program === $r->program;

        return [$r, $allowed, $r->title, $r->sharer->name ?? null, $r->created_at];
    }

    protected function repository(int $id, User $user): array
    {
        $d = RepositoryDocument::with('uploader')->findOrFail($id);

        return [$d, $this->isStaff($user), $d->title, $d->uploader->name ?? null, $d->created_at];
    }

    protected function submission(int $id, User $user): array
    {
        $s = Submission::with(['requirement', 'faculty'])->findOrFail($id);
        $allowed = $this->isStaff($user)
            || $s->faculty_id === $user->id
            || ($user->role === 'program_head' && $s->requirement && $s->requirement->program === $user->program);

        return [$s, $allowed, $s->requirement->title ?? 'Submission', $s->faculty->name ?? null, $s->submitted_at ?? $s->created_at];
    }

    protected function module(int $id, User $user): array
    {
        $m = ContentModule::findOrFail($id);

        return [$m, $m->created_by === $user->id, $m->title, $user->name, $m->created_at];
    }
}
