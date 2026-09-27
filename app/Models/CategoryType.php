<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasSortOrder;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryType extends Model
{
	use HasFactory, HasPublish, HasSortOrder, HasUuid;

	protected $fillable = [
		'uuid',
		'category_id',
		'name_singular',
		'name_plural',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];

	public function sortGroup(Builder $query): Builder
	{
		return $query->where('category_id', $this->category_id);
	}

	public function category(): BelongsTo
	{
		return $this->belongsTo(Category::class);
	}

	public function projects(): HasMany
	{
		return $this->hasMany(Project::class)->orderBy('sort_order')->orderBy('id');
	}
}
