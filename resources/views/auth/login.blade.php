<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<x-layouts.guest>
    <div class="my-4 flex flex-col gap-1 items-center justify-center">
        <!-- CvSU Icon -->
        <img src="{{ asset('images/CvSU-Logo.png') }}" alt="CvSU Icon" class="w-22">

        <!-- App Name -->
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ config('app.name') }}</h1>

    </div>

    <div
        class="w-full max-w-sm p-6 bg-white rounded-xl shadow-lg border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
        <div class="space-y-4 flex flex-col items-center justify-center">
            <p class="text-sm text-zinc-500 text-center">Faculty & Admin Access Only</p>

            <x-ts-button href="{{ route('google.redirect') }}" sm outline color="primary" icon="fab.google"
                class="px-4 bg-zinc-100 font-semibold text-md">
                Sign in with Google
            </x-ts-button>

            <p class="text-xs text-center text-zinc-400">
                Restricted to <span class="font-mono text-zinc-600 dark:text-zinc-300">@cvsu.edu.ph</span> accounts.
            </p>
        </div>

    </div>
</x-layouts.guest>