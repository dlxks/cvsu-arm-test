<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Admin;

use App\Livewire\Forms\Concerns\NormalizesFormData;
use App\Models\Campus;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CampusForm extends Form
{
    use NormalizesFormData;

    public ?Campus $campus = null;

    public string $code = '';

    public string $name = '';

    public string $description = '';

    public bool $is_active = true;

    public function setCampus(Campus $campus): void
    {
        $this->campus = $campus;
        $this->code = $campus->code;
        $this->name = $campus->name;
        $this->description = $campus->description ?? '';
        $this->is_active = $campus->is_active;
    }

    public function resetForm(): void
    {
        $this->reset(['campus', 'code', 'name', 'description']);
        $this->is_active = true;
    }

    public function validateForm(): array
    {
        return $this->validate($this->rules());
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('campuses', 'code')
                    ->ignore($this->campus?->id)
                    ->whereNull('deleted_at'),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function payload(array $validated): array
    {
        return [
            'code' => $this->trimmedString($validated['code']),
            'name' => $this->trimmedString($validated['name']),
            'description' => $this->nullableTrimmedString($validated['description']),
            'is_active' => (bool) $validated['is_active'],
        ];
    }
}
