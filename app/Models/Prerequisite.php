<?php

namespace App\Models;

use Database\Factories\PrerequisiteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['curriculum_entry_id', 'label'])]
class Prerequisite extends Model
{
    /** @use HasFactory<PrerequisiteFactory> */
    use HasFactory;

    public function curriculumEntry(): BelongsTo
    {
        return $this->belongsTo(CurriculumEntry::class);
    }

    public function requiredCurriculumEntries(): BelongsToMany
    {
        return $this->belongsToMany(CurriculumEntry::class, 'prerequisite_subjects')
            ->withTimestamps();
    }
}
