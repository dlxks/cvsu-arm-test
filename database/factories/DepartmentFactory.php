<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $branch = Branch::query()->inRandomOrder()->first() ?? Branch::factory()->create();

        return [
            'branch_id' => $branch->id,
            'code' => strtoupper(fake()->bothify('DEP-###')),
            'name' => fake()->randomElement([
                'Academic Programs Office',
                'Applied Sciences Department',
                'Information Technology Department',
                'Management Studies Department',
                'Student Services Office',
            ]),
            'is_active' => true,
        ];
    }

    public function forBranch(Branch $branch): static
    {
        return $this->state(fn () => ['branch_id' => $branch->id]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
