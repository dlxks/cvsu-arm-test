<?php

namespace App\Traits;

use App\Services\ReferenceDataService;

/**
 * Provides cascading Campus → College → Department select watchers.
 *
 * Host class must declare:
 *   public array $colleges = [];
 *   public array $departments = [];
 *
 * Host class must have a Livewire Form Object property named $form
 * that exposes campus_id, college_id, and department_id.
 */
trait HasCascadingLocationSelects
{
    protected function loadCollegesForCampus(mixed $campusId): array
    {
        $campusId = $this->normalizeAcademicSelectId($campusId);

        if (! filled($campusId)) {
            return [];
        }

        return app(ReferenceDataService::class)->collegesForCampus($campusId);
    }

    protected function loadDepartmentsForCollege(mixed $collegeId): array
    {
        $collegeId = $this->normalizeAcademicSelectId($collegeId);

        if (! filled($collegeId)) {
            return [];
        }

        return app(ReferenceDataService::class)->departmentsForCollege($collegeId);
    }

    public function updatedFormCampusId($value): void
    {
        $campusId = $this->normalizeAcademicSelectId($value);

        $this->form->campus_id = $campusId;
        $this->colleges = $this->loadCollegesForCampus($campusId);
        $this->departments = [];
        $this->form->college_id = null;
        $this->form->department_id = null;
    }

    public function updatedFormCollegeId($value): void
    {
        $collegeId = $this->normalizeAcademicSelectId($value);

        $this->form->college_id = $collegeId;
        $this->departments = $this->loadDepartmentsForCollege($collegeId);
        $this->form->department_id = null;
    }

    protected function refreshAssignmentOptions(): void
    {
        $this->colleges = $this->loadCollegesForCampus($this->form->campus_id);
        $this->departments = $this->loadDepartmentsForCollege($this->form->college_id);
    }

    protected function normalizeAcademicSelectId(mixed $value): ?int
    {
        // TallStack/Livewire can emit arrays or value-wrapper payloads for selects.
        if (is_array($value)) {
            $value = $value['value'] ?? null;
        }

        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value > 0 ? $value : null;
        }

        if (is_string($value)) {
            $value = trim($value);

            if ($value === '' || ! ctype_digit($value)) {
                return null;
            }

            $value = (int) $value;

            return $value > 0 ? $value : null;
        }

        if (is_numeric($value)) {
            $value = (int) $value;

            return $value > 0 ? $value : null;
        }

        return null;
    }
}

