<?php
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.app')] class extends Component
{
    // Your logic here
};
?>

{{-- Everything MUST be inside this one single <div> --}}
    <div>
        <h1 class="text-xl font-bold dark:text-white">Admin Dashboard</h1>

        <div class="mt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ts-button type="submit" flat color="red" class="w-full !justify-start">
                    Log Out
                </x-ts-button>
            </form>
        </div>
    </div>