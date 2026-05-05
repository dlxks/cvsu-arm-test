<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Concerns;

use Illuminate\Support\Str;

trait NormalizesFormData
{
    protected function trimmedString(mixed $value): string
    {
        return Str::of((string) $value)
            ->trim()
            ->value();
    }

    protected function nullableTrimmedString(mixed $value): ?string
    {
        $normalizedValue = $this->trimmedString($value);

        return $normalizedValue !== '' ? $normalizedValue : null;
    }
}