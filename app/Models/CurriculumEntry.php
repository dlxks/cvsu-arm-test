<?php

namespace App\Models;

use Database\Factories\CurriculumEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
