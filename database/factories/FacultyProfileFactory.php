<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyProfileFactory extends Factory
{
    protected $model = FacultyProfile::class;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $middleName = fake()->boolean(40) ? fake()->lastName() : null;
        $lastName = fake()->lastName();
        $email = fake()->unique()->safeEmail();
        $department = Department::query()->inRandomOrder()->first() ?? Department::factory()->create();

        return [
            'user_id' => User::factory()->state([
                'name' => trim($firstName.' '.($middleName ? $middleName.' ' : '').$lastName),
                'email' => $email,
            ]),
            'employee_no' => 'FAC-'.fake()->unique()->numerify('#####'),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'branch_id' => $department->branch_id,
            'department_id' => $department->id,
            'academic_rank' => fake()->randomElement(['Instructor I', 'Assistant Professor', 'Professor']),
            'email' => fn (array $attributes) => User::query()->find($attributes['user_id'])?->email ?? $email,
            'contactno' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'sex' => fake()->randomElement(['Male', 'Female']),
            'birthday' => fake()->dateTimeBetween('-60 years', '-21 years')->format('Y-m-d'),
            'updated_by' => User::query()->inRandomOrder()->value('id'),
        ];
    }

    public function forDepartment(Department $department): static
    {
        return $this->state(fn () => [
            'branch_id' => $department->branch_id,
            'department_id' => $department->id,
        ]);
    }
}
