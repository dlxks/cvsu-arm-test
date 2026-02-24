<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    /** @use HasFactory<\Database\Factories\BranchFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'code',
        'name',
        'type',
        'address',
        'is_active',
    ];

    /**
     * Generate the next available ID based on the type.
     * M = Main (CvSU-M001), S = Satellite (CvSU-S001)
     */
    public static function generateNextId(string $type): string
    {
        $prefixMap = [
            'Main' => 'M',
            'Satellite' => 'S',
        ];

        $char = $prefixMap[$type] ?? 'O';
        $prefix = "CvSU-{$char}";

        // Search against branch_id instead of id
        $latest = self::where('branch_id', 'LIKE', "{$prefix}%")
            ->orderByRaw('LENGTH(branch_id) DESC')
            ->orderBy('branch_id', 'desc')
            ->first();

        if (! $latest) {
            return "{$prefix}001";
        }

        $numberPart = str_replace($prefix, '', $latest->branch_id); // Target branch_id
        $number = (int) $numberPart;
        $nextNumber = str_pad($number + 1, 3, '0', STR_PAD_LEFT);

        return "{$prefix}{$nextNumber}";
    }

    /**
     * Relationships declaration
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'branch_id', 'branch_id');
    }
}
