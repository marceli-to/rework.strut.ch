<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'firstname' => $this->firstname,
			'name' => $this->name,
			'title' => $this->title,
			'email' => $this->email,
			'cv' => $this->cv,
			'publish' => $this->publish,
			'former' => $this->former,
			'sort_order' => $this->sort_order,
		];
	}
}
