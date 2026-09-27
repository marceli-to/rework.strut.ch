<?php

namespace App\Actions\Team;

use App\Models\TeamMember;

class StoreAction
{
	public function execute(array $data): TeamMember
	{
		return TeamMember::create($data);
	}
}
