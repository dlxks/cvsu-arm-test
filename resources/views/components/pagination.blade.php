<div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full py-2 px-4"
    wire:loading.class="blur-[2px]" wire:target="loadMore">

    {{-- PER-PAGE BLOCK --}}
    @php
    $perPageValues = data_get($this->setUp, 'footer.perPageValues', []);
    @endphp

    @if(count($perPageValues) > 1)
    <div class="flex items-center gap-2">
        <label for="perPage" class="text-sm font-medium text-zinc-700 dark:text-zinc-300 whitespace-nowrap">
            Per page
        </label>

        <div class="relative">
            <div class="w-24">
                <x-select.native id="perPage" wire:model.live="setUp.footer.perPage" class="rounded-xl">
                    @foreach ($perPageValues as $value)
                    <option value="{{ $value }}">
                        {{ $value == 0 ? trans('livewire-powergrid::datatable.labels.all') : $value }}
                    </option>
                    @endforeach
                </x-select.native>
            </div>

        </div>
    </div>
    @endif
    {{-- END PER-PAGE BLOCK --}}


    {{-- PAGINATION LINKS BLOCK --}}
    @if($paginator->count() > 0)
    <div
        class="flex flex-col sm:flex-row items-center justify-between w-full sm:flex-1 gap-4 overflow-x-auto pb-1 sm:pb-0">

        {{-- Record Count Display --}}
        @if ($recordCount === 'full')
        <div class="whitespace-nowrap leading-5 text-sm text-zinc-600 dark:text-zinc-400 text-center sm:text-right">
            {{ trans('livewire-powergrid::datatable.pagination.showing') }}
            <span class="font-semibold text-zinc-900 dark:text-white firstItem">{{ $paginator->firstItem() }}</span>
            {{ trans('livewire-powergrid::datatable.pagination.to') }}
            <span class="font-semibold text-zinc-900 dark:text-white lastItem">{{ $paginator->lastItem() }}</span>
            {{ trans('livewire-powergrid::datatable.pagination.of') }}
            <span class="font-semibold text-zinc-900 dark:text-white total">{{ $paginator->total() }}</span>
            {{ trans('livewire-powergrid::datatable.pagination.results') }}
        </div>
        @elseif($recordCount === 'short')
        <div class="whitespace-nowrap leading-5 text-sm text-zinc-600 dark:text-zinc-400 text-center sm:text-right">
            <span class="font-semibold text-zinc-900 dark:text-white firstItem">{{ $paginator->firstItem() }}</span>
            -
            <span class="font-semibold text-zinc-900 dark:text-white lastItem">{{ $paginator->lastItem() }}</span>
            |
            <span class="font-semibold text-zinc-900 dark:text-white total">{{ $paginator->total() }}</span>
        </div>
        @elseif($recordCount === 'min')
        <div class="whitespace-nowrap leading-5 text-sm text-zinc-600 dark:text-zinc-400 text-center sm:text-right">
            <span class="font-semibold text-zinc-900 dark:text-white firstItem">{{ $paginator->firstItem() }}</span>
            -
            <span class="font-semibold text-zinc-900 dark:text-white lastItem">{{ $paginator->lastItem() }}</span>
        </div>
        @endif

        {{-- Full Pagination (Numbered Links) --}}
        @if ($paginator->hasPages() && !in_array($recordCount, ['min', 'short']))
        <nav role="navigation" aria-label="Pagination Navigation" class="flex-shrink-0">
            <div class="flex justify-center rounded-xl ">

                @if (!$paginator->onFirstPage())
                <a class="cursor-pointer relative inline-flex items-center px-2 py-2 text-sm font-medium text-zinc-500 dark:text-zinc-400 bg-white dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-l-xl leading-5 hover:bg-zinc-50 hover:text-zinc-900 dark:hover:bg-white/10 dark:hover:text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-white transition"
                    wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                    </svg>
                </a>

                <a class="cursor-pointer relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-zinc-500 dark:text-zinc-400 bg-white dark:bg-white/5 border border-zinc-200 dark:border-white/10 leading-5 hover:bg-zinc-50 hover:text-zinc-900 dark:hover:bg-white/10 dark:hover:text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-white transition"
                    wire:click="previousPage('{{ $paginator->getPageName() }}')" rel="next">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </a>
                @endif

                @foreach ($elements as $element)
                @if (is_array($element))
                @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                <span
                    class="select-none relative z-10 inline-flex items-center px-4 py-2 -ml-px text-sm font-semibold text-zinc-900 dark:text-white bg-zinc-100 dark:bg-white/10 border border-zinc-200 dark:border-white/10 cursor-default">
                    {{ $page }}
                </span>
                @elseif ($page === $paginator->currentPage() + 1 || $page === $paginator->currentPage() + 2 || $page ===
                $paginator->currentPage() - 1 || $page === $paginator->currentPage() - 2)
                <a class="select-none cursor-pointer relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-zinc-600 dark:text-zinc-400 bg-white dark:bg-white/5 border border-zinc-200 dark:border-white/10 leading-5 hover:bg-zinc-50 hover:text-zinc-900 dark:hover:bg-white/10 dark:hover:text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-white transition"
                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')">
                    {{ $page }}
                </a>
                @endif
                @endforeach
                @endif
                @endforeach

                @if ($paginator->hasMorePages())
                <a @class([ 'block'=> $paginator->lastPage() - $paginator->currentPage() >= 2,
                    'hidden' => $paginator->lastPage() - $paginator->currentPage() <
                        2, 'select-none cursor-pointer relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-zinc-500 dark:text-zinc-400 bg-white dark:bg-white/5 border border-zinc-200 dark:border-white/10 leading-5 hover:bg-zinc-50 hover:text-zinc-900 dark:hover:bg-white/10 dark:hover:text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-white transition'
                        ]) wire:click="nextPage('{{ $paginator->getPageName() }}')" rel="next">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                </a>
                <a class="select-none cursor-pointer relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-zinc-500 dark:text-zinc-400 bg-white dark:bg-white/5 border border-zinc-200 dark:border-white/10 rounded-r-xl leading-5 hover:bg-zinc-50 hover:text-zinc-900 dark:hover:bg-white/10 dark:hover:text-white focus:z-10 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-white transition"
                    wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m5.25 4.5 7.5 7.5-7.5 7.5m6-15 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
                @endif
            </div>
        </nav>
        @endif

        {{-- Simple Pagination (Previous / Next Buttons) --}}
        @if ($paginator->hasPages() && in_array($recordCount, ['min', 'short']))
        <nav role="navigation" aria-label="Pagination Navigation" class="flex-shrink-0">
            <div class="flex justify-center gap-2">
                <span>
                    @if ($paginator->onFirstPage())
                    <button disabled
                        class="inline-flex items-center justify-center gap-x-2 px-4 py-2 text-sm font-medium rounded-xl border border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5 text-zinc-400 dark:text-zinc-500 cursor-not-allowed opacity-80">
                        @lang('Previous')
                    </button>
                    @else
                    @if (method_exists($paginator, 'getCursorName'))
                    <button
                        wire:click="setPage('{{ $paginator->previousCursor()->encode() }}','{{ $paginator->getCursorName() }}')"
                        wire:loading.attr="disabled"
                        class="select-none inline-flex items-center justify-center p-2 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/5 text-zinc-500 dark:text-zinc-400  transition-all hover:bg-zinc-50 dark:hover:bg-white/10 hover:text-zinc-900 dark:hover:text-white focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-white">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m18.75 4.5-7.5 7.5 7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                        </svg>
                    </button>
                    @else
                    <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                        class="select-none group inline-flex items-center justify-center gap-x-2 px-4 py-2 text-sm font-medium rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/5 text-zinc-700 dark:text-zinc-300 transition-all duration-200 ease-in-out hover:bg-zinc-50 dark:hover:bg-white/10 hover:text-zinc-900 dark:hover:text-white focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-white">
                        @lang('Previous')
                    </button>
                    @endif
                    @endif
                </span>

                <span>
                    @if ($paginator->hasMorePages())
                    @if (method_exists($paginator, 'getCursorName'))
                    <button
                        wire:click="setPage('{{ $paginator->nextCursor()->encode() }}','{{ $paginator->getCursorName() }}')"
                        wire:loading.attr="disabled"
                        class="select-none inline-flex items-center justify-center p-2 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/5 text-zinc-500 dark:text-zinc-400  transition-all hover:bg-zinc-50 dark:hover:bg-white/10 hover:text-zinc-900 dark:hover:text-white focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-white">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                    @else
                    <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                        class="select-none group inline-flex items-center justify-center gap-x-2 px-4 py-2 text-sm font-medium rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-white/5 text-zinc-700 dark:text-zinc-300  transition-all duration-200 ease-in-out hover:bg-zinc-50 dark:hover:bg-white/10 hover:text-zinc-900 dark:hover:text-white focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:focus:ring-white">
                        @lang('Next')
                    </button>
                    @endif
                    @else
                    <button disabled
                        class="inline-flex items-center justify-center gap-x-2 px-4 py-2 text-sm font-medium rounded-xl border border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-white/5 text-zinc-400 dark:text-zinc-500 cursor-not-allowed opacity-80">
                        @lang('Next')
                    </button>
                    @endif
                </span>
            </div>
        </nav>
        @endif
    </div>
    @endif
</div>