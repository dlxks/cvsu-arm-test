<?php

use App\Livewire\Tables\CollegeAdmin\SubjectsTable;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

describe('college admin subjects table', function () {
    beforeEach(function () {
        $this->user = actingUserWithPermissions([]);
    });

    it('formats created at values in Philippine time', function () {
        $subject = Subject::factory()->create();

        DB::table('subjects')->where('id', $subject->id)->update([
            'created_at' => '2026-04-23 10:51:16',
        ]);

        $fields = Livewire::actingAs($this->user)
            ->test(SubjectsTable::class)
            ->instance()
            ->fields()
            ->fields;

        expect($fields['created_at_formatted']($subject->fresh()))
            ->toBe($subject->fresh()->created_at->timezone(config('app.timezone'))->format('d/m/Y H:i:s'));
    });
});
