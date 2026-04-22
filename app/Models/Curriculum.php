<?php

namespace App\Models;

use Database\Factories\CurriculumFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['program_id', 'title', 'year_implemented'])]
class Curriculum extends Model
{
    /** @use HasFactory<CurriculumFactory> */
    use HasFactory;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(CurriculumEntry::class);
    }
}
