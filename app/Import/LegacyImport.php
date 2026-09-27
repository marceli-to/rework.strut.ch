<?php

namespace App\Import;

use App\Enums\Competition;
use App\Enums\EntryType;
use App\Enums\ProjectStatus;
use App\Models\Book;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Entry;
use App\Models\GridItem;
use App\Models\GridRow;
use App\Models\JobListing;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Imports the legacy strut.ch database (see docs/analysis.md §7 for the
 * field mapping). Idempotent: every legacy row is tracked in legacy_map.
 */
class LegacyImport
{
	/** Legacy highlight slideshow elements live under this home_grid id. */
	public const HIGHLIGHT_GRID_ID = 1;

	/** Unreferenced legacy files attached on import (client decision Q8). */
	public const ORPHAN_ATTACHMENTS = [51 => '/_strut_stadtterrasse_\d+\.jpg$/'];

	/** Meta descriptions hardcoded in the legacy views. */
	public const PAGE_META = [
		'home' => 'Strut Architekten AG aus Winterthur, Schweiz. Gegründet im Jahre 2015 durch Roger Studerus, Felix Rutishauser und Peter Kunz.',
		'contact' => 'Strut Architekten AG aus Winterthur, Schweiz. Gegründet im Jahre 2015 durch Roger Studerus, Felix Rutishauser und Peter Kunz.',
		'works' => 'Strut Architekten AG entwickelt und plant anspruchsvolle Wohn- und Gewerbebauten. Das Büro kann auf erfolgreiche Projekte und mehr als 20-jährige Erfahrungen zurückgreifen.',
		'press' => 'Strut Architekten AG zeigt in verschieden Publikationen eine breite Palette an ausgeführten Gebäuden: Schulgebäude, Private Wohnbauten und Siedlungen, Produktions- und Verwaltungsgebäude.',
		'books' => 'Strut Architekten AG zeigt in verschieden Publikationen eine breite Palette an ausgeführten Gebäuden: Schulgebäude, Private Wohnbauten und Siedlungen, Produktions- und Verwaltungsgebäude.',
		'downloads' => 'Strut Architekten AG zeigt in verschieden Publikationen eine breite Palette an ausgeführten Gebäuden: Schulgebäude, Private Wohnbauten und Siedlungen, Produktions- und Verwaltungsgebäude.',
		'about' => 'Roger Studerus und Felix Rutishauser tragen gemeinsam die Verantwortung für die Strut Architekten AG. Im Team mit Peter Kunz werden eigenständige Projekte entwickelt, welche den Menschen ins Zentrum rücken.',
		'jobs' => 'Roger Studerus und Felix Rutishauser tragen gemeinsam die Verantwortung für die Strut Architekten AG. Im Team mit Peter Kunz werden eigenständige Projekte entwickelt, welche den Menschen ins Zentrum rücken.',
		'awards' => 'Roger Studerus und Felix Rutishauser tragen gemeinsam die Verantwortung für die Strut Architekten AG. Im Team mit Peter Kunz werden eigenständige Projekte entwickelt, welche den Menschen ins Zentrum rücken.',
		'lectures' => 'Roger Studerus und Felix Rutishauser tragen gemeinsam die Verantwortung für die Strut Architekten AG. Im Team mit Peter Kunz werden eigenständige Projekte entwickelt, welche den Menschen ins Zentrum rücken.',
	];

	protected Connection $legacy;
	protected MediaCopier $media;
	protected HtmlCleaner $clean;

	public function __construct(protected ImportContext $context, string $mediaSource)
	{
		$this->legacy = DB::connection('legacy');
		$this->media = new MediaCopier($context, $mediaSource);
		$this->clean = $context->clean;
	}

	public function run(): ImportContext
	{
		$this->users();
		$this->categories();
		$this->projects();
		$this->projectMedia();
		$this->projectGrids();
		$this->news();
		$this->pages();
		$this->homeGrid();
		$this->team();
		$this->jobs();
		$this->books();
		$this->entries();

		return $this->context;
	}

	/**
	 * Unwrap a spatie-translatable JSON value ({"de": …}).
	 */
	protected function de(?string $json): ?string
	{
		if ($json === null) {
			return null;
		}

		$value = json_decode($json, true);

		return is_array($value) ? ($value['de'] ?? null) : $json;
	}

