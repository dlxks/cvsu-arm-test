<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public function mount(): void
    {
        $user = Auth::user();

        abort_unless($user, 403);
    }

    #[Computed]
    public function hasDataWidgets(): bool
    {
        $user = Auth::user();

        return (bool) ($user?->can('schedules.view')
            || $user?->can('schedules.assign')
            || $user?->can('schedule_requests.view')
            || $user?->can('faculty_schedules.view'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="p-4 text-sm text-zinc-500">Loading dashboard...</div>
        HTML;
    }
};
?>

<div class="space-y-6">
    <livewire:dashboard.overview-widget />

    <livewire:dashboard.state-cards-widget />

    <livewire:dashboard.schedule-chart-widget />

    @if (! $this->hasDataWidgets)
        <x-card>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">No dashboard widgets are currently available for your
                account. Please contact an administrator to assign access.</p>
        </x-card>
    @endif
</div>
