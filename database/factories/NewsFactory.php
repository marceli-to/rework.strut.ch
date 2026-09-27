<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
	protected $model = News::class;

	public function definition(): array
	{
		return [
			'date_label' => fake()->monthName() . ' ' . fake()->year(),
			'title' => fake()->sentence(3),
			'subtitle' => fake()->sentence(4),
			'text' => fake()->paragraph(),
			'link_url' => null,
			'link_label' => null,
			'publish' => true,
		];
	}
}
