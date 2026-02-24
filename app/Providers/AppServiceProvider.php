<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use TallStackUi\Facades\TallStackUi;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // SIDEBAR STYLE CONFIGURATION
        TallStackUi::personalize('sidebar.item')
            ->block('item.state.current', 'group bg-emerald-500/10 dark:bg-emerald-400/10 hover:bg-emerald-500/20 transition-all')
            ->block('item.state.normal', 'group hover:bg-zinc-200/50 dark:hover:bg-zinc-700/30 transition-all')
            ->block('item.icon', function (array $data) {
                return $data['current']
                    ? 'w-5 text-emerald-700 dark:text-emerald-400'
                    : 'w-5 text-zinc-500 dark:text-zinc-400 group-hover:text-zinc-700 dark:group-hover:text-zinc-200';
            })
            ->block('item.text', function (array $data) {
                return $data['current']
                    ? 'text-sm font-bold text-emerald-800 dark:text-emerald-400'
                    : 'text-sm font-medium text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-800 dark:group-hover:text-zinc-200';
            });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
