<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductTemplateFactory extends Factory
{
    protected $model = \App\Models\ProductTemplate::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'category_id' => \App\Models\Category::factory(), // tạo category nếu chưa có
            'menu_id' => \App\Models\Menu::factory(), // tạo menu nếu chưa có, hoặc có thể null
            'code' => $this->faker->bothify('CODE-###??'),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'unit' => $this->faker->randomElement(['kg', 'g', 'pcs', 'box']),
            'image' => $this->faker->imageUrl(640, 480, 'products'),
            'status' => $this->faker->randomElement([0, 1]), // hoặc mặc định là 1
        ];
    }
}
