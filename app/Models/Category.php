<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasSortOrder;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Category extends Model
{
	use HasFactory, HasPublish, HasSortOrder, HasUuid;

	protected $fillable = [
		'uuid',
		'name',
		'show_types',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'show_types' => 'boolean',
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];

	public function types(): HasMany
	{
		return $this->hasMany(CategoryType::class)->orderBy('sort_order')->orderBy('id');
	}

	public function projects(): HasManyThrough
	{
		return $this->hasManyThrough(Project::class, CategoryType::class);
	}
}
