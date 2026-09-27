<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
	protected $model = Page::class;

	public function definition(): array
	{
		return [
			'key' => fake()->unique()->slug(2),
			'title' => fake()->words(2, true),
			'text' => '<p>' . fake()->paragraph() . '</p>',
			'publish' => true,
		];
	}

	public function home(): static
	{
		return $this->state(fn () => ['key' => 'home', 'title' => 'Startseite']);
	}
}
