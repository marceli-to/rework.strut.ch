<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3, false);
        $location = fake()->city();

        return [
            'title' => $title,
            'location' => $location,
            'slug' => Str::slug($title . ' ' . $location),
            'subtitle' => fake()->sentence(4),
            'year' => fake()->numberBetween(1990, 2025),
            'description' => fake()->paragraph(),
            'info' => fake()->paragraph(),
            'meta_description' => fake()->sentence(),
            'publish' => false,
            'feature' => false,
            'sort_order' => 0,
            'topic_id' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['publish' => true]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['feature' => true]);
    }
}
