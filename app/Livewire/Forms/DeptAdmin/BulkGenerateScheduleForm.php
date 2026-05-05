<?php

declare(strict_types=1);

namespace App\Livewire\Forms\DeptAdmin;

use App\Enums\PermissionEnum;
use App\Models\Curriculum;
use App\Models\CurriculumEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Form;

class BulkGenerateScheduleForm extends Form
{
    public ?int $program_id = null;

    public ?int $year_level = null;

    public string $semester = '1ST';

    public string $school_year = '';

    public int $section_count = 1;

    public int $slots = 40;

    public function resetForm(string $schoolYear): void
    {
        $this->reset([
            'program_id',
            'year_level',
            'semester',
            'school_year',
            'section_count',
            'slots',
        ]);

        $this->semester = '1ST';
        $this->school_year = $schoolYear;
        $this->section_count = 1;
        $this->slots = 40;
    }

    public function resetAfterSubmit(): void
    {
        $this->program_id = null;
        $this->year_level = null;
        $this->section_count = 1;
        if (! $this->canModifySlots()) {
            $this->slots = 40;
        }
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
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'year_level' => [
                'required',
                'integer',
                ValidationRule::in($this->availableYearLevels()),
            ],
            'semester' => ['required', Rule::in(array_keys(CurriculumEntry::SEMESTERS))],
            'school_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'section_count' => ['required', 'integer', 'min:1', 'max:30'],
            'slots' => $this->canModifySlots()
                ? ['required', 'integer', 'min:1', 'max:500']
                : ['integer', 'min:1', 'max:500'],
        ];
    }

    /**
     * @return array<int, int>
     */
    public function availableYearLevels(): array
    {
        if ($this->program_id === null) {
            return [];
        }

        return Curriculum::query()
            ->where('program_id', $this->program_id)
            ->join('curriculum_entries', 'curricula.id', '=', 'curriculum_entries.curriculum_id')
            ->whereNotNull('curriculum_entries.year_level')
            ->distinct()
            ->orderBy('curriculum_entries.year_level')
            ->pluck('curriculum_entries.year_level')
            ->map(static fn (mixed $yearLevel): int => (int) $yearLevel)
            ->values()
            ->all();
    }

    private function canModifySlots(): bool
    {
        return Auth::user()?->can(PermissionEnum::SCHEDULE_SLOT_MODIFY->value) ?? false;
    }
}
