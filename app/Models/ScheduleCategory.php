<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ScheduleCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'slug', 'is_active'])]
class ScheduleCategory extends Model
{
    /** @use HasFactory<ScheduleCategoryFactory> */
    use HasFactory, SoftDeletes;

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): bool => (bool) $value,
        );
    }

    public function roomTimes(): HasMany
    {
        return $this->hasMany(ScheduleRoomTime::class);
    }

    public function facultyAssignments(): HasMany
    {
        return $this->hasMany(ScheduleFaculty::class);
    }
}