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
            Main dashboard content goes here. You can create Livewire components and include them here as needed.
        </div>
    </div>