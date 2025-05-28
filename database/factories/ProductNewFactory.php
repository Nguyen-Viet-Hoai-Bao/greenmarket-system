<?php

namespace Database\Factories;

use App\Models\ProductNew;
use App\Models\Client;           // Thay Client bằng User nếu bạn dùng User cho client
use App\Models\ProductTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductNewFactory extends Factory
{
    protected $model = ProductNew::class;

    public function definition()
    {
        return [
            'client_id' => Client::factory(),  // Tạo tự động client mới nếu chưa có
            'product_template_id' => ProductTemplate::factory(), // Tạo mới product template
            'qty' => $this->faker->numberBetween(0, 100),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'discount_price' => $this->faker->optional()->randomFloat(2, 5, 900),
            'most_popular' => $this->faker->boolean(20), // 20% khả năng true
            'best_seller' => $this->faker->boolean(15),  // 15% khả năng true
            'status' => 'active',  // hoặc $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
