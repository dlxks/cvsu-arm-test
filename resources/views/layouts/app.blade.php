<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="tallstackui_darkTheme()">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <tallstackui:script />
    @livewireStyles

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="font-sans antialiased" x-cloak x-data="{ name: @js(auth()->user()->name) }"
    x-on:name-updated.window="name = $event.detail.name"
    x-bind:class="{ 'dark bg-gray-800': darkTheme, 'bg-gray-100': !darkTheme }">
    <x-ts-layout>
        <x-slot:top>
            <x-ts-dialog />
            <x-ts-toast />
        </x-slot:top>

        <x-slot:header>
            <x-slot:left>
                <x-ts-theme-switch />
            </x-slot:left>
            <x-slot:right>
                <x-ts-dropdown>
                    <x-slot:action>
                        <div>
                            <button class="cursor-pointer" x-on:click="show = !show">
                                <span class="text-base font-semibold text-primary-500" x-text="name"></span>
                            </button>
                        </div>
                    </x-slot:action>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        {{--
                        <x-ts-dropdown.items :text="__('Profile')" :href="route('user.profile')" /> --}}
                        <x-ts-dropdown.items :text="__('Logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" separator />
                    </form>
                </x-ts-dropdown>
            </x-slot:right>
        </x-slot:header>
    </x-ts-layout>

    {{ $slot }}

    @livewireScripts
</body>

</html>