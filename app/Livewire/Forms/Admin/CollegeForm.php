<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Admin;

use App\Livewire\Forms\Concerns\NormalizesFormData;
use App\Models\College;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CollegeForm extends Form
{
    use NormalizesFormData;

    public ?College $college = null;

    public ?int $campusId = null;

    public string $code = '';

    public string $name = '';

    public string $description = '';

    public bool $is_active = true;

    public function setCollege(College $college): void
    {
        $this->college = $college;
        $this->campusId = $college->campus_id;
        $this->code = $college->code;
        $this->name = $college->name;
        $this->description = $college->description ?? '';
        $this->is_active = $college->is_active;
    }

    public function resetForm(): void
    {
        $this->reset(['college', 'campusId', 'code', 'name', 'description']);
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
                Rule::unique('colleges', 'code')
                    ->ignore($this->college?->id)
                    ->where(fn ($query) => $query
                        ->where('campus_id', $this->campusId ?? $this->college?->campus_id)
                        ->whereNull('deleted_at')),
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
