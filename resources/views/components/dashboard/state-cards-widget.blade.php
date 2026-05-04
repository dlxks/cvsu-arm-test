@if ($this->hasCards)
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($this->stateCards as $state)
            <x-card>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $state['label'] }}</p>
                <p class="mt-1 text-2xl font-bold text-{{ $state['tone'] }}-600">{{ $state['value'] }}</p>
            </x-card>
        @endforeach
    </div>
@endif
