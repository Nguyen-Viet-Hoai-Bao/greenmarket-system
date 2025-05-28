<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Gallery;

class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition()
    {
        return [
            'client_id' => $this->faker->randomDigitNotNull(),
            'gallery_img' => $this->faker->imageUrl(640, 480, 'cats', true, 'Faker'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
