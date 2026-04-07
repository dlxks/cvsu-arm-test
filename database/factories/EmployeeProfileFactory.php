<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Department;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeProfile>
 */
class EmployeeProfileFactory extends Factory
{
    protected $model = EmployeeProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $middleName = fake()->boolean(40) ? fake()->lastName() : null;
        $lastName = fake()->lastName();
        $department = Department::query()->inRandomOrder()->first();
        $branchId = $department?->branch_id ?? Branch::query()->inRandomOrder()->value('id') ?? Branch::factory()->create()->id;

        return [
            'user_id' => User::factory()->state([
                'name' => trim($firstName.' '.($middleName ? $middleName.' ' : '').$lastName),
            ]),
            'employee_no' => 'EMP-'.fake()->unique()->numerify('#####'),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'position' => fake()->jobTitle(),
            'branch_id' => $branchId,
            'department_id' => $department?->id,
        ];
    }

    public function forDepartment(Department $department): static
    {
        return $this->state(fn () => [
            'branch_id' => $department->branch_id,
            'department_id' => $department->id,
        ]);
    }

    public function withoutDepartment(): static
    {
        return $this->state(fn () => [
            'branch_id' => null,
            'department_id' => null,
        ]);
    }
}
