<?php

use App\Livewire\Tables\Admin\ScheduleCategoriesTable;
use App\Models\Campus;
use App\Models\College;
use App\Models\Permission;
use App\Models\Schedule;
use App\Models\ScheduleCategory;
use App\Models\ScheduleFaculty;
use App\Models\ScheduleRoomTime;
use App\Models\Subject;
use App\Models\User;
use Livewire\Livewire;

function scheduleCategoryAbilities(): array
{
    return [
        'schedule_categories.view',
        'schedule_categories.create',
        'schedule_categories.update',
        'schedule_categories.delete',
        'schedule_categories.restore',
    ];
}

describe('admin schedule categories page', function () {
    beforeEach(function () {
        collect(scheduleCategoryAbilities())->each(fn (string $ability) => Permission::findOrCreate($ability, 'web'));
    });

    it('creates and updates schedule categories including deactivation', function () {
        $user = actingUserWithPermissions(scheduleCategoryAbilities());

        Livewire::actingAs($user)
            ->test('pages::admin.schedule-categories.index')
            ->call('openCreateModal')
            ->set('form.name', 'Lecture New')
            ->set('form.slug', 'lecture-new')
            ->set('form.is_active', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('scheduleCategoryModal', false)
            ->assertDispatched('pg:eventRefresh-scheduleCategoriesTable');

        $scheduleCategory = ScheduleCategory::query()->where('slug', 'lecture-new')->first();

        expect($scheduleCategory)->not->toBeNull()
            ->and($scheduleCategory->is_active)->toBeTrue();

        Livewire::actingAs($user)
            ->test('pages::admin.schedule-categories.index')
            ->call('openEditModal', $scheduleCategory->id)
            ->set('form.name', 'Lecture Updated')
            ->set('form.slug', 'lecture-updated')
            ->set('form.is_active', false)
            ->call('save')
            ->assertHasNoErrors();

        expect($scheduleCategory->fresh()->name)->toBe('LECTURE UPDATED')
            ->and($scheduleCategory->fresh()->slug)->toBe('lecture-updated')
            ->and($scheduleCategory->fresh()->is_active)->toBeFalse();
    });

    it('blocks deleting in-use categories and allows delete restore for unused ones', function () {
        $user = actingUserWithPermissions(scheduleCategoryAbilities());

        $campus = Campus::factory()->create();
        $college = College::factory()->forCampus($campus)->create();
        $subject = Subject::factory()->create();
        $schedule = Schedule::query()->create([
            'sched_code' => '260100999',
            'subject_id' => $subject->id,
            'campus_id' => $campus->id,
            'college_id' => $college->id,
            'department_id' => null,
            'semester' => '1ST',
            'school_year' => '2026-2027',
            'slots' => 40,
            'status' => 'draft',
        ]);

        $categoryInUse = ScheduleCategory::factory()->create([
            'name' => 'LECTURE IN USE',
            'slug' => 'lecture-in-use',
        ]);
        $unusedCategory = ScheduleCategory::factory()->create([
            'name' => 'UNUSED CATEGORY',
            'slug' => 'unused-category',
        ]);

        ScheduleRoomTime::query()->create([
            'schedule_id' => $schedule->id,
            'room_id' => null,
            'schedule_category_id' => $categoryInUse->id,
            'day' => 'MON',
            'time_in' => '08:00',
            'time_out' => '09:00',
        ]);

        ScheduleFaculty::query()->create([
            'schedule_id' => $schedule->id,
            'user_id' => null,
            'schedule_category_id' => $categoryInUse->id,
        ]);

        Livewire::actingAs($user)
            ->test(ScheduleCategoriesTable::class)
            ->call('deleteScheduleCategory', $categoryInUse->id);

        expect(ScheduleCategory::query()->find($categoryInUse->id)?->trashed())->toBeFalse();

        Livewire::actingAs($user)
            ->test(ScheduleCategoriesTable::class)
            ->call('deleteScheduleCategory', $unusedCategory->id);

        expect(ScheduleCategory::withTrashed()->find($unusedCategory->id)?->trashed())->toBeTrue();

        Livewire::actingAs($user)
            ->test(ScheduleCategoriesTable::class)
            ->call('restoreScheduleCategory', $unusedCategory->id);

        expect(ScheduleCategory::query()->find($unusedCategory->id)?->trashed())->toBeFalse();
    });

    it('allows only super admins with permission to access the schedule categories route', function () {
        $authorized = User::factory()->superAdmin()->create();
        /** @var User $authorized */
        $authorized->givePermissionTo('schedule_categories.view');

        $unauthorized = User::factory()->create();
        /** @var User $unauthorized */
        $this->actingAs($authorized)
            ->get(route('schedule-categories.index'))
            ->assertOk();

        $this->actingAs($unauthorized)
            ->get(route('schedule-categories.index'))
            ->assertRedirect(route('dashboard'));
    });
});
