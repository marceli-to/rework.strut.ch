<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One slot of a grid row: either a media item (image/video) or a news item.
 */
class GridItem extends Model
{
	use HasFactory, HasUuid;

	protected $fillable = [
		'uuid',
		'grid_row_id',
		'position',
		'media_id',
		'news_id',
	];

	protected $casts = [
		'position' => 'integer',
	];

	public function row(): BelongsTo
	{
		return $this->belongsTo(GridRow::class, 'grid_row_id');
	}

	public function media(): BelongsTo
	{
		return $this->belongsTo(Media::class);
	}

	public function news(): BelongsTo
	{
		return $this->belongsTo(News::class);
	}

	public function type(): string
	{
		if ($this->news_id) {
			return 'news';
		}

		return $this->media?->isVideo() ? 'video' : 'image';
	}
}
