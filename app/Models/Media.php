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

	/**
	 * Public URL of the original file.
	 */
	public function url(): string
	{
		return '/storage/uploads/' . $this->file;
	}

	/**
	 * Image sizes of the legacy site (MediaService), as [max width, max height].
	 */
	public const SIZES = [
		'xs' => [500, 350],
		'sm' => [900, 500],
		'md' => [1200, 800],
		'lg' => [1600, 1100],
	];

	/**
	 * Public Glide URL for a size of SIZES (or explicit Glide params), with the
	 * crop from the admin applied.
	 */
	public function imageUrl(string|array $size = []): string
	{
		$crop = $this->crop && isset($this->crop['w'], $this->crop['h'], $this->crop['x'], $this->crop['y']) ? $this->crop : null;
		$params = $size;

		if (is_string($size)) {
			$params = $this->legacySize(self::SIZES[$size], $crop['w'] ?? $this->width, $crop['h'] ?? $this->height);
		}

		if ($crop) {
			$params['crop'] = implode(',', [$crop['w'], $crop['h'], $crop['x'], $crop['y']]);
		}

		return '/img/uploads/' . $this->file . ($params ? '?' . http_build_query($params) : '');
	}

	/**
	 * Target size as the legacy MediaService: landscape images at least the max
	 * width are scaled to it, other images at least the max height to that,
	 * the other side rounded. Exact width and height go to Glide, whose own
	 * fitting rounds the height first and can end up 1px narrower.
	 */
	private function legacySize(array $max, ?int $width, ?int $height): array
	{
		[$maxWidth, $maxHeight] = $max;

		if (!$width || !$height) {
			return ['w' => $maxWidth, 'h' => $maxHeight, 'fit' => 'max'];
		}

		if ($width > $height && $width >= $maxWidth) {
			return ['w' => $maxWidth, 'h' => (int) round($height * $maxWidth / $width), 'fit' => 'stretch'];
		}

		if ($height >= $maxHeight) {
			return ['w' => (int) round($width * $maxHeight / $height), 'h' => $maxHeight, 'fit' => 'stretch'];
		}

		return ['w' => $width, 'h' => $height, 'fit' => 'stretch'];
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
