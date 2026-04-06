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
        TallStackUi::customize('sidebar.item')
            // Active routes use primary tones.
            ->block('item.state.current')
            ->replace([
                'text-primary-500' => 'text-primary-600',
                'dark:text-white' => 'dark:text-primary-400',
            ])

            // Inactive routes use zinc tones.
            ->block('item.state.normal')
            ->replace([
                'text-primary-500' => 'text-zinc-500',
                'dark:text-white' => 'dark:text-zinc-300',
            ])

            // Icons inherit the current route state color.
            ->block('item.icon')
            ->replace([
                'text-primary-500' => 'text-current',
                'dark:text-white' => 'dark:text-current',
            ]);
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
