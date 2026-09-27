<?php

namespace App\Actions\Topic;

use App\Models\Topic;
use Illuminate\Support\Str;

class UpdateAction
{
	public function execute(Topic $topic, array $data): Topic
	{
		$data['slug'] = Str::slug($data['title']);
		$topic->update($data);
		return $topic;
	}
}
