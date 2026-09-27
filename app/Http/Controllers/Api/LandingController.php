<?php

namespace App\Http\Controllers\Api;

use App\Actions\Landing\DeleteAction as DeleteLandingSlideAction;
use App\Actions\Landing\ReorderAction as ReorderLandingSlidesAction;
use App\Actions\Landing\StoreAction as StoreLandingSlideAction;
use App\Actions\Landing\UpdateAction as UpdateLandingSlideAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landing\ReorderLandingSlideRequest;
use App\Http\Requests\Landing\StoreLandingSlideRequest;
use App\Http\Requests\Landing\UpdateLandingSlideRequest;
use App\Http\Resources\LandingSlideResource;
use App\Models\LandingSlide;

class LandingController extends Controller
{
	public function index()
	{
		$slides = LandingSlide::with('media')
			->orderBy('sort_order')
			->orderBy('created_at', 'desc')
			->get();

		return LandingSlideResource::collection($slides);
	}

	public function store(StoreLandingSlideRequest $request)
	{
		$slide = (new StoreLandingSlideAction)->execute($request->validated());

		return new LandingSlideResource($slide->load('media'));
	}

	public function show(LandingSlide $slide)
	{
		return new LandingSlideResource($slide->load('media'));
	}

	public function update(UpdateLandingSlideRequest $request, LandingSlide $slide)
	{
		$slide = (new UpdateLandingSlideAction)->execute($slide, $request->validated());

		return new LandingSlideResource($slide->load('media'));
	}

	public function toggle(LandingSlide $slide)
	{
		$slide->update(['publish' => !$slide->publish]);

		return new LandingSlideResource($slide);
	}

	public function destroy(LandingSlide $slide)
	{
		(new DeleteLandingSlideAction)->execute($slide);

		return response()->json(null, 204);
	}

	public function reorder(ReorderLandingSlideRequest $request)
	{
		(new ReorderLandingSlidesAction)->execute($request->validated('items'));

		return response()->json(['message' => 'Reordered']);
	}
}
