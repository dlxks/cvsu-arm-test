<x-card>
    <div class="space-y-3">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Dashboard</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ $this->contextLine !== '' ? $this->contextLine : 'Access context unavailable' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @foreach ($this->roleNames as $role)
                    <x-badge color="slate" :text="strtoupper($role)" round />
                @endforeach
            </div>
        </div>

        <p class="text-xs uppercase tracking-wide text-zinc-400">
            {{ $this->permissionCount }} permission(s) available to this account
        </p>
    </div>
</x-card>
