<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
	use HasFactory, HasUuid;

	protected $fillable = [
		'uuid',
		'mediable_type',
		'mediable_id',
		'collection',
		'file',
		'original_name',
		'mime_type',
		'size',
		'alt',
		'caption',
		'width',
		'height',
		'crop',
		'variant',
		'is_teaser',
		'is_og',
		'sort_order',
	];

	protected $casts = [
		'is_teaser' => 'boolean',
		'is_og' => 'boolean',
		'size' => 'integer',
		'width' => 'integer',
		'height' => 'integer',
		'crop' => 'array',
	];

	public function mediable(): MorphTo
	{
		return $this->morphTo();
	}

	public function gridItems(): HasMany
	{
		return $this->hasMany(GridItem::class);
	}

	public function isImage(): bool
	{
		return str_starts_with((string) $this->mime_type, 'image/');
	}

	public function isVideo(): bool
	{
		return str_starts_with((string) $this->mime_type, 'video/');
	}

	public function isPdf(): bool
	{
		return $this->mime_type === 'application/pdf';
	}

	public function getOrientationAttribute(): string
	{
		return static::orientationFor($this->width, $this->height);
	}

	public static function orientationFor(?int $width, ?int $height): string
	{
		if (!$width || !$height) {
			return 'unknown';
		}

		return match (true) {
			$width > $height => 'landscape',
			$height > $width => 'portrait',
			default => 'square',
		};
	}
}
