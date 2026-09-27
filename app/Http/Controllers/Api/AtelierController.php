<?php

namespace App\Http\Controllers\Api;

use App\Actions\Atelier\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Atelier\UpdateAtelierPageRequest;
use App\Http\Resources\AtelierPageResource;
use App\Models\AtelierPage;

class AtelierController extends Controller
{
	public function index()
	{
		return AtelierPageResource::collection(
			AtelierPage::with('media')->orderBy('id')->get()
		);
	}

	public function show(AtelierPage $page)
	{
		return new AtelierPageResource($page->load('media'));
	}

	public function update(UpdateAtelierPageRequest $request, AtelierPage $page)
	{
		$page = (new UpdateAction)->execute($page, $request->validated());
		return new AtelierPageResource($page->load('media'));
	}

	public function toggle(AtelierPage $page)
	{
		$page->update(['publish' => !$page->publish]);
		return new AtelierPageResource($page);
	}
}
