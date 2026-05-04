<?php

use App\Livewire\Forms\DeptAdmin\CustomSectionScheduleForm;
use App\Models\CurriculumEntry;
use App\Models\Schedule;
use App\Models\Subject;
use App\Services\ScheduleGenerationService;
use App\Traits\CanManage;
use Livewire\Attributes\Computed;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use CanManage, Interactions;

    public CustomSectionScheduleForm $form;

    public int $campusId;
    public int $collegeId;
    public ?int $departmentId = null;
    public string $campusName = '-';
    public string $collegeName = '-';
    public string $departmentName = '-';

    public function mount(): void
    {
        $this->ensureCanManage('schedules.create');

        $user = auth()
            ->guard()
            ->user()
            ?->loadMissing(['employeeProfile.campus', 'employeeProfile.college', 'employeeProfile.department', 'facultyProfile.campus', 'facultyProfile.college', 'facultyProfile.department']);
        $profile = $user?->assignedAcademicProfile();

        abort_unless($profile && filled($profile->campus_id) && filled($profile->college_id), 403);

        $this->campusId = (int) $profile->campus_id;
        $this->collegeId = (int) $profile->college_id;
        $this->departmentId = filled($profile->department_id) ? (int) $profile->department_id : null;
        $this->campusName = $profile->campus?->name ?? '-';
        $this->collegeName = $profile->college?->name ?? '-';
        $this->departmentName = $profile->department?->name ?? 'College-wide';

        $year = (int) now()->format('Y');
        $this->form->resetForm($year . '-' . ($year + 1));
    }

    #[Computed]
    public function subjectOptions(): array
    {
        return Subject::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'title'])
            ->map(fn($s) => ['label' => $s->code . ' – ' . $s->title, 'value' => $s->id])
            ->values()
            ->toArray();
    }

    #[Computed]
    public function sectionTypeOptions(): array
    {
        return collect(['IRREGULAR', 'PETITION', 'NSTP', 'OTHERS'])
            ->map(fn($t) => ['label' => $t, 'value' => $t])
            ->values()
            ->toArray();
    }

    #[Computed]
    public function semesterOptions(): array
    {
        return collect(CurriculumEntry::SEMESTERS)->map(fn($label, $value) => ['label' => $label, 'value' => $value])->values()->toArray();
    }

    #[Computed]
    public function nstpTrackOptions(): array
    {
        return [['label' => 'CWTS', 'value' => 'CWTS'], ['label' => 'ROTC', 'value' => 'ROTC']];
    }

    public function createCustom(): void
    {
        $this->ensureCanManage('schedules.create');

        $validated = $this->form->validateForm();

        $schedule = app(ScheduleGenerationService::class)->createCustomSectionSchedule([
            'campus_id' => $this->campusId,
            'college_id' => $this->collegeId,
            'department_id' => $this->departmentId,
            'subject_id' => $validated['subject_id'],
            'program_code' => $validated['program_code'],
            'year_level' => $validated['year_level'],
            'section_identifier' => $validated['section_identifier'],
            'section_type' => $validated['section_type'],
            'semester' => $validated['semester'],
            'school_year' => $validated['school_year'],
            'slots' => $validated['slots'],
            'nstp_track' => $validated['nstp_track'],
        ]);

        $this->form->resetAfterSubmit();
        $this->toast()
            ->success('Created', 'Section schedule ' . $schedule->sched_code . ' created.')
            ->send();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold dark:text-white">Custom Section Creation</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $campusName }} | {{ $collegeName }} | {{ $departmentName }}
            </p>
        </div>
        <a href="{{ route('schedules.index') }}"
            class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">
            <x-icon name="arrow-left" class="h-4 w-4" /> Back to Schedules
        </a>
    </div>

    <x-card>
        <div class="space-y-4">
            <div>
                <h2 class="text-base font-semibold dark:text-white">Create Custom Section</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Create a single custom schedule for irregular,
                    petition, NSTP, or other non-standard sections.</p>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                <div class="md:col-span-2 lg:col-span-3">
                    <x-select.styled label="Subject" wire:model="form.subject_id" :options="$this->subjectOptions"
                        select="label:label|value:value" searchable />
                </div>

                <x-input label="Program Code" wire:model="form.program_code" placeholder="e.g. BSIT" />
                <x-input label="Year Level (optional)" type="number" wire:model="form.year_level" min="1"
                    max="8" />
                <x-input label="Section Identifier" wire:model="form.section_identifier"
                    placeholder="e.g. A, IRC, PET1" />

                <x-select.styled label="Section Type" wire:model="form.section_type" :options="$this->sectionTypeOptions"
                    select="label:label|value:value" />
                <x-select.styled label="Semester" wire:model="form.semester" :options="$this->semesterOptions"
                    select="label:label|value:value" />
                <x-input label="School Year (YYYY-YYYY)" wire:model="form.school_year" />
                <x-input label="Slots" type="number" wire:model="form.slots" min="1" max="500" />

                @if ($form->section_type === 'NSTP')
                    <x-select.styled label="NSTP Track" wire:model="form.nstp_track" :options="$this->nstpTrackOptions"
                        select="label:label|value:value" />
                @endif
            </div>

            <div class="flex justify-end">
                <x-button color="primary" text="Create Section" wire:click="createCustom" />
            </div>
        </div>
    </x-card>
</div>
