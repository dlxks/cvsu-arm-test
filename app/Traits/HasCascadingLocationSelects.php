<?php

namespace App\Traits;

use App\Models\College;
use App\Models\Department;
use Illuminate\Support\Collection;

/**
 * Provides cascading Campus → College → Department select watchers.
 *
 * Host class must declare:
 *   public Collection $colleges;
 *   public Collection $departments;
 *
 * Host class must have a Livewire Form Object property named $form
 * that exposes campus_id, college_id, and department_id.
 */
trait HasCascadingLocationSelects
{
    protected function loadCollegesForCampus(?int $campusId): Collection
    {
        return filled($campusId)
            ? College::where('campus_id', $campusId)->where('is_active', true)->orderBy('name')->get()
            : collect();
    }

    protected function loadDepartmentsForCollege(?int $collegeId): Collection
    {
        return filled($collegeId)
            ? Department::where('college_id', $collegeId)->where('is_active', true)->orderBy('name')->get()
            : collect();
    }

    public function updatedFormCampusId($value): void
    {
        $this->colleges = $this->loadCollegesForCampus($value);
        $this->departments = collect();
        $this->form->college_id = null;
        $this->form->department_id = null;
    }

    public function updatedFormCollegeId($value): void
    {
        $this->departments = $this->loadDepartmentsForCollege($value);
        $this->form->department_id = null;
    }

    protected function refreshAssignmentOptions(): void
    {
        $this->colleges = $this->loadCollegesForCampus($this->form->campus_id);
        $this->departments = $this->loadDepartmentsForCollege($this->form->college_id);
    }
}
