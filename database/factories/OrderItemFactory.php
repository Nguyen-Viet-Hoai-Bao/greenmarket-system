<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = \App\Models\OrderItem::class;

    public function definition()
    {
        return [
            'order_id' => \App\Models\Order::factory(), // Tự động tạo Order mới nếu không truyền order_id
            'product_id' => $this->faker->randomNumber(), // Nên dùng factory cho Product nếu có
            'client_id' => $this->faker->optional()->uuid(),
            'qty' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 5, 500),
        ];
    }
}
