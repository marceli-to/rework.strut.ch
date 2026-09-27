<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
	use HasFactory, HasPublish, HasUuid;

	protected $fillable = [
		'uuid',  
    'title', 
    'slug', 
    'publish', 
    'sort_order',
	];

	protected $casts = [
		'uuid' => 'string',
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];

	public function projects(): HasMany
	{
		return $this->hasMany(Project::class);
	}
}
