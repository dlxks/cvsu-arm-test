<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CurriculumEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['curriculum_id', 'subject_id', 'subject_category_id', 'semester', 'year_level'])]
class CurriculumEntry extends Model
{
    /** @use HasFactory<CurriculumEntryFactory> */
    use HasFactory;

    public const SEMESTERS = [
        '1ST' => '1st Semester',
        '2ND' => '2nd Semester',
        'SUMMER' => 'Summer',
    ];

    public static function semesterLabel(?string $semester): string
    {
        if (! filled($semester)) {
            return '-';
        }

        return self::SEMESTERS[$semester] ?? Str::headline((string) $semester);
    }

    public static function semesterFilterOptions(?iterable $semesters = null): array
    {
        return collect($semesters ?? array_keys(self::SEMESTERS))
            ->filter(fn (mixed $semester): bool => filled($semester))
            ->map(fn (mixed $semester): string => (string) $semester)
            ->unique()
            ->values()
            ->map(fn (string $semester): array => [
                'id' => $semester,
                'name' => self::semesterLabel($semester),
            ])
            ->all();
    }

    public static function semesterSelectOptions(?iterable $semesters = null): array
    {
        return collect(self::semesterFilterOptions($semesters))
            ->map(fn (array $option): array => [
                'label' => $option['name'],
                'value' => $option['id'],
            ])
            ->all();
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function subjectCategory(): BelongsTo
    {
        return $this->belongsTo(SubjectCategory::class);
    }

    public function prerequisites(): HasMany
    {
        return $this->hasMany(Prerequisite::class);
    }

    public function dependentPrerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Prerequisite::class, 'prerequisite_subjects')
            ->withTimestamps();
    }
}
