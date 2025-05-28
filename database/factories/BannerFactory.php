<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = \App\Models\Banner::class;

    public function definition()
    {
        return [
            'image' => $this->faker->imageUrl(800, 300, 'business'),  // ảnh banner giả
            'url' => $this->faker->url(),                            // url giả
        ];
    }
}
