<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
			'uuid' => $this->uuid,
			'name' => $this->name,
			'address' => $this->address,
			'email' => $this->email,
			'phone' => $this->phone,
			'maps_url' => $this->maps_url,
			'imprint' => $this->imprint,
			'publish' => $this->publish,
		];
	}
}
