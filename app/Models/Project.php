<?php

namespace App\Models;

use App\Enums\Competition;
use App\Enums\ProjectStatus;
use App\Support\Slug;
use App\Traits\HasGrid;
use App\Traits\HasMedia;
use App\Traits\HasPublish;
use App\Traits\HasSortOrder;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
	use HasFactory, HasGrid, HasMedia, HasPublish, HasSortOrder, HasUuid;

	protected $fillable = [
		'uuid',
		'category_type_id',
		'title',
		'name',
		'location',
		'slug',
		'year',
		'description',
		'info',
		'status',
		'competition',
		'has_detail',
		'meta_description',
		'publish',
		'sort_order',
	];

	protected $casts = [
		'year' => 'integer',
		'status' => ProjectStatus::class,
		'competition' => Competition::class,
		'has_detail' => 'boolean',
		'publish' => 'boolean',
		'sort_order' => 'integer',
	];

	/**
	 * Slug is generated once (same algorithm as the legacy site) and only
	 * changes when it is set explicitly.
	 */
	protected static function booted(): void
	{
		static::creating(function (Project $project) {
			$project->slug = Slug::unique(
				$project->slug ?: Slug::project($project->name, $project->location, $project->year),
				static::class,
			);
		});

		static::updating(function (Project $project) {
			if ($project->isDirty('slug')) {
				$project->slug = Slug::unique($project->slug, static::class, $project->id);
			}
		});
	}

	public function sortGroup(Builder $query): Builder
	{
		return $query->where('category_type_id', $this->category_type_id);
	}

	public function categoryType(): BelongsTo
	{
		return $this->belongsTo(CategoryType::class);
	}

	public function entries(): HasMany
	{
		return $this->hasMany(Entry::class);
	}

	public function scopeDetailed(Builder $query): Builder
	{
		return $query->where('has_detail', true);
	}

	/**
	 * Open Graph image: the image flagged in the admin, else the first image (legacy).
	 */
	public function ogImage(): ?Media
	{
		return $this->images->firstWhere('is_og', true) ?? $this->images->first();
	}

	protected function fullTitle(): Attribute
	{
		return Attribute::get(fn () => collect([$this->name, $this->location])->filter()->implode(', '));
	}

	/**
	 * Public URL (legacy scheme, ids are kept on import): /bauten/{id}/{slug}
	 */
	protected function url(): Attribute
	{
		return Attribute::get(fn () => '/bauten/' . $this->id . '/' . $this->slug);
	}

	protected function metaDescription(): Attribute
	{
		return Attribute::get(function ($value) {
			if ($value) {
				return $value;
			}
			// a space at line breaks and block ends, so paragraphs don't run together
			$html = preg_replace('#<br\s*/?>|</(p|li|h\d|div)>#i', ' ', $this->description ?? '');
			$text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html))));
			return Str::limit($text, 160);
		});
	}
}
