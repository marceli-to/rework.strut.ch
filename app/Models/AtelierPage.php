<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AtelierPage extends Model
{
	use HasFactory, HasPublish, HasUuid;

	protected $fillable = [
		'uuid',
    'slug',
    'title',
    'text',
    'publish',
	];

	protected $casts = [
		'publish' => 'boolean',
	];

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
	}

	protected function ogImage(): Attribute
	{
		return Attribute::get(fn () => $this->media->first(fn ($m) => $m->isImage())?->file);
	}
}