	/**
	 * Rows ranked by the legacy `order` column (ties by id) → 0..n per group.
	 */
	protected function ranked(Collection $rows, ?string $groupBy = null, string $order = 'order'): Collection
	{
		return $rows->groupBy(fn ($row) => $groupBy ? $row->{$groupBy} : 0)
			->flatMap(fn (Collection $group) => $group
				->sortBy([[$order, 'asc'], ['id', 'asc']])
				->values()
				->each(fn ($row, $rank) => $row->rank = $rank));
	}

	protected function users(): void
	{
		$rows = $this->legacy->table('users')->get();

		foreach ($rows as $row) {
			[$firstname, $name] = array_pad(explode(' ', trim($row->name), 2), 2, '');

			// raw insert/update: keep the legacy password hash as is (no re-hash)
			$values = [
				'firstname' => $firstname,
				'name' => $name,
				'password' => $row->password,
				'email_verified_at' => $row->email_verified_at ?? now(),
				'updated_at' => now(),
			];

			if (DB::table('users')->where('email', $row->email)->exists()) {
				DB::table('users')->where('email', $row->email)->update($values);
			} else {
				DB::table('users')->insert($values + ['uuid' => (string) Str::uuid(), 'email' => $row->email, 'role' => 'admin', 'created_at' => $row->created_at ?? now()]);
			}

			$this->context->remember('users', $row->id, User::where('email', $row->email)->first());
		}

		$this->context->count('Benutzer', $rows->count(), User::count());
	}

	protected function categories(): void
	{
		$categories = $this->ranked($this->legacy->table('categories')->get());

		foreach ($categories as $row) {
			$this->context->upsert('categories', $row->id, Category::class, [
				'name' => $this->clean->text($this->de($row->name)),
				'show_types' => (bool) $row->show_types,
				'publish' => (bool) $row->publish,
				'sort_order' => $row->rank,
			]);
		}

		$types = $this->ranked($this->legacy->table('category_types')->get(), 'category_id');

		foreach ($types as $row) {
			$this->context->upsert('category_types', $row->id, CategoryType::class, [
				'category_id' => $this->context->id('categories', $row->category_id),
				'name_singular' => $this->clean->text($this->de($row->name_singular)),
				'name_plural' => $this->clean->text($this->de($row->name_plural)),
				'publish' => (bool) $row->publish,
				'sort_order' => $row->rank,
			]);
		}

		$this->context->count('Kategorien', $categories->count(), Category::count());
		$this->context->count('Typen', $types->count(), CategoryType::count());
	}

	protected function projects(): void
	{
		$rows = $this->ranked($this->legacy->table('projects')->get(), 'category_type_id');

		foreach ($rows as $row) {
			$attributes = [
				'category_type_id' => $this->context->id('category_types', $row->category_type_id),
				'title' => $this->clean->text($this->de($row->title)),
				'name' => $this->clean->text($this->de($row->name)),
				'location' => $this->clean->text($this->de($row->location)),
				'year' => $row->year,
				'description' => $this->clean->html($this->de($row->description)),
				'info' => $this->clean->html($this->de($row->info)),
				'status' => $this->status($row->status),
				'competition' => $this->competition($row->competition),
				'has_detail' => (bool) $row->has_detail,
				'publish' => (bool) $row->publish,
				'sort_order' => $row->rank,
			];

			// keep the legacy id: public URLs are /bauten/{id}/{slug}
			if (!$this->context->find('projects', $row->id)) {
				$attributes['id'] = $row->id;
			}

			$this->context->upsert('projects', $row->id, Project::class, $attributes);
		}

		$this->context->count('Projekte', $rows->count(), Project::count());
	}

