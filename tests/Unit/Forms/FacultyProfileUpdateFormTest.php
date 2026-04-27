<?php

use App\Livewire\Forms\Admin\FacultyProfileUpdateForm;
use App\Models\Campus;
use App\Models\College;
use App\Models\Department;
use App\Models\FacultyProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Component;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('FacultyProfileUpdateForm', function () {
    it('accepts assignment changes when selected department matches selected campus and college', function () {
        $profile = FacultyProfile::factory()->create();
        $otherCampus = Campus::factory()->create();
        $otherCollege = College::factory()->forCampus($otherCampus)->create();
        $otherDepartment = Department::factory()->forCollege($otherCollege)->create();

        $component = new class extends Component
        {
            public function render()
            {
                return null;
            }
        };

        $form = new FacultyProfileUpdateForm($component, 'form');
        $form->setValues($profile);
        $form->campus_id = $otherCampus->id;
        $form->college_id = $otherCollege->id;
        $form->department_id = $otherDepartment->id;

        $validated = $form->validateForm();

        expect($validated['campus_id'])->toBe($otherCampus->id)
            ->and($validated['college_id'])->toBe($otherCollege->id)
            ->and($validated['department_id'])->toBe($otherDepartment->id);
    });
});
