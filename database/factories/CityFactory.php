<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        $name = $this->faker->city();
        return [
            'city_name' => $name,
            'city_slug' => Str::slug($name),
        ];
    }
}
