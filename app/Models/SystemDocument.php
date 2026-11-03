<?php

namespace App\Models;

use App\Models\Concerns\HasEditableFile;
use Illuminate\Database\Eloquent\Model;

// An official document in the System CMS. The Admin and the Secretary manage these; when
// published, every user can open it. Separate from the faculty's own ContentModule records.
class SystemDocument extends Model
{
    use HasEditableFile;

    const ONLYOFFICE_SOURCE = 'system-documents';

    protected $fillable = [
        'created_by', 'title', 'description', 'is_published',
        'file_path', 'file_name', 'file_type', 'file_size',
    ];

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'last_edited_at' => 'datetime'];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function canManage(User $user): bool
    {
        return in_array($user->role, ['admin', 'secretary'], true);
    }

    public function canView(User $user): bool
    {
        return $this->is_published || self::canManage($user);
    }

    public function canEdit(User $user): bool
    {
        return self::canManage($user) && $this->isEditableFile();
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
