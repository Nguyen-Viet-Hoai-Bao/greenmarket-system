<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    // Tên model tương ứng, thay thành App\Models\Order nếu bạn có namespace khác
    protected $model = \App\Models\Order::class;

    public function definition()
    {
        $date = $this->faker->dateTimeBetween('-1 years', 'now');
        return [
            'user_id' => $this->faker->randomNumber(), // Bạn nên dùng factory của user nếu có
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'payment_type' => $this->faker->randomElement(['card', 'cash', 'paypal']),
            'payment_method' => $this->faker->randomElement(['visa', 'mastercard', 'stripe']),
            'transaction_id' => Str::random(10),
            'currency' => 'USD',
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'total_amount' => $this->faker->randomFloat(2, 10, 2000),
            'order_number' => Str::upper(Str::random(10)),
            'invoice_no' => 'INV' . $this->faker->unique()->numberBetween(1000, 9999),
            'order_date' => $date->format('d F Y'),
            'order_month' => $date->format('F'),
            'order_year' => $date->format('Y'),
            'confirmed_date' => $date->modify('+1 day')->format('d F Y'),
            'processing_date' => $date->modify('+2 days')->format('d F Y'),
            'shipped_date' => $date->modify('+3 days')->format('d F Y'),
            'delivered_date' => $date->modify('+5 days')->format('d F Y'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'processing', 'shipped', 'delivered']),
        ];
    }
}
