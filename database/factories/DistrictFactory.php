<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DistrictFactory extends Factory
{
    protected $model = District::class;

    public function definition()
    {
        $districtName = $this->faker->city;

        return [
            'city_id' => City::factory(), // Tạo city mới nếu chưa có
            'district_name' => $districtName,
            'district_slug' => Str::slug($districtName),
        ];
    }
}
