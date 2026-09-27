<?php

namespace App\Traits;

use App\Actions\Media\DeleteAction;
use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMedia
{
	protected static function bootHasMedia(): void
	{
		static::deleting(function ($model) {
			$delete = new DeleteAction;
			$model->media()->get()->each(fn (Media $media) => $delete->execute($media));
		});
	}

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order')->orderBy('id');
	}

	public function mediaIn(string $collection): MorphMany
	{
		return $this->media()->where('collection', $collection);
	}

	public function images(): MorphMany
	{
		return $this->mediaIn('images');
	}

	public function files(): MorphMany
	{
		return $this->mediaIn('files');
	}

	public function teaser(): ?Media
	{
		return $this->images->firstWhere('is_teaser', true) ?? $this->images->first();
	}
}
