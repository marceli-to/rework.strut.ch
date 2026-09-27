<?php

namespace Database\Factories;

use App\Models\AtelierPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class AtelierPageFactory extends Factory
{
    protected $model = AtelierPage::class;

    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'title' => fake()->sentence(3),
            'text' => fake()->paragraph(),
            'publish' => false,
        ];
    }
}
