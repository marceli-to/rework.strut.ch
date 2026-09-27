<?php

namespace Database\Factories;

use App\Models\JobListing;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobListingFactory extends Factory
{
	protected $model = JobListing::class;

	public function definition(): array
	{
		return [
			'title' => fake()->jobTitle(),
			'lead' => fake()->sentence(),
			'info' => '<p>' . fake()->paragraph() . '</p>',
			'publish' => false,
		];
	}
}
