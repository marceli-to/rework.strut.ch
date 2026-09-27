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
			'lastname' => fake()->lastName(),
			'role' => 'Architekt FH SIA',
			'position' => null,
			'phone' => fake()->phoneNumber(),
			'email' => fake()->safeEmail(),
			'cv' => '<p>' . fake()->paragraph() . '</p>',
			'publish' => false,
		];
	}
}
