<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['code', 'title', 'description', 'no_of_years', 'level', 'is_active'])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory, SoftDeletes;

    public const LEVELS = [
        'UNDERGRADUATE' => 'Undergraduate',
        'GRADUATE' => 'Graduate',
        'PRE-BACCALAUREATE' => 'Pre-Baccalaureate',
        'POST-BACCALAUREATE' => 'Post-Baccalaureate',
    ];

    public static function levelOptions(?iterable $levels = null): array
    {
        return collect($levels ?? array_keys(self::LEVELS))
            ->filter(fn (mixed $level): bool => filled($level))
            ->map(fn (mixed $level): string => (string) $level)
            ->unique()
            ->values()
            ->map(fn (string $level): array => [
                'id' => $level,
                'name' => self::LEVELS[$level] ?? $level,
            ])
            ->all();
    }

    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => (bool) $value,
        );
    }

    protected function levelLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::LEVELS[$this->level] ?? ($this->level ?: '-'),
        );
    }

    protected function durationLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => filled($this->no_of_years) ? $this->no_of_years.' '.Str::plural('year', $this->no_of_years) : '-',
        );
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => trim($this->code.' - '.$this->title, ' -'),
        );
    }

    public function colleges(): BelongsToMany
    {
        return $this->belongsToMany(College::class, 'college_programs')
            ->withTimestamps();
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_program')
            ->withTimestamps();
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class);
    }
}
