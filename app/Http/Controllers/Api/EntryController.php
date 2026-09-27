<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\EntryRequest;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EntryController extends ResourceController
{
	protected string $model = Entry::class;
	protected string $resource = EntryResource::class;
	protected string $request = EntryRequest::class;

	protected array $with = ['media', 'project'];
	protected array $indexWith = ['project'];

	protected function query(Request $request): Builder
	{
		return parent::query($request)->when($request->query('type'), fn (Builder $q, string $type) => $q->ofType($type));
	}
}
