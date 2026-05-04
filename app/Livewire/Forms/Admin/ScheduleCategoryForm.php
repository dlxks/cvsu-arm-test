<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Admin;

use App\Models\ScheduleCategory;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ScheduleCategoryForm extends Form
{
    public ?ScheduleCategory $scheduleCategory = null;

    public string $name = '';

    public string $slug = '';

    public bool $is_active = true;

    public function setScheduleCategory(ScheduleCategory $scheduleCategory): void
    {
        $this->scheduleCategory = $scheduleCategory;
        $this->name = $scheduleCategory->name;
        $this->slug = $scheduleCategory->slug;
        $this->is_active = $scheduleCategory->is_active;
    }

    public function resetForm(): void
    {
        $this->reset(['scheduleCategory', 'name', 'slug']);
        $this->is_active = true;
    }

    public function validateForm(): array
    {
        $validated = $this->validate($this->rules());
        $validated['name'] = Str::upper(trim($validated['name']));
        $validated['slug'] = Str::slug($validated['slug']);

        return $validated;
    }

    public function rules(): array
    {
        $scheduleCategoryId = $this->scheduleCategory?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('schedule_categories', 'name')->ignore($scheduleCategoryId),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('schedule_categories', 'slug')->ignore($scheduleCategoryId),
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }
}