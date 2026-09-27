<?php

namespace App\Models;

use App\Enums\EntryType;
use App\Traits\HasMedia;
use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Press, awards and lectures: same shape, same listing (by year).
 */
class Entry extends Model
{
	use HasFactory, HasMedia, HasPublish, HasUuid;

	protected $fillable = [
		'uuid',
		'type',
		'project_id',
		'title',
		'description',
		'year',
		'url',
		'publish',
	];

	protected $casts = [
		'type' => EntryType::class,
		'year' => 'integer',
		'publish' => 'boolean',
	];

	public function project(): BelongsTo
	{
		return $this->belongsTo(Project::class);
	}

	public function scopeOfType(Builder $query, EntryType|string $type): Builder
	{
		return $query->where('type', $type instanceof EntryType ? $type->value : $type);
	}

	public function scopeOrdered(Builder $query): Builder
	{
		return $query->orderByDesc('year')->orderBy('id');
	}
}
