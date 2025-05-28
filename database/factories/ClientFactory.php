<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientFactory extends Factory
{
    protected $model = \App\Models\Client::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(), // Hoặc $this->faker->optional()->dateTime()
            'password' => bcrypt('password'), // Mật khẩu mặc định "password"
            'token' => Str::random(60),
            'photo' => $this->faker->imageUrl(200, 200, 'people'), 
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'role' => 'client',
            'status' => '1',
            'remember_token' => Str::random(10),
            'city_id' => $this->faker->optional()->numberBetween(1, 50),
            'shop_info' => $this->faker->optional()->sentence(),
            'cover_photo' => $this->faker->optional()->imageUrl(600, 200, 'business'),
        ];
    }
}
