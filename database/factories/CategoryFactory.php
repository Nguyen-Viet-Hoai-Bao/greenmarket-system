<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Menu;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(), // tạo menu mới nếu chưa có
            'category_name' => $this->faker->words(2, true),
            'image' => null,
        ];
    }
}
