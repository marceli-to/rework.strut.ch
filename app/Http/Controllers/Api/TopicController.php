<?php

namespace App\Http\Controllers\Api;

use App\Actions\Topic\DeleteAction;
use App\Actions\Topic\ReorderAction;
use App\Actions\Topic\StoreAction;
use App\Actions\Topic\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\ReorderTopicRequest;
use App\Http\Requests\Topic\StoreTopicRequest;
use App\Http\Requests\Topic\UpdateTopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;

class TopicController extends Controller
{
	public function index()
	{
		return TopicResource::collection(
			Topic::orderBy('sort_order')->get()
		);
	}

	public function store(StoreTopicRequest $request)
	{
		$topic = (new StoreAction)->execute($request->validated());
		return new TopicResource($topic);
	}

	public function show(Topic $topic)
	{
		return new TopicResource($topic);
	}

	public function update(UpdateTopicRequest $request, Topic $topic)
	{
		$topic = (new UpdateAction)->execute($topic, $request->validated());
		return new TopicResource($topic);
	}

	public function toggle(Topic $topic)
	{
		$topic->update(['publish' => !$topic->publish]);
		return new TopicResource($topic);
	}

	public function destroy(Topic $topic)
	{
		(new DeleteAction)->execute($topic);
		return response()->json(null, 204);
	}

	public function reorder(ReorderTopicRequest $request)
	{
		(new ReorderAction)->execute($request->validated('items'));
		return response()->json(['message' => 'ok']);
	}
}
