<div class="space-y-4">
    <div class="grid gap-4 md:grid-cols-2">
        <x-input label="First Name" wire:model="form.first_name" required />
        <x-input label="Middle Name" wire:model="form.middle_name" />
        <x-input label="Last Name" wire:model="form.last_name" required />
        <x-input label="Email" wire:model="form.email" type="email" required />
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <!-- Account Type triggers visibility of other sections, keep .live -->
        <x-select.styled label="Account Type" wire:model.live="form.type" :options="$this->typeOptions"
            select="label:label|value:value" required />
        <x-toggle label="Active account" wire:model="form.is_active" />
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <!-- FIX: Removed .live. Roles/Permissions don't need to talk to the server until Save -->
        <div wire:key="wrapper-roles">
            <x-select.styled label="Roles" wire:model="form.roles" :options="$roles" select="label:label|value:value"
                multiple searchable required />
        </div>
        <div wire:key="wrapper-permissions">
            <x-select.styled label="Direct Permissions" wire:model="form.direct_permissions" :options="$permissions"
                select="label:label|value:value" multiple searchable />
        </div>
    </div>

    @if ($form->requiresAssignment())
        <!-- FIX: Added relative z-10 to prevent assignment dropdowns from blocking faculty sections below -->
        <div class="border-l-4 border-primary-200 bg-primary-50 p-4 dark:border-primary-900 dark:bg-primary-950 relative z-20"
            wire:key="section-academic-assignment">

            <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-white">Academic Assignment</h3>
            <div class="grid gap-4 md:grid-cols-3">

                <!-- Campus -->
                <x-select.styled label="Campus" wire:model.live="form.campus_id" :options="$this->campuses"
                    select="label:label|value:value" required />

                <!-- College: Keyed to Campus -->
                <div wire:key="wrapper-college-{{ $form->campus_id ?? 'none' }}">
                    <x-select.styled label="College" wire:model.live="form.college_id" :options="$colleges"
                        select="label:label|value:value" required :disabled="empty($form->campus_id)" invalidate />
                </div>

                <!-- Department: Uses .native (Safe from the bug), but keyed for consistency -->
                <div wire:key="wrapper-dept-{{ $form->college_id ?? 'none' }}">
                    <x-select.styled label="Department" wire:model.live="form.department_id" :options="$departments"
                        select="label:label|value:value" :required="$form->requiresFacultyProfile()" :disabled="empty($form->college_id)" />
                </div>
            </div>
        </div>
    @endif

    @if ($form->requiresFacultyProfile())
        <div class="border-l-4 border-amber-200 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-950 relative z-10"
            wire:key="section-faculty-details">
            <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-white">Faculty Details</h3>
            <div class="grid gap-4 md:grid-cols-2">
                <x-input label="Academic Rank" wire:model="form.academic_rank" />
                <x-input label="Contact Number" wire:model="form.contactno" />

                <!-- Sex: Simple select, no .live needed -->
                <div wire:key="wrapper-sex">
                    <x-select.styled label="Sex" wire:model="form.sex" :options="[['label' => 'Male', 'value' => 'Male'], ['label' => 'Female', 'value' => 'Female']]"
                        select="label:label|value:value" />
                </div>

                <x-input label="Birthday" wire:model="form.birthday" type="date" />
            </div>
            <x-textarea label="Address" wire:model="form.address" class="mt-4" />
        </div>
    @endif

    @if ($form->requiresEmployeeProfile())
        <div class="border-l-4 border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-950"
            wire:key="section-employee-details">
            <h3 class="mb-3 text-sm font-semibold text-zinc-900 dark:text-white">Employee Details</h3>
            <x-input label="Position" wire:model="form.position" required />
        </div>
    @endif
</div>