	protected function projectMedia(): void
	{
		$images = $this->ranked($this->legacy->table('project_images')->get(), 'project_id');
		$videos = $this->legacy->table('project_videos')->get();
		$files = $this->ranked($this->legacy->table('project_files')->get(), 'project_id');
		$projects = Project::all()->keyBy('id');

		foreach ($images as $row) {
			$this->media->attach($projects[$row->project_id], 'project_images', $row->id, '', $row->name, 'images', [
				'caption' => $this->clean->text($this->de($row->caption)),
				'sort_order' => $row->rank,
			]);
		}

		foreach ($videos as $row) {
			$this->media->attach($projects[$row->project_id], 'project_videos', $row->id, '', $row->name, 'images', [
				'caption' => $this->clean->text($this->de($row->caption)),
				'sort_order' => 1000 + $row->id,
			]);
		}

		foreach ($files as $row) {
			$this->media->attach($projects[$row->project_id], 'project_files', $row->id, '', $row->name, 'files', [
				'caption' => $this->clean->text($this->de($row->caption)),
				'sort_order' => $row->rank,
			]);
		}

		$this->orphans($projects);

		$this->context->count('Projektbilder', $images->count(), $this->mapped('project_images'));
		$this->context->count('Projektvideos', $videos->count(), $this->mapped('project_videos'));
		$this->context->count('Projektdokumentationen (PDF)', $files->count(), $this->mapped('project_files'));
	}

	/**
	 * Legacy files on disk that no record references (reported), with the
	 * approved exception of attaching the Stadtterrasse images.
	 */
	protected function orphans(Collection $projects): void
	{
		$source = config('strut.legacy_media_path');
		$referenced = collect([
			...$this->legacy->table('project_images')->pluck('name'),
			...$this->legacy->table('project_files')->pluck('name'),
			...$this->legacy->table('project_videos')->pluck('name'),
			...$this->legacy->table('content_images')->pluck('name'),
		]);
		foreach (['team' => ['media'], 'news' => ['media'], 'books' => ['media'], 'jobs' => ['media'], 'press' => ['media', 'file'], 'awards' => ['media', 'file'], 'lectures' => ['media', 'file']] as $table => $columns) {
			foreach ($columns as $column) {
				$referenced->push(...$this->legacy->table($table)->whereNotNull($column)->pluck($column));
			}
		}
		$referenced = $referenced->filter()->flip();

		$files = collect(glob($source . '/*.*'))->merge(glob($source . '/downloads/*.*'))->map(fn ($path) => basename($path));
		$sort = 1000;

		foreach ($files->reject(fn ($file) => $referenced->has($file))->sort()->values() as $file) {
			$projectId = collect(self::ORPHAN_ATTACHMENTS)->search(fn ($pattern) => preg_match($pattern, $file));

			if ($projectId && isset($projects[$projectId])) {
				$this->media->attach($projects[$projectId], 'orphan_files', $projectId, $file, $file, 'images', ['sort_order' => $sort++]);
			} else {
				$this->context->unusedFiles[] = $file;
			}
		}
	}

	protected function projectGrids(): void
	{
		$layouts = $this->legacy->table('project_grid_layouts')->pluck('key', 'id');
		$rows = $this->ranked($this->legacy->table('project_grids')->get(), 'project_id');

		foreach ($rows as $row) {
			$this->context->upsert('project_grids', $row->id, GridRow::class, [
				'gridable_type' => 'project',
				'gridable_id' => $row->project_id,
				'area' => 'main',
				'layout' => $layouts[$row->layout_id],
				'publish' => (bool) $row->publish,
				'sort_order' => $row->rank,
			]);
		}

		$elements = $this->legacy->table('project_grid_elements')->orderBy('id')->get();
		foreach ($elements as $row) {
			$this->gridItem('project_grid_elements', $row, $this->context->id('project_grids', $row->grid_id));
		}

		$this->context->count('Projektraster: Zeilen', $rows->count(), $this->mapped('project_grids'));
		$this->context->count('Projektraster: Elemente', $elements->count(), $this->mapped('project_grid_elements'));
	}

	protected function news(): void
	{
		$rows = $this->legacy->table('news')->get();

		foreach ($rows as $row) {
			$news = $this->context->upsert('news', $row->id, News::class, [
				'date_label' => $this->clean->text($this->de($row->date)),
				'title' => $this->clean->text($this->de($row->title)),
				'subtitle' => $this->clean->text($this->de($row->subtitle)),
				'text' => $this->clean->text($this->de($row->text)),
				'link_url' => $this->clean->text($this->de($row->link)),
				'link_label' => $this->clean->text($this->de($row->linkText)),
				'publish' => (bool) $row->publish,
				'created_at' => $row->created_at,
			]);

			$this->media->attach($news, 'news', $row->id, 'media', $row->media, 'images');
		}

		$this->context->count('News', $rows->count(), News::count());
	}

