<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasPublish;
use App\Traits\HasSortOrder;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
	use HasFactory, HasMedia, HasPublish, HasSortOrder, HasUuid;

	protected $fillable = [
		'uuid',
		'title',
		'lead',
		'info',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];
}
