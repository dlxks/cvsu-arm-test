<?php

declare(strict_types=1);

namespace App\Livewire\Forms\CollegeAdmin;

use Livewire\Form;

class CreateScheduleServiceRequestForm extends Form
{
    /** @var array<int, string> */
    public array $section_names = [];

    /** @var array<int, int> */
    public array $schedule_ids = [];

    public ?int $servicing_college_id = null;

    public function resetForm(): void
    {
        $this->section_names = [];
        $this->schedule_ids = [];
        $this->servicing_college_id = null;
    }

    /**
     * @return array<string, mixed>
     */
    public function validateForm(): array
    {
        return $this->validate($this->rules());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'section_names' => ['required', 'array', 'min:1'],
            'section_names.*' => ['string'],
            'schedule_ids' => ['required', 'array', 'min:1'],
            'schedule_ids.*' => ['integer', 'exists:schedules,id'],
            'servicing_college_id' => ['required', 'integer', 'exists:colleges,id'],
        ];
    }
}
