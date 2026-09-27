<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'name' => fake()->lastName(),
            'title' => fake()->jobTitle(),
            'email' => fake()->safeEmail(),
            'cv' => fake()->paragraph(),
            'publish' => false,
            'former' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['publish' => true]);
    }

    public function former(): static
    {
        return $this->state(fn () => ['former' => true]);
    }
}
