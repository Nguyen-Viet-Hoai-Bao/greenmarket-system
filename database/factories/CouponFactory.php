<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition()
    {
        return [
            'coupon_name' => strtoupper($this->faker->unique()->word()),
            'coupon_desc' => $this->faker->sentence(),
            'discount' => $this->faker->numberBetween(1, 100),
            'validity' => Carbon::now()->addDays(rand(1, 30))->toDateString(),
            'client_id' => $this->faker->randomDigitNotNull(), // hoặc bạn có thể gán tĩnh hoặc dùng factory Client tạo rồi truyền
            'status' => $this->faker->randomElement([0, 1]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
