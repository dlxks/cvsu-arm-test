<?php

namespace Database\Factories;

use App\Models\SubjectCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubjectCategory>
 */
class SubjectCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => strtoupper(fake()->unique()->words(fake()->numberBetween(1, 4), true)),
        ];
    }
}
