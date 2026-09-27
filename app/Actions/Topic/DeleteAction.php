<?php

namespace App\Actions\Topic;

use App\Models\Topic;

class DeleteAction
{
	public function execute(Topic $topic): void
	{
		$topic->delete();
	}
}
