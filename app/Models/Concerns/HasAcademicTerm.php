<?php

namespace App\Models\Concerns;

use App\Support\AcademicTerm;

// A document that belongs to the academic year and semester it was uploaded in. New rows
// take the current term; the document pages filter by it (see AcademicTerm::requested()).
trait HasAcademicTerm
{
    public static function bootHasAcademicTerm(): void
    {
        static::creating(function ($model) {
            $model->school_year ??= AcademicTerm::currentSchoolYear();
            $model->semester ??= AcademicTerm::currentSemester();
        });
    }

    // Null for either part means any.
    public function scopeInTerm($query, ?string $schoolYear, ?string $semester)
    {
        return $query
            ->when($schoolYear, fn ($q) => $q->where($this->qualifyColumn('school_year'), $schoolYear))
            ->when($semester, fn ($q) => $q->where($this->qualifyColumn('semester'), $semester));
    }

    // "1st Sem, A.Y. 2026-2027"
    public function getTermLabelAttribute(): string
    {
        return AcademicTerm::shortLabel($this->semester, $this->school_year);
    }
}
