<?php

declare(strict_types=1);

namespace App\Livewire\Forms\CollegeAdmin;

use Livewire\Form;

class AssignDepartmentForm extends Form
{
    public ?int $service_request_id = null;

    public ?int $department_id = null;

    public function resetForm(): void
    {
        $this->service_request_id = null;
        $this->department_id = null;
    }

    /**
     * @return array<string, mixed>
     */
    public function validateForm(): array
    {
        return $this->validate($this->rules());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'service_request_id' => ['required', 'integer', 'exists:schedule_service_requests,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ];
    }
}
