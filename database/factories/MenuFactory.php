<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'menu_name' => $this->faker->word,
            'image' => null, // có thể là 'uploads/menus/example.jpg' nếu cần
        ];
    }
}
