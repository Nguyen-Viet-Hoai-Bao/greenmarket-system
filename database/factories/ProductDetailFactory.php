<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductDetailFactory extends Factory
{
    protected $model = \App\Models\ProductDetail::class;

    public function definition()
    {
        return [
            // Giả sử ProductTemplate đã tồn tại, hoặc bạn có thể tạo mới
            'product_template_id' => \App\Models\ProductTemplate::factory(),

            'description' => $this->faker->optional()->text(200) ?: 'Đang cập nhật',
            'product_info' => $this->faker->optional()->text(200) ?: 'Đang cập nhật',
            'note' => $this->faker->optional()->text(100) ?: 'Đang cập nhật',
            'origin' => $this->faker->optional()->state(function () {
                return 'Đang cập nhật';
            }),
            'preservation' => $this->faker->optional()->text(150) ?: 'Đang cập nhật',
            'weight' => $this->faker->optional()->randomFloat(2, 0.1, 10) . ' kg' ?: 'Đang cập nhật',
            'usage_instructions' => $this->faker->optional()->text(250) ?: 'Đang cập nhật',
        ];
    }
}
