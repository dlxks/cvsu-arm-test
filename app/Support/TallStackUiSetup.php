<?php

namespace App\Support;

use TallStackUi\Facades\TallStackUi;

class TallStackUiSetup
{
    public static function configureSidebar(): void
    {
        TallStackUi::customize('sidebar.item')
            ->block('item.state.current')
            ->replace([
                'text-primary-500' => 'text-primary-600',
                'dark:text-white' => 'dark:text-primary-400',
            ])

            ->block('item.state.normal')
            ->replace([
                'text-primary-500' => 'text-zinc-500',
                'dark:text-white' => 'dark:text-zinc-300',
            ])

            ->block('item.icon')
            ->replace([
                'text-primary-500' => 'text-current',
                'dark:text-white' => 'dark:text-current',
            ]);

    }
}
