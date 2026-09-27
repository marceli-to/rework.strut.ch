<?php

namespace App\Models;

use App\Traits\HasMedia;
use App\Traits\HasPublish;
use App\Traits\HasSortOrder;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
	use HasFactory, HasMedia, HasPublish, HasSortOrder, HasUuid;

	protected $fillable = [
		'uuid',
		'title',
		'description',
		'info',
		'url',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];

	/**
	 * `url` holds either a web address or an e-mail address (order by mail).
	 */
	protected function href(): Attribute
	{
		return Attribute::get(fn () => $this->url && filter_var($this->url, FILTER_VALIDATE_EMAIL)
			? 'mailto:' . $this->url
			: $this->url);
	}
}
