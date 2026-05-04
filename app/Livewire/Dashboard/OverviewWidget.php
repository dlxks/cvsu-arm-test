<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OverviewWidget extends Component
{
    #[Computed]
    public function contextLine(): string
    {
        $profile = Auth::user()?->assignedAcademicProfile();

        if (! $profile) {
            return 'Global access scope';
        }

        return collect([
            $profile->campus?->name,
            $profile->college?->name,
            $profile->department?->name,
        ])->filter()->implode(' | ');
    }

    #[Computed]
    public function permissionCount(): int
    {
        return Auth::user()?->getAllPermissions()->count() ?? 0;
    }

    /**
     * @return array<int, string>
     */
    #[Computed]
    public function roleNames(): array
    {
        return Auth::user()?->roles->pluck('name')->values()->all() ?? [];
    }

    public function render()
    {
        return view('components.dashboard.overview-widget');
    }
}