	protected function pages(): void
	{
		$content = $this->legacy->table('content')->get()->keyBy('key');

		foreach (Page::KEYS as $key => $label) {
			$row = $content[$key] ?? null;
			$page = Page::firstOrNew(['key' => $key]);

			$page->fill([
				'title' => $row ? $this->clean->text($this->de($row->title)) : $label,
				'text' => $row ? $this->clean->html($this->de($row->text)) : $page->text,
				'meta_description' => $page->meta_description ?? (self::PAGE_META[$key] ?? null),
				'publish' => $row ? (bool) $row->publish : true,
			])->save();

			if ($row) {
				$this->context->remember('content', $row->id, $page);
			}
		}

		$images = $this->legacy->table('content_images')->get();
		foreach ($images as $row) {
			$page = $this->context->find('content', $row->content_id);
			$this->media->attach($page, 'content_images', $row->id, '', $row->name, 'images', [
				'caption' => $this->clean->text($row->caption),
			]);
		}

		$this->context->count('Seiten (aus content)', $content->count(), $this->mapped('content'));
		$this->context->count('Seitenbilder', $images->count(), $this->mapped('content_images'));
	}

	protected function homeGrid(): void
	{
		$home = Page::where('key', 'home')->firstOrFail();
		$layouts = $this->legacy->table('home_grid_layouts')->pluck('key', 'id');
		$rows = $this->ranked($this->legacy->table('home_grids')->get());

		foreach ($rows as $row) {
			$this->context->upsert('home_grids', $row->id, GridRow::class, [
				'gridable_type' => 'page',
				'gridable_id' => $home->id,
				'area' => 'main',
				'layout' => $layouts[$row->layout_id],
				'publish' => (bool) $row->publish,
				'sort_order' => $row->rank,
			]);
		}

		$highlight = $this->context->upsert('home_grids', self::HIGHLIGHT_GRID_ID, GridRow::class, [
			'gridable_type' => 'page',
			'gridable_id' => $home->id,
			'area' => 'highlight',
			'layout' => 'slideshow',
			'publish' => true,
			'sort_order' => 0,
		], 'highlight');

		// only what the live site shows (staging was dropped, Q4)
		$elements = $this->legacy->table('home_grid_elements')->where('environment', 'production')->orderBy('id')->get();
		$slide = 0;

		foreach ($elements as $row) {
			if ($row->grid_id === self::HIGHLIGHT_GRID_ID) {
				$row->position = $slide++;
				$this->gridItem('home_grid_elements', $row, $highlight->id);
			} else {
				$this->gridItem('home_grid_elements', $row, $this->context->id('home_grids', $row->grid_id));
			}
		}

		$this->context->count('Startseite: Zeilen (+ Highlights)', $rows->count() + 1, $this->mapped('home_grids'));
		$this->context->count('Startseite: Elemente', $elements->count(), $this->mapped('home_grid_elements'));
	}

	protected function gridItem(string $table, object $row, ?int $gridRowId): void
	{
		$mediaId = $this->context->id('project_images', $row->project_image_id) ?? $this->context->id('project_videos', $row->project_video_id ?? null);
		$newsId = $this->context->id('news', $row->news_id ?? null);

		if (!$gridRowId) {
			$this->context->skip($table, $row->id, 'Zeile nicht gefunden');
			return;
		}

		if (!$mediaId && !$newsId) {
			$this->context->skip($table, $row->id, 'Bild/Video/News nicht importiert');
			return;
		}

		// legacy data contains duplicates (same row + position): the first one
		// is what the live site renders, later ones are skipped
		$occupied = GridItem::where('grid_row_id', $gridRowId)->where('position', $row->position)
			->whereNotIn('id', array_filter([$this->context->id($table, $row->id)]))
			->exists();

		if ($occupied) {
			$this->context->skip($table, $row->id, "Position {$row->position} bereits belegt (Duplikat in Legacy-Daten)");
			return;
		}

		$this->context->upsert($table, $row->id, GridItem::class, [
			'grid_row_id' => $gridRowId,
			'position' => $row->position,
			'media_id' => $newsId ? null : $mediaId,
			'news_id' => $newsId,
		]);
	}

