<?php

namespace App\Http\Controllers;

use App\Models\ContentModule;
use App\Models\SharedResource;
use App\Models\SystemDocument;
use App\Models\TemplateDocument;
use App\Models\User;
use App\Services\OnlyOffice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// What the editor service itself calls. There's no signed-in user on these requests, so
// each one proves itself: a signed link for the file, a signed message for the callback.
class OnlyOfficeController extends Controller
{
    // Sent when every editor has closed: the document is ready, so it gets a new key.
    const STATUS_READY_TO_SAVE = 2;

    // Sent when someone presses Save while the document stays open: the key must not change.
    const STATUS_FORCE_SAVE = 6;

    public function file(string $source, int $id)
    {
        $document = $this->document($source, $id);
        abort_unless($document->file_path && Storage::disk('public')->exists($document->file_path), 404);

        return response()->file(Storage::disk('public')->path($document->file_path), ['Cache-Control' => 'no-store']);
    }

    // The editor treats anything except {"error":0} as a failed save and keeps its copy.
    public function callback(Request $request, OnlyOffice $onlyOffice, string $source, int $id)
    {
        $module = $this->document($source, $id);
        $data = $onlyOffice->verifiedCallback($request->input('token'), $request->bearerToken());

        if ($data === null) {
            return response()->json(['error' => 1], 403);
        }

        $status = (int) ($data['status'] ?? 0);
        if (!in_array($status, [self::STATUS_READY_TO_SAVE, self::STATUS_FORCE_SAVE], true)) {
            return response()->json(['error' => 0]);
        }

        if (($data['key'] ?? null) !== $module->document_key || !$module->acceptsEditorSave() || empty($data['url'])) {
            Log::warning('Document editor sent a save for an outdated document', ['source' => $source, 'id' => $module->id, 'status' => $status]);

            return response()->json(['error' => 1]);
        }

        try {
            $response = Http::timeout(60)->get($data['url']);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['error' => 1]);
        }

        if (!$response->successful() || $response->body() === '') {
            Log::warning('Could not download the edited document', ['source' => $source, 'id' => $module->id, 'http' => $response->status()]);

            return response()->json(['error' => 1]);
        }

        // Saving again with no changes shouldn't fill the history with copies.
        if (md5($response->body()) !== md5(Storage::disk('public')->get($module->file_path))) {
            $module->keepCurrentVersion();
        }

        Storage::disk('public')->put($module->file_path, $response->body());

        $editorId = $data['users'][0] ?? ($data['actions'][0]['userid'] ?? null);
        $module->forceFill([
            'file_size'      => strlen($response->body()),
            'last_edited_by' => $editorId && User::whereKey($editorId)->exists() ? (int) $editorId : $module->last_edited_by,
            'last_edited_at' => now(),
            'document_key'   => $status === self::STATUS_READY_TO_SAVE ? OnlyOffice::newKey($module) : $module->document_key,
        ])->save();

        return response()->json(['error' => 0]);
    }

    protected function document(string $source, int $id): Model
    {
        $class = match ($source) {
            ContentModule::ONLYOFFICE_SOURCE => ContentModule::class,
            SystemDocument::ONLYOFFICE_SOURCE => SystemDocument::class,
            TemplateDocument::ONLYOFFICE_SOURCE => TemplateDocument::class,
            SharedResource::ONLYOFFICE_SOURCE => SharedResource::class,
            default => abort(404),
        };

        return $class::findOrFail($id);
    }
}
