<?php

namespace App\Actions\Topic;

use App\Models\Topic;
use Illuminate\Support\Str;

class StoreAction
{
	public function execute(array $data): Topic
	{
		$data['slug'] = Str::slug($data['title']);
		return Topic::create($data);
	}
}
