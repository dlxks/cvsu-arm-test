<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ScheduleChartWidget extends Component
{
    /**
     * @return array<int, array{label: string, value: int, percent: int, tone: string}>
     */
    #[Computed]
    public function chartRows(): array
    {
        $user = Auth::user();

        if (! $user?->can('schedules.view') && ! $user?->can('schedules.assign')) {
            return [];
        }

        $query = Schedule::query();
        $this->applyScheduleScope($query);

        $draft = (clone $query)->where('status', 'draft')->count();
        $pending = (clone $query)->where('status', 'pending_plotting')->count();
        $plotted = (clone $query)->where('status', 'plotted')->count();
        $published = (clone $query)->where('status', 'published')->count();

        $total = max(1, $draft + $pending + $plotted + $published);

        return [
            ['label' => 'Draft', 'value' => $draft, 'percent' => (int) round(($draft / $total) * 100), 'tone' => 'bg-zinc-500'],
            ['label' => 'Pending Plotting', 'value' => $pending, 'percent' => (int) round(($pending / $total) * 100), 'tone' => 'bg-amber-500'],
            ['label' => 'Plotted', 'value' => $plotted, 'percent' => (int) round(($plotted / $total) * 100), 'tone' => 'bg-emerald-500'],
            ['label' => 'Published', 'value' => $published, 'percent' => (int) round(($published / $total) * 100), 'tone' => 'bg-blue-500'],
        ];
    }

    #[Computed]
    public function totalSchedules(): int
    {
        return collect($this->chartRows)->sum('value');
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
        return view('components.dashboard.schedule-chart-widget');
    }
}
