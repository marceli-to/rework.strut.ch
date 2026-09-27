<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\CategoryType;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
	protected $model = Project::class;

	public function definition(): array
	{
		return [
			'category_type_id' => CategoryType::factory(),
			'title' => null,
			'name' => fake()->unique()->streetName(),
			'location' => fake()->city(),
			'year' => fake()->numberBetween(1995, 2026),
			'description' => '<p>' . fake()->paragraph() . '</p>',
			'info' => '<p>' . fake()->sentence() . '</p>',
			'status' => ProjectStatus::Executed,
			'competition' => null,
			'has_detail' => true,
			'publish' => false,
		];
	}

	public function published(): static
	{
		return $this->state(fn () => ['publish' => true]);
	}
}
