<?php

namespace App\PowerGridThemes;

use PowerComponents\LivewirePowerGrid\Themes\Tailwind;

class Minimalist extends Tailwind
{
    public string $name = 'tailwind';

    public function table(): array
    {
        return [
            'layout' => [
                'base' => 'p-3 align-middle inline-block min-w-full w-full sm:px-4 lg:px-6',
                'div' => 'rounded-t-lg relative dark:bg-pg-primary-700 dark:border-pg-primary-600',
                'table' => 'min-w-full bg-white dark:!bg-primary-800',
                'container' => '-my-2 overflow-x-auto sm:-mx-3 lg:-mx-8',
                'actions' => 'flex gap-2',
            ],

            'header' => [
                'thead' => 'shadow-sm rounded-t-lg ',
                'tr' => '',
                'th' => 'font-extrabold px-3 py-3 text-left text-xs text-pg-primary-700 tracking-wider whitespace-nowrap dark:text-pg-primary-300',
                'thAction' => '!font-bold',
            ],

            'body' => [
                'tbody' => 'text-pg-primary-800',
                'tbodyEmpty' => '',
                'tr' => 'border-b border-pg-primary-100 dark:border-pg-primary-600 hover:bg-pg-primary-50 dark:bg-pg-primary-800 dark:hover:bg-pg-primary-700',
                'td' => 'px-3 py-1 text-sm whitespace-nowrap dark:text-pg-primary-200',
                'tdEmpty' => 'p-2 whitespace-nowrap dark:text-pg-primary-200',
                'tdSummarize' => 'p-2 whitespace-nowrap dark:text-pg-primary-200 text-sm text-pg-primary-600 text-right space-y-2',
                'trSummarize' => '',
                'tdFilters' => '',
                'trFilters' => '',
                'tdActionsContainer' => 'flex gap-2',
            ],
        ];
    }

    public function footer(): array
    {
        return [
            'view' => $this->root().'.footer',
            'select' => 'appearance-none !bg-none block w-auto rounded-xl border border-zinc-200 bg-white text-sm text-zinc-900 transition focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-white dark:focus:ring-white',
            'footer' => 'rounded-b-lg  dark:bg-pg-primary-700',
            'footer_with_pagination' => 'md:flex md:flex-row w-full items-center py-3 bg-white overflow-y-auto pl-2 pr-2 relative dark:bg-pg-primary-900 text-sm',
        ];
    }

    public function cols(): array
    {
        return [
            'div' => 'select-none flex items-center gap-1',
        ];
    }

    public function editable(): array
    {
        return [
            'view' => $this->root().'.editable',
            'input' => 'block w-full rounded-xl border border-zinc-200 bg-white py-1.5 px-3 text-sm text-zinc-900 transition placeholder:text-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white dark:focus:ring-white',
        ];
    }

    public function toggleable(): array
    {
        return [
            'view' => $this->root().'.toggleable',
        ];
    }

    public function checkbox(): array
    {
        return [
            'th' => 'px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider',
            'base' => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'form-checkbox dark:border-dark-600 border-1 dark:bg-dark-800 rounded border-gray-300 bg-white transition duration-100 ease-in-out h-4 w-4 text-primary-500 focus:ring-primary-500 dark:ring-offset-dark-900',
        ];
    }

    public function radio(): array
    {
        return [
            'th' => 'px-6 py-3 text-left text-xs font-medium text-pg-primary-500 tracking-wider',
            'base' => '',
            'label' => 'flex items-center space-x-3',
            'input' => 'form-checkbox h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:checked:bg-white dark:checked:border-white dark:focus:ring-white transition duration-150 ease-in-out',
        ];
    }

    public function filterBoolean(): array
    {
        return [
            'view' => $this->root().'.filters.boolean',
            'base' => 'min-w-[5rem]',
            'select' => 'block w-full rounded-xl border border-zinc-200 bg-white py-1.5 px-3 text-sm text-zinc-900 transition focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-white dark:focus:ring-white',
        ];
    }

    public function filterDatePicker(): array
    {
        return [
            'base' => '',
            'view' => $this->root().'.filters.date-picker',
            'input' => 'flatpickr flatpickr-input block w-full rounded-xl border border-zinc-200 bg-white py-1.5 px-3 text-sm text-zinc-900 transition placeholder:text-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white dark:focus:ring-white',
        ];
    }

    public function filterMultiSelect(): array
    {
        return [
            'view' => $this->root().'.filters.multi-select',
            'base' => 'inline-block relative w-full',
            'select' => 'mt-1',
        ];
    }

    public function filterNumber(): array
    {
        return [
            'view' => $this->root().'.filters.number',
            'input' => 'block w-full min-w-[5rem] rounded-xl border border-zinc-200 bg-white py-1.5 px-3 text-sm text-zinc-900 transition placeholder:text-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white dark:focus:ring-white',
        ];
    }

    public function filterSelect(): array
    {
        return [
            'view' => $this->root().'.filters.select',
            'base' => '',
            'select' => 'block w-full rounded-xl border border-zinc-200 bg-white py-1.5 px-3 text-sm text-zinc-900 transition focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-white dark:focus:ring-white',
        ];
    }

    public function filterInputText(): array
    {
        return [
            'view' => $this->root().'.filters.input-text',
            'base' => 'min-w-[9.5rem]',
            'select' => 'mb-1 block w-full rounded-xl border border-zinc-200 bg-white py-1.5 px-3 text-sm text-zinc-900 transition focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:focus:border-white dark:focus:ring-white',
            'input' => 'block w-full rounded-xl border border-zinc-200 bg-white py-1.5 px-3 text-sm text-zinc-900 transition placeholder:text-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white dark:focus:ring-white',
        ];
    }

    public function searchBox(): array
    {
        return [
            'input' => 'block w-full rounded-xl border border-zinc-200 bg-white py-1.5 pl-10 pr-3 text-sm text-zinc-900 transition placeholder:text-zinc-400 focus:border-zinc-400 focus:outline-none focus:ring-1 focus:ring-zinc-400 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white dark:focus:ring-white',
            'iconClose' => 'text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300 transition cursor-pointer',
            'iconSearch' => 'text-zinc-400 dark:text-zinc-500 w-5 h-5 ml-2',
        ];
    }
}
