<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Media as offered/placed in a grid: adds the owning project and whether it
 * is already placed somewhere.
 */
class GridMediaResource extends MediaResource
{
	public function toArray(Request $request): array
	{
		$project = $this->mediable_type === 'project' ? $this->mediable : null;

		return [
			...parent::toArray($request),
			'project' => $project ? ['uuid' => $project->uuid, 'title' => $project->full_title] : null,
			'placed' => $this->whenCounted('gridItems', fn () => $this->grid_items_count > 0),
		];
	}
}
