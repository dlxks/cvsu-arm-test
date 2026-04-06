<?php

use Livewire\Component;

new class extends Component {
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
        class="w-full max-w-sm px-8 py-6 bg-white rounded-xl shadow-lg border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
        <div class="space-y-4 flex flex-col items-center justify-center">
            @if ($errors->any())
                <div
                    class="bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 text-red-800 dark:text-red-200 p-4 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium">Authentication failed</h3>
                            <ul class="mt-2 list-disc list-inside space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <p class="text-sm text-zinc-500 text-center">Faculty & Admin Use Only</p>

            <x-button href="{{ route('google.redirect') }}" sm outline color="primary"
                class="px-4 bg-zinc-100 font-semibold text-md w-full">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M23.06 12.25C23.06 11.47 22.99 10.72 22.86 10H12.5V14.26H18.42C18.16 15.63 17.38 16.79 16.21 17.57V20.34H19.78C21.86 18.42 23.06 15.6 23.06 12.25Z"
                        fill="#4285F4" />
                    <path
                        d="M12.4997 23C15.4697 23 17.9597 22.02 19.7797 20.34L16.2097 17.57C15.2297 18.23 13.9797 18.63 12.4997 18.63C9.63969 18.63 7.20969 16.7 6.33969 14.1H2.67969V16.94C4.48969 20.53 8.19969 23 12.4997 23Z"
                        fill="#34A853" />
                    <path
                        d="M6.34 14.0899C6.12 13.4299 5.99 12.7299 5.99 11.9999C5.99 11.2699 6.12 10.5699 6.34 9.90995V7.06995H2.68C1.93 8.54995 1.5 10.2199 1.5 11.9999C1.5 13.7799 1.93 15.4499 2.68 16.9299L5.53 14.7099L6.34 14.0899Z"
                        fill="#FBBC05" />
                    <path
                        d="M12.4997 5.38C14.1197 5.38 15.5597 5.94 16.7097 7.02L19.8597 3.87C17.9497 2.09 15.4697 1 12.4997 1C8.19969 1 4.48969 3.47 2.67969 7.07L6.33969 9.91C7.20969 7.31 9.63969 5.38 12.4997 5.38Z"
                        fill="#EA4335" />
                </svg>
                Sign in with Google
            </x-button>

            <p class="text-xs text-center text-zinc-400">
                Restricted to <span class="font-mono text-zinc-600 dark:text-zinc-300">@cvsu.edu.ph</span> accounts.
            </p>
        </div>

    </div>
</x-layouts.guest>
