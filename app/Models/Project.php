<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Project extends Model
{
	use HasFactory, HasPublish, HasSlug, HasUuid;

	public function getSlugOptions(): SlugOptions
	{
		return SlugOptions::create()
			->generateSlugsFrom(fn (Project $p) => collect([$p->title, $p->location, $p->year])->filter()->implode(' '))
			->saveSlugsTo('slug')
			->usingLanguage('de')
			->doNotGenerateSlugsOnUpdate()
			->preventOverwrite();
	}

	protected $fillable = [
		'uuid',
    'title',
    'location', 
    'slug', 
    'subtitle', 
    'year',
		'description', 
    'info', 
    'meta_description',
		'publish',
    'feature',
    'detail',
    'sort_order',
    'topic_id',
	];

	protected $casts = [
		'publish' => 'boolean',
		'feature' => 'boolean',
		'detail' => 'boolean',
		'year' => 'integer',
		'sort_order' => 'integer',
	];

	public function media(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
	}

	public function teaser(): MorphMany
	{
		return $this->morphMany(Media::class, 'mediable')->where('is_teaser', true);
	}

	public function topic(): BelongsTo
	{
		return $this->belongsTo(Topic::class);
	}

	public function scopeFeatured(Builder $query): Builder
	{
		return $query->where('feature', true);
	}

	public function scopeDetailed(Builder $query): Builder
	{
		return $query->where('detail', true);
	}

	protected function fullTitle(): Attribute
	{
		return Attribute::get(fn () => collect([$this->title, $this->location])->filter()->implode(', '));
	}

	protected function metaDescription(): Attribute
	{
		return Attribute::get(function ($value) {
			if ($value) {
				return $value;
			}
			$text = trim(preg_replace('/\s+/', ' ', strip_tags($this->description ?? '')));
			return Str::limit($text, 160);
		});
	}

	protected function ogImage(): Attribute
	{
		return Attribute::get(fn () => $this->media->firstWhere('is_og', true)?->file
			?? $this->media->first(fn ($m) => $m->isImage())?->file);
	}
}
