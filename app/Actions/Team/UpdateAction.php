<?php

namespace App\Actions\Team;

use App\Models\TeamMember;

class UpdateAction
{
	public function execute(TeamMember $member, array $data): TeamMember
	{
		$member->update($data);

		return $member->fresh();
	}
}
