<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\District;

class WardFactory extends Factory
{
    protected $model = \App\Models\Ward::class;

    public function definition()
    {
        return [
            'district_id' => District::factory(),

            'ward_name' => $this->faker->unique()->citySuffix, 
            'ward_slug' => function(array $attributes) {
                return Str::slug($attributes['ward_name']);
            },

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
