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
			'lastname' => $this->lastname,
			'full_name' => $this->full_name,
			'role' => $this->role,
			'position' => $this->position,
			'phone' => $this->phone,
			'email' => $this->email,
			'cv' => $this->cv,
			'publish' => $this->publish,
			'sort_order' => $this->sort_order,
			'media' => MediaResource::collection($this->whenLoaded('media')),
		];
	}
}
