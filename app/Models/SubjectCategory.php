<?php

namespace App\Models;

use Database\Factories\SubjectCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class SubjectCategory extends Model
{
    /** @use HasFactory<SubjectCategoryFactory> */
    use HasFactory;

    public function curriculumEntries(): HasMany
    {
        return $this->hasMany(CurriculumEntry::class);
    }
}
