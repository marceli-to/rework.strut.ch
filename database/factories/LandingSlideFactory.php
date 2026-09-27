<?php

namespace Database\Factories;

use App\Models\LandingSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

class LandingSlideFactory extends Factory
{
    protected $model = LandingSlide::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['image', 'image_text']),
            'text' => fake()->paragraph(),
            'publish' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['publish' => true]);
    }

    public function imageOnly(): static
    {
        return $this->state(fn () => ['type' => 'image', 'text' => null]);
    }
}
