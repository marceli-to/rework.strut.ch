<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Morph aliases for polymorphic relations (media, grid rows).
	 */
	public const MORPH_MAP = [
		'project' => \App\Models\Project::class,
		'page' => \App\Models\Page::class,
		'news' => \App\Models\News::class,
		'team_member' => \App\Models\TeamMember::class,
		'job_listing' => \App\Models\JobListing::class,
		'book' => \App\Models\Book::class,
		'entry' => \App\Models\Entry::class,
		'category' => \App\Models\Category::class,
		'category_type' => \App\Models\CategoryType::class,
		'grid_row' => \App\Models\GridRow::class,
		'grid_item' => \App\Models\GridItem::class,
		'media' => \App\Models\Media::class,
		'user' => \App\Models\User::class,
	];

	public function register(): void
	{
		//
	}

	public function boot(): void
	{
		Relation::enforceMorphMap(self::MORPH_MAP);
	}
}
