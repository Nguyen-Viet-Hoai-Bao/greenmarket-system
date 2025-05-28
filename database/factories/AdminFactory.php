<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'), // hoặc Hash::make()
            'token' => Str::random(10),
            'photo' => null,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'role' => 'admin',
            'status' => '1',
            'remember_token' => Str::random(10),
        ];
    }
}
