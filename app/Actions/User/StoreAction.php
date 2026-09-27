<?php

namespace App\Actions\User;

use App\Models\User;

class StoreAction
{
	public function execute(array $data): User
	{
		$data['role'] = 'admin';
		$data['email_verified_at'] = now();

		return User::create($data);
	}
}
