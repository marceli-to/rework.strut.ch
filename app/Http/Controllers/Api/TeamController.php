<?php

namespace App\Http\Controllers\Api;

use App\Actions\Team\DeleteAction;
use App\Actions\Team\ReorderAction;
use App\Actions\Team\StoreAction;
use App\Actions\Team\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\ReorderTeamRequest;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;

class TeamController extends Controller
{
	public function index()
	{
		return TeamMemberResource::collection(
			TeamMember::orderBy('sort_order')->get()
		);
	}

	public function store(StoreTeamRequest $request)
	{
		$member = (new StoreAction)->execute($request->validated());
		return (new TeamMemberResource($member))->response()->setStatusCode(201);
	}

	public function show(TeamMember $member)
	{
		return new TeamMemberResource($member);
	}

	public function update(UpdateTeamRequest $request, TeamMember $member)
	{
		$member = (new UpdateAction)->execute($member, $request->validated());
		return new TeamMemberResource($member);
	}

	public function toggle(TeamMember $member)
	{
		$member->update(['publish' => !$member->publish]);
		return new TeamMemberResource($member);
	}

	public function destroy(TeamMember $member)
	{
		(new DeleteAction)->execute($member);
		return response()->json(null, 204);
	}

	public function reorder(ReorderTeamRequest $request)
	{
		(new ReorderAction)->execute($request->validated('items'));
		return response()->json(['message' => 'ok']);
	}
}
