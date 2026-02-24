<?php

use Livewire\Component;

new class extends Component
{
    //
    public bool $tallstackui = false;
};
?>

<div class="max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h1 class="text-lg font-medium">Branches/Colleges Management</h1>

        <div class="flex gap-2">
            {{-- <flux:modal.trigger name="import-branches-modal">
                <flux:button variant="outline" size="sm" icon="document-arrow-up">Import Data</flux:button>
            </flux:modal.trigger>

            <flux:button variant="primary" size="sm" icon="plus" wire:click="create">New Branch</flux:button> --}}


            <x-modal title="TallStackUi" wire="tallstackui">
                TallStackUi
            </x-modal>

            <x-button wire:click="$toggle('tallstackui')" sm color="primary" icon="plus" text="New Branch" />
        </div>
    </div>
    <livewire:admin.branches-table />
</div>