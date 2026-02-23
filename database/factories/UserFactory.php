<?php

namespace Database\Factories;

use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(), // unique() prevents collisions
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function faculty(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('faculty');

            FacultyProfile::factory()->create([
                'user_id' => $user->id,
                'email' => $user->email,
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name)[1] ?? 'Faculty',
            ]);
        });
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole('admin');
        });
    }
}
