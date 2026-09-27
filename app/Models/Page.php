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

	public static function findByKey(string $key): self
	{
		return static::where('key', $key)->firstOrFail();
	}

	public function gridContext(): string
	{
		return $this->key;
	}
}
