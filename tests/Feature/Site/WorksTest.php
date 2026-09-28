<?php

use App\Actions\Site\GetWorks;
use App\Enums\Competition;
use App\Enums\ProjectStatus;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Page;
use App\Models\Project;

beforeEach(function () {
	Page::factory()->create(['key' => 'works', 'meta_description' => 'Werkliste Beschreibung']);
	$this->type = CategoryType::factory()->create(['name_plural' => 'Wohnhäuser']);
});

function work(array $attributes = []): Project
{
	return Project::factory()->for(test()->type)->create($attributes + ['publish' => true]);
}

it('groups by status, and lists competitions by prize, newest first', function () {
	work(['name' => 'Alt', 'year' => 2010, 'competition' => Competition::FirstPrize]);
	work(['name' => 'Neu', 'year' => 2020, 'status' => ProjectStatus::Study, 'competition' => Competition::FirstPrize]);
	work(['name' => 'Geplant', 'status' => ProjectStatus::Planned]);
	work(['name' => 'Entwurf', 'publish' => false]);

	$works = app(GetWorks::class)->byStatus();

	expect(array_keys($works['status']))->toBe(['executed', 'planned', 'study'])
		->and($works['status']['executed']['projects']->pluck('name')->all())->toBe(['Alt'])
		->and(array_keys($works['competition']))->toBe(['first_prize'])
		->and($works['competition']['first_prize']['projects']->pluck('name')->all())->toBe(['Neu', 'Alt']);
});

it('orders by year, then name, in three columns of whole years', function () {
	foreach ([[2024, 'B'], [2024, 'A'], [2023, 'C'], [2022, 'D'], [2021, 'E']] as [$year, $name]) {
		work(['year' => $year, 'name' => $name]);
	}

	$columns = app(GetWorks::class)->byYear();

	expect($columns->map(fn ($years) => $years->keys()->all())->all())->toBe([[2024, 2023], [2022, 2021]])
		->and($columns[0][2024]->pluck('name')->all())->toBe(['A', 'B']);
});

it('leaves out types and categories without published projects', function () {
	work(['name' => 'Sichtbar']);
	CategoryType::factory()->for($this->type->category)->create(['name_plural' => 'Leer']);
	Category::factory()->create(['name' => 'Ohne Projekte']);

	$categories = app(GetWorks::class)->byType();

	expect($categories->pluck('name')->all())->toBe([$this->type->category->name])
		->and($categories[0]->types->pluck('name_plural')->all())->toBe(['Wohnhäuser']);
});

it('renders each view with its tab active and its PDF link', function (string $url, string $label, string $pdf) {
	work(['name' => 'Mit Detail', 'location' => 'Winterthur']);
	work(['name' => 'Ohne Detail', 'location' => 'Brütten', 'has_detail' => false]);

	$response = $this->get($url)->assertOk();

	expect($response->getContent())->toMatch('#aria-current="page"[^>]*>' . $label . '</a>#')
		->and(substr_count($response->getContent(), 'data-active aria-current="page"'))->toBe(1);

	$response
		->assertSee('href="' . url("/werkliste/pdf/{$pdf}") . '"', false)
		->assertSee('Mit Detail, Winterthur</a>', false)
		->assertSee('Ohne Detail, Brütten')
		->assertDontSee('Ohne Detail, Brütten</a>', false);
})->with([
	['/werkliste', 'Status', 'status'],
	['/werkliste/status', 'Status', 'status'],
	['/werkliste/jahr', 'Jahr', 'jahr'],
	['/werkliste/typ', 'Typ', 'typ'],
]);