	protected function team(): void
	{
		$rows = $this->ranked($this->legacy->table('team')->get());

		foreach ($rows as $row) {
			$member = $this->context->upsert('team', $row->id, TeamMember::class, [
				'firstname' => $this->clean->text($row->firstname),
				'lastname' => $this->clean->text($row->name),
				'role' => $this->clean->text($this->de($row->role)),
				'position' => $this->clean->text($this->de($row->position)),
				'phone' => $this->clean->text($row->phone),
				'email' => $this->clean->text($row->email),
				'cv' => $this->clean->html($this->de($row->cv)),
				'publish' => (bool) $row->publish,
				'sort_order' => $row->rank,
			]);

			$this->media->attach($member, 'team', $row->id, 'media', $row->media, 'images');
		}

		$this->context->count('Team', $rows->count(), TeamMember::count());
	}

	protected function jobs(): void
	{
		$rows = $this->ranked($this->legacy->table('jobs')->get());

		foreach ($rows as $row) {
			$job = $this->context->upsert('jobs', $row->id, JobListing::class, [
				'title' => $this->clean->text($this->de($row->title)),
				'lead' => $this->clean->text($this->de($row->lead)),
				'info' => $this->clean->html($this->de($row->info)),
				'publish' => (bool) $row->publish,
				'sort_order' => $row->rank,
			]);

			$this->media->attach($job, 'jobs', $row->id, 'media', $row->media, 'files');
		}

		$this->context->count('Jobs', $rows->count(), JobListing::count());
	}

	protected function books(): void
	{
		$rows = $this->ranked($this->legacy->table('books')->get());

		foreach ($rows as $row) {
			$book = $this->context->upsert('books', $row->id, Book::class, [
				'title' => $this->clean->text($row->title),
				'description' => $this->clean->text($this->de($row->description)),
				'info' => $this->clean->html($this->de($row->info)),
				'url' => $this->clean->text($row->url),
				'publish' => (bool) $row->publish,
				'sort_order' => $row->rank,
			]);

			$this->media->attach($book, 'books', $row->id, 'media', $row->media, 'images');
		}

		$this->context->count('Bücher', $rows->count(), Book::count());
	}

	protected function entries(): void
	{
		foreach (['press' => EntryType::Press, 'awards' => EntryType::Award, 'lectures' => EntryType::Lecture] as $table => $type) {
			$rows = $this->legacy->table($table)->get();

			foreach ($rows as $row) {
				$entry = $this->context->upsert($table, $row->id, Entry::class, [
					'type' => $type,
					'project_id' => isset($row->project_id) ? $this->context->id('projects', $row->project_id) : null,
					'title' => $this->clean->text($this->de($row->title)),
					'description' => $this->clean->text($this->de($row->description)),
					'year' => $row->year,
					'url' => $this->clean->text($row->url),
					'publish' => (bool) $row->publish,
				]);

				$this->media->attach($entry, $table, $row->id, 'media', $row->media, 'images');
				$this->media->attach($entry, $table, $row->id, 'file', $row->file, 'files');
			}

			$this->context->count($type->label(), $rows->count(), Entry::ofType($type)->count());
		}
	}

	protected function status(string $value): ProjectStatus
	{
		return match ($value) {
			'Ausgeführt' => ProjectStatus::Executed,
			'In Planung' => ProjectStatus::Planned,
			'Studie' => ProjectStatus::Study,
		};
	}

	protected function competition(?string $value): ?Competition
	{
		return match ($value) {
			'1. Preis' => Competition::FirstPrize,
			'2. Preis' => Competition::SecondPrize,
			'Andere' => Competition::Other,
			default => null,
		};
	}

	protected function mapped(string $table): int
	{
		return \App\Models\LegacyMap::where('legacy_table', $table)->count();
	}
}
