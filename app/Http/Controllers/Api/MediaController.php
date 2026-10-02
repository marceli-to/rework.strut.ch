<?php

namespace App\Http\Controllers\Api;

use App\Actions\Media\CropAction;
use App\Actions\Media\DeleteAction as DeleteMediaAction;
use App\Actions\Content\ReorderAction;
use App\Actions\Media\SetFlagAction;
use App\Actions\Media\UpdateAction as UpdateMediaAction;
use App\Actions\Media\UploadAction as UploadMediaAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\CropMediaRequest;
use App\Http\Requests\Media\ReorderMediaRequest;
use App\Http\Requests\Media\UpdateMediaRequest;
use App\Http\Requests\Media\UploadMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;

class MediaController extends Controller
{
	public function upload(UploadMediaRequest $request)
	{
		$data = (new UploadMediaAction)->execute($request->file('file'));

		return response()->json(['data' => $data]);
	}

	public function update(UpdateMediaRequest $request, Media $media)
	{
		$media = (new UpdateMediaAction)->execute($media, $request->validated());

		return new MediaResource($media);
	}

	public function destroy(Media $media)
	{
		(new DeleteMediaAction)->execute($media);

		return response()->json(null, 204);
	}

	public function reorder(ReorderMediaRequest $request)
	{
		(new ReorderAction)->execute(Media::class, $request->validated('items'));

		return response()->json(['message' => 'ok']);
	}

	public function og(Media $media)
	{
		$media = (new SetFlagAction)->execute($media, 'is_og');

		return new MediaResource($media);
	}

	public function crop(CropMediaRequest $request, Media $media)
	{
		$media = (new CropAction)->execute($media, $request->validated());

		return new MediaResource($media);
	}
}
