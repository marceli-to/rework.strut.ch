<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GridRow extends Model
{
	use HasFactory, HasPublish, HasUuid;

	protected $fillable = [
		'uuid',
		'gridable_type',
		'gridable_id',
		'area',
		'layout',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];

	public function gridable(): MorphTo
	{
		return $this->morphTo();
	}

	public function items(): HasMany
	{
		return $this->hasMany(GridItem::class)->orderBy('position');
	}

	public function scopeInArea(Builder $query, string $area): Builder
	{
		return $query->where('area', $area);
	}
}
