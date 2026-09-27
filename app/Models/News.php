<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
	use HasFactory, HasMedia, HasPublish, HasUuid;

	protected $table = 'news';

	protected $fillable = [
		'uuid',
		'date_label',
		'title',
		'subtitle',
		'text',
		'link_url',
		'link_label',
		'publish',
	];

	protected $casts = [
		'publish' => 'boolean',
	];

	public function scopeOrdered(Builder $query): Builder
	{
		return $query->orderByDesc('id');
	}
}
