<div class="flex items-center gap-2">
    <label for="perPage" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
        {{ trans('livewire-powergrid::datatable.labels.per_page') }}
    </label>

    <div class="relative">
        <select id="perPage" wire:model.live="setUp.footer.perPage"
            class="block w-full appearance-none rounded-lg border border-zinc-200 bg-white py-1.5 pl-3 pr-8 text-sm text-zinc-900 shadow-sm transition focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-white dark:focus:ring-white">
            @foreach ($setUp['footer']['perPageValues'] as $value)
            <option value="{{ $value }}">
                @if ($value == 0)
                {{ trans('livewire-powergrid::datatable.labels.all') }}
                @else
                {{ $value }}
                @endif
            </option>
            @endforeach
        </select>

        {{-- Custom Dropdown Arrow for the Flux look --}}
        <div
            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-zinc-500 dark:text-zinc-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
            </svg>
        </div>
    </div>
</div>