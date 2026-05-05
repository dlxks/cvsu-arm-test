<?php

use App\Livewire\Forms\DeptAdmin\PlotScheduleForm;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\ScheduleCategory;
use App\Models\ScheduleRoomTime;
use App\Models\User;
use App\Services\ScheduleConflictService;
use App\Services\SchedulePlottingService;
use App\Traits\CanManage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use CanManage, Interactions;

    public PlotScheduleForm $form;

    public int $campusId;

    public int $collegeId;

    public ?int $departmentId = null;

    public string $campusName = '-';

    public string $collegeName = '-';

    public string $departmentName = '-';

    public bool $facultyConflict = false;

    public bool $roomConflict = false;

    public function mount(): void
    {
        $this->ensureCanManage('schedules.assign');

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

        $requestedScheduleId = request()->integer('schedule');

        if ($requestedScheduleId > 0) {
            $this->form->schedule_id = $requestedScheduleId;
        }
    }

    #[Computed]
    public function scheduleOptions(): array
    {
        return Schedule::query()
            ->with(['sections:id,schedule_id,computed_section_name', 'subject:id,code'])
            ->where(function ($query) {
                $query->where(function ($localScope) {
                    $localScope
                        ->where('campus_id', $this->campusId)
                        ->where('college_id', $this->collegeId)
                        ->when($this->departmentId !== null, fn($departmentScope) => $departmentScope->where('department_id', $this->departmentId));
                });

                if ($this->departmentId !== null) {
                    $query->orWhereHas('serviceRequests', function ($requestQuery) {
                        $requestQuery->where('assigned_department_id', $this->departmentId)->whereIn('schedule_service_requests.status', ['assigned_to_dept', 'dept_submitted']);
                    });
                }
            })
            ->whereIn('status', ['draft', 'pending_plotting'])
            ->orderBy('sched_code')
            ->get()
            ->map(
                fn($s) => [
                    'label' => $s->sched_code . ' — ' . ($s->subject?->code ?? '?') . ' / ' . ($s->sections->first()?->computed_section_name ?? '—'),
                    'value' => $s->id,
                ],
            )
            ->values()
            ->toArray();
    }

    #[Computed]
    public function scheduleCategoryOptions(): array
    {
        return ScheduleCategory::query()
            ->where(function ($query) {
                $query->where('is_active', true);

                if (filled($this->form->schedule_category_id)) {
                    $query->orWhere('id', $this->form->schedule_category_id);
                }
            })
            ->orderBy('name', 'asc')
            ->get(['id', 'name'])
            ->map(fn(ScheduleCategory $category) => ['label' => $category->name, 'value' => (int) $category->id])
            ->values()
            ->toArray();
    }

    #[Computed]
    public function dayOptions(): array
    {
        return collect(ScheduleRoomTime::DAYS)->map(fn($d) => ['label' => $d, 'value' => $d])->values()->toArray();
    }

    #[Computed]
    public function facultyOptions(): array
    {
        return User::query()
            ->whereHas('facultyProfile', function ($q) {
                $q->where('campus_id', $this->campusId)
                    ->where('college_id', $this->collegeId)
                    ->when($this->departmentId !== null, fn($q2) => $q2->where('department_id', $this->departmentId));
            })
            ->orderBy('name', 'asc')
            ->get(['id', 'name'])
            ->map(fn($u) => ['label' => $u->name, 'value' => $u->id])
            ->values()
            ->toArray();
    }

    #[Computed]
    public function roomOptions(): array
    {
        return Room::query()
            ->where('campus_id', $this->campusId)
            ->orderBy('name', 'asc')
            ->get(['id', 'name'])
            ->map(
                fn($r) => [
                    'label' => $r->name,
                    'value' => $r->id,
                ],
            )
            ->values()
            ->toArray();
    }

    #[Computed]
    public function plottedSchedules()
    {
        return Schedule::query()
            ->with(['subject:id,code,title', 'sections:id,schedule_id,computed_section_name', 'roomTimes:id,schedule_id,schedule_category_id,day,time_in,time_out,room_id', 'roomTimes.scheduleCategory:id,name', 'roomTimes.room:id,name', 'facultyAssignments.user:id,name'])
            ->where('campus_id', $this->campusId)
            ->where('college_id', $this->collegeId)
            ->when($this->departmentId !== null, fn($q) => $q->where('department_id', $this->departmentId))
            ->where('status', 'plotted')
            ->latest()
            ->limit(30)
            ->get();
    }

    public function updated(string $property): void
    {
        if (collect(['form.schedule_category_id', 'form.day', 'form.time_in', 'form.time_out', 'form.faculty_id', 'form.room_id', 'form.schedule_id'])->contains($property)) {
            $this->checkConflicts();
        }
    }

    private function checkConflicts(): void
    {
        $this->facultyConflict = false;
        $this->roomConflict = false;

        if (blank($this->form->day) || blank($this->form->time_in) || blank($this->form->time_out)) {
            return;
        }

        $conflict = app(ScheduleConflictService::class);

        if ($this->form->faculty_id) {
            $this->facultyConflict = $conflict->hasFacultyConflict($this->form->faculty_id, $this->form->day, $this->form->time_in, $this->form->time_out, $this->form->schedule_category_id, $this->form->schedule_id);
        }

        if ($this->form->room_id) {
            $this->roomConflict = $conflict->hasRoomConflict($this->form->room_id, $this->form->day, $this->form->time_in, $this->form->time_out, $this->form->schedule_id);
        }
    }

    public function plot(): void
    {
        $this->ensureCanManage('schedules.assign');

        $validated = $this->form->validateForm();

        try {
            app(SchedulePlottingService::class)->plot((int) $validated['schedule_id'], $this->form->payload($validated));
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                $mappedKey = match ($key) {
                    'schedule_category_id' => 'form.schedule_category_id',
                    'day' => 'form.day',
                    'time_in' => 'form.time_in',
                    'time_out' => 'form.time_out',
                    'user_id' => 'form.faculty_id',
                    'room_id' => 'form.room_id',
                    default => 'form.' . $key,
                };

                $this->addError($mappedKey, $messages[0]);
            }

            return;
        }

        $this->form->resetAfterSubmit();
        $this->facultyConflict = false;
        $this->roomConflict = false;
        $this->toast()->success('Plotted', 'Schedule has been plotted.')->send();
    }
};
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold dark:text-white">Plot Schedule</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                {{ $campusName }} | {{ $collegeName }} | {{ $departmentName }}
            </p>
        </div>
        <a href="{{ route('schedules.service-requests') }}"
            class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">
            <x-icon name="arrow-left" class="h-4 w-4" /> Back to Schedule Assignments
        </a>
    </div>

    <x-card>
        <div class="space-y-4">
            <h2 class="text-base font-semibold dark:text-white">Assign Faculty &amp; Room</h2>

            @if ($facultyConflict)
                <div
                    class="rounded-md border border-red-300 bg-red-50 px-4 py-2 text-sm text-red-800 dark:border-red-700 dark:bg-red-900/30 dark:text-red-300">
                    <strong>Conflict:</strong> The selected faculty already has a class during this time block.
                </div>
            @endif

            @if ($roomConflict)
                <div
                    class="rounded-md border border-amber-300 bg-amber-50 px-4 py-2 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                    <strong>Conflict:</strong> The selected room is occupied during this time block.
                </div>
            @endif

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                <div class="md:col-span-2 lg:col-span-3">
                    <x-select.styled label="Schedule" wire:model="form.schedule_id" :options="$this->scheduleOptions"
                        select="label:label|value:value" searchable />
                </div>

                <x-select.styled label="Schedule Category" wire:model.live="form.schedule_category_id" :options="$this->scheduleCategoryOptions"
                    select="label:label|value:value" />
                <x-select.styled label="Day" wire:model.live="form.day" :options="$this->dayOptions"
                    select="label:label|value:value" />
                <x-input label="Time In" type="time" wire:model.live="form.time_in" />
                <x-input label="Time Out" type="time" wire:model.live="form.time_out" />

                <x-select.styled label="Faculty" wire:model.live="form.faculty_id" :options="$this->facultyOptions"
                    select="label:label|value:value" searchable />
                <x-select.styled label="Room" wire:model.live="form.room_id" :options="$this->roomOptions"
                    select="label:label|value:value" searchable />
            </div>

            <div class="flex justify-end">
                <x-button color="primary" text="Plot Schedule" wire:click="plot" sm />
            </div>
        </div>
    </x-card>

    <x-card>
        <div class="space-y-3">
            <h2 class="text-base font-semibold dark:text-white">Recently Plotted</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700 text-sm">
                    <thead>
                        <tr
                            class="text-left text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                            <th class="px-3 py-2">Code</th>
                            <th class="px-3 py-2">Subject</th>
                            <th class="px-3 py-2">Section</th>
                            <th class="px-3 py-2">Schedule Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse ($this->plottedSchedules as $schedule)
                            <tr>
                                <td class="px-3 py-2 font-mono font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $schedule->sched_code }}</td>
                                <td class="px-3 py-2 text-zinc-600 dark:text-zinc-300">{{ $schedule->subject?->code }} –
                                    {{ $schedule->subject?->title }}</td>
                                <td class="px-3 py-2 text-zinc-600 dark:text-zinc-300">
                                    {{ $schedule->sections->first()?->computed_section_name ?? '—' }}</td>
                                <td class="px-3 py-2 text-zinc-500 dark:text-zinc-400">
                                    @foreach ($schedule->roomTimes as $rt)
                                        <div>{{ $rt->scheduleCategory?->name ?? '—' }} | {{ $rt->day }}
                                            {{ substr($rt->time_in, 0, 5) }}–{{ substr($rt->time_out, 0, 5) }} |
                                            {{ $rt->room?->name ?? '—' }}</div>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-zinc-400">No plotted schedules yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-card>
</div>
