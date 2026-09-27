<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasPublish;
use App\Traits\HasSortOrder;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
	use HasFactory, HasMedia, HasPublish, HasSortOrder, HasUuid;

	protected $fillable = [
		'uuid',
		'firstname',
		'lastname',
		'role',
		'position',
		'phone',
		'email',
		'cv',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];

	protected function fullName(): Attribute
	{
		return Attribute::get(fn () => trim($this->firstname . ' ' . $this->lastname));
	}
}
