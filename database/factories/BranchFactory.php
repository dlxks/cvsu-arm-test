<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['Main', 'Satellite']);
        $location = fake()->unique()->city();

        return [
            'code' => strtoupper(fake()->unique()->bothify($type === 'Main' ? 'COL-###' : 'SAT-###')),
            'name' => $type === 'Main'
                ? 'CvSU '.$location.' College'
                : 'CvSU '.$location.' Campus',
            'type' => $type,
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }

    public function main(): static
    {
        return $this->state(fn () => ['type' => 'Main']);
    }

    public function satellite(): static
    {
        return $this->state(fn () => ['type' => 'Satellite']);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
