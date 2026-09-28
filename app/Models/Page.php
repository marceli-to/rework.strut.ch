<?php

namespace App\Models;

use App\Traits\HasGrid;
use App\Traits\HasMedia;
use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
	use HasFactory, HasGrid, HasMedia, HasPublish, HasUuid;

	/**
	 * Fixed set of pages, created by the import / `strut:pages`.
	 */
	public const KEYS = [
		'home' => 'Startseite',
		'works' => 'Werkliste',
		'press' => 'Presse',
		'books' => 'Bücher',
		'downloads' => 'Downloads',
		'about' => 'Über uns',
		'jobs' => 'Jobs',
		'awards' => 'Auszeichnungen',
		'lectures' => 'Vorträge',
		'contact' => 'Kontakt',
		'imprint' => 'Impressum',
	];

	/**
	 * Pages with editable title/text/images. The others are listing pages;
	 * for those only the SEO fields (meta description, OG image) are edited.
	 */
	public const CONTENT_KEYS = ['about', 'jobs', 'contact', 'imprint'];

	protected $fillable = [
		'uuid',
		'key',
		'title',
		'text',
		'meta_description',
		'publish',
	];

	protected $casts = [
		'publish' => 'boolean',
	];

	public function isContent(): bool
	{
		return in_array($this->key, self::CONTENT_KEYS);
	}

	/**
	 * Sorts pages in the order of KEYS.
	 */
	public static function sortByKey(\Illuminate\Support\Collection $pages): \Illuminate\Support\Collection
	{
		$order = array_flip(array_keys(self::KEYS));

		return $pages->sortBy(fn (self $page) => $order[$page->key] ?? PHP_INT_MAX)->values();
	}

	public static function findByKey(string $key): self
	{
		return static::where('key', $key)->firstOrFail();
	}

	/**
	 * Open Graph image of the page (admin field "Opengraph Image").
	 */
	public function ogImage(): ?Media
	{
		return $this->mediaIn('og')->first();
	}

	public function gridContext(): string
	{
		return $this->key;
	}
}
