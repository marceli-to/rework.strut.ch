<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Content\TeamMemberRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;

class TeamMemberController extends ResourceController
{
	protected string $model = TeamMember::class;
	protected string $resource = TeamMemberResource::class;
	protected string $request = TeamMemberRequest::class;
}
