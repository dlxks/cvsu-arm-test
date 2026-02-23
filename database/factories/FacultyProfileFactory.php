<?php

namespace Database\Factories;

use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacultyProfileFactory extends Factory
{
    protected $model = FacultyProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->lastName(),
            'last_name' => fake()->lastName(),
            'branch' => fake()->randomElement(['Main Campus', 'Indang', 'Cavite City', 'Colleg of Engineering and Information Technology', 'College of Arts and Sciences']),
            'department' => fake()->randomElement(['DIT', 'DAS', 'DTE', 'DCEE', 'DBIO', 'DCS', 'DoE']),
            'academic_rank' => fake()->randomElement(['Instructor I', 'Assistant Professor', 'Professor']),
            'email' => fake()->unique()->safeEmail(),
            'contactno' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'sex' => fake()->randomElement(['Male', 'Female']),
            'birthday' => fake()->date(),
        ];
    }
}
