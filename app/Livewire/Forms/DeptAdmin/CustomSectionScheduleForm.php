<?php

declare(strict_types=1);

namespace App\Livewire\Forms\DeptAdmin;

use App\Models\CurriculumEntry;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CustomSectionScheduleForm extends Form
{
    public ?int $subject_id = null;

    public string $program_code = '';

    public ?int $year_level = null;

    public string $section_identifier = '';

    public string $section_type = 'IRREGULAR';

    public string $semester = '1ST';

    public string $school_year = '';

    public int $slots = 40;

    public ?string $nstp_track = null;

    public function resetForm(string $schoolYear): void
    {
        $this->reset([
            'subject_id',
            'program_code',
            'year_level',
            'section_identifier',
            'section_type',
            'semester',
            'school_year',
            'slots',
            'nstp_track',
        ]);

        $this->section_type = 'IRREGULAR';
        $this->semester = '1ST';
        $this->school_year = $schoolYear;
        $this->slots = 40;
    }

    public function resetAfterSubmit(): void
    {
        $this->subject_id = null;
        $this->program_code = '';
        $this->year_level = null;
        $this->section_identifier = '';
        $this->nstp_track = null;
    }

    /**
     * @return array<string, mixed>
     */
    public function validateForm(): array
    {
        $validated = $this->validate($this->rules());

        $validated['program_code'] = Str::upper(trim((string) $validated['program_code']));
        $validated['section_identifier'] = Str::upper(trim((string) $validated['section_identifier']));

        if (($validated['section_type'] ?? null) !== 'NSTP') {
            $validated['nstp_track'] = null;
        }

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'program_code' => ['required', 'string', 'max:20'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:8'],
            'section_identifier' => ['required', 'string', 'max:20'],
            'section_type' => ['required', Rule::in(['IRREGULAR', 'PETITION', 'NSTP', 'OTHERS'])],
            'semester' => ['required', Rule::in(array_keys(CurriculumEntry::SEMESTERS))],
            'school_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'slots' => ['required', 'integer', 'min:1', 'max:500'],
            'nstp_track' => ['nullable', Rule::in(['CWTS', 'ROTC'])],
        ];
    }
}
