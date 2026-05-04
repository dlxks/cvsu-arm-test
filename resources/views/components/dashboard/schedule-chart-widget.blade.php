@if ($this->chartRows !== [])
    <x-card>
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-2">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Schedule Status</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Total: {{ $this->totalSchedules }}</p>
            </div>

            <div class="space-y-3">
                @foreach ($this->chartRows as $row)
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-zinc-700 dark:text-zinc-200">{{ $row['label'] }}</span>
                            <span class="text-zinc-500 dark:text-zinc-400">{{ $row['value'] }}
                                ({{ $row['percent'] }}%)
                            </span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded bg-zinc-200 dark:bg-zinc-700">
                            <div class="h-full {{ $row['tone'] }}" style="width: {{ $row['percent'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-card>
@endif
