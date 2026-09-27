<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\DeleteAction;
use App\Actions\User\StoreAction;
use App\Actions\User\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
	public function index()
	{
		return UserResource::collection(
			User::orderBy('name')->get()
		);
	}

	public function store(StoreUserRequest $request)
	{
		$user = (new StoreAction)->execute($request->validated());
		return (new UserResource($user))->response()->setStatusCode(201);
	}

	public function show(User $user)
	{
		return new UserResource($user);
	}

	public function update(UpdateUserRequest $request, User $user)
	{
		$user = (new UpdateAction)->execute($user, $request->validated());
		return new UserResource($user);
	}

	public function destroy(User $user)
	{
		(new DeleteAction)->execute($user);
		return response()->json(null, 204);
	}
}
