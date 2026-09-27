<?php

namespace App\Actions\User;

use App\Models\User;

class DeleteAction
{
	public function execute(User $user): void
	{
		$user->delete();
	}
}
