<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\Schedule;
use App\Models\ScheduleFaculty;
use App\Models\ScheduleServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StateCardsWidget extends Component
{
    /**
     * @return array<int, array{label: string, value: int, tone: string}>
     */
    #[Computed]
    public function stateCards(): array
    {
        $cards = [];
        /** @var User|null $user */
        $user = Auth::user();

        if ($user?->can('schedules.view') || $user?->can('schedules.assign')) {
            $scheduleQuery = Schedule::query();
            $this->applyScheduleScope($scheduleQuery);

            $cards[] = ['label' => 'Draft Schedules', 'value' => (clone $scheduleQuery)->where('status', 'draft')->count(), 'tone' => 'zinc'];
            $cards[] = ['label' => 'Pending Plotting', 'value' => (clone $scheduleQuery)->where('status', 'pending_plotting')->count(), 'tone' => 'amber'];
            $cards[] = ['label' => 'Plotted', 'value' => (clone $scheduleQuery)->where('status', 'plotted')->count(), 'tone' => 'emerald'];
            $cards[] = ['label' => 'Published', 'value' => (clone $scheduleQuery)->where('status', 'published')->count(), 'tone' => 'blue'];
        }

        if ($user?->can('schedule_requests.view')) {
            $serviceRequestQuery = ScheduleServiceRequest::query();
            $profile = $user->assignedAcademicProfile();

            if (filled($profile?->college_id)) {
                $serviceRequestQuery->where(function (Builder $query) use ($profile): void {
                    $query->where('servicing_college_id', (int) $profile->college_id)
                        ->orWhere('requesting_college_id', (int) $profile->college_id);
                });
            }

            $cards[] = ['label' => 'Pending Requests', 'value' => (clone $serviceRequestQuery)->where('status', 'pending')->count(), 'tone' => 'amber'];
            $cards[] = ['label' => 'Assigned to Dept', 'value' => (clone $serviceRequestQuery)->where('status', 'assigned_to_dept')->count(), 'tone' => 'violet'];
            $cards[] = ['label' => 'Dept Submitted', 'value' => (clone $serviceRequestQuery)->where('status', 'dept_submitted')->count(), 'tone' => 'indigo'];
            $cards[] = ['label' => 'Completed', 'value' => (clone $serviceRequestQuery)->where('status', 'completed')->count(), 'tone' => 'emerald'];
        }

        if ($user?->can('faculty_schedules.view')) {
            $myAssignments = ScheduleFaculty::query()->where('user_id', $user->id);

            $cards[] = [
                'label' => 'My Plotted Loads',
                'value' => (clone $myAssignments)
                    ->whereHas('schedule', fn (Builder $query): Builder => $query->where('status', 'plotted'))
                    ->distinct('schedule_id')
                    ->count('schedule_id'),
                'tone' => 'emerald',
            ];

            $cards[] = [
                'label' => 'My Published Loads',
                'value' => (clone $myAssignments)
                    ->whereHas('schedule', fn (Builder $query): Builder => $query->where('status', 'published'))
                    ->distinct('schedule_id')
                    ->count('schedule_id'),
                'tone' => 'blue',
            ];
        }

        return $cards;
    }

    #[Computed]
    public function hasCards(): bool
    {
        return $this->stateCards !== [];
    }

    private function applyScheduleScope(Builder $query): void
    {
        $profile = Auth::user()?->assignedAcademicProfile();

        if (filled($profile?->campus_id)) {
            $query->where('campus_id', (int) $profile->campus_id);
        }

        if (filled($profile?->college_id)) {
            $query->where('college_id', (int) $profile->college_id);
        }

        if (filled($profile?->department_id)) {
            $query->where('department_id', (int) $profile->department_id);
        }

        $query->whereNull('deleted_at');
    }

    public function render()
    {
        return view('components.dashboard.state-cards-widget');
    }
}
