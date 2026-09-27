<?php

namespace Database\Factories;

use App\Enums\EntryType;
use App\Models\Entry;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntryFactory extends Factory
{
	protected $model = Entry::class;

	public function definition(): array
	{
		return [
			'type' => EntryType::Press,
			'project_id' => null,
			'title' => fake()->sentence(3),
			'description' => fake()->sentence(),
			'year' => fake()->numberBetween(2000, 2026),
			'url' => null,
			'publish' => false,
		];
	}
}
