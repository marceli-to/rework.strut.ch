<?php

namespace App\Actions\Team;

use App\Models\TeamMember;

class DeleteAction
{
	public function execute(TeamMember $member): void
	{
		$member->delete();
	}
}
