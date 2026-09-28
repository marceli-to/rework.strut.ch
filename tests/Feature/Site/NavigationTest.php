<?php

use App\Actions\Site\GetNavigation;
use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Project;

beforeEach(function () {
	$this->category = Category::factory()->create(['name' => 'Wohnen']);
	$this->type = CategoryType::factory()->for($this->category)->create(['name_plural' => 'Wohnhäuser']);
	$this->project = Project::factory()->for($this->type)->create(['name' => 'Hofwiesenweg', 'location' => 'Winterthur', 'publish' => true]);
});

it('lists only published projects with a detail page', function () {
	Project::factory()->for($this->type)->create(['name' => 'Entwurf', 'publish' => false]);
	Project::factory()->for($this->type)->create(['name' => 'Ohne Detail', 'publish' => true, 'has_detail' => false]);

	$types = app(GetNavigation::class)->execute('page.press')['projects']['categories'][0]['types'];

	expect($types)->toHaveCount(1)
		->and(array_column($types[0]['projects'], 'label'))->toBe(['Hofwiesenweg, Winterthur']);
});

it('drops types without projects and unpublished categories', function () {
	CategoryType::factory()->for($this->category)->create(['name_plural' => 'Leer']);
	Category::factory()->create(['name' => 'Versteckt', 'publish' => false]);

	$categories = app(GetNavigation::class)->execute(null)['projects']['categories'];

	expect(array_column($categories, 'label'))->toBe(['Wohnen'])
		->and(array_column($categories[0]['types'], 'label'))->toBe(['Wohnhäuser']);
});

it('marks the current project, its type and category as active', function () {
	$nav = app(GetNavigation::class)->execute('page.project', $this->project);
	$category = $nav['projects']['categories'][0];

	expect($nav['projects']['active'])->toBeTrue()
		->and($category['active'])->toBeTrue()
		->and($category['types'][0]['active'])->toBeTrue()
		->and($category['types'][0]['projects'][0]['active'])->toBeTrue();
});

it('marks only /werkliste itself as active, like the legacy site', function (string $route, bool $active) {
	expect(app(GetNavigation::class)->execute($route)['works']['active'])->toBe($active);
})->with([
	['page.works', true],
	['page.works.status', false],
	['page.works.year', false],
]);

it('marks the section of the current page as active', function () {
	$nav = app(GetNavigation::class)->execute('page.books');

	expect($nav['publications']['active'])->toBeTrue()
		->and(collect($nav['publications']['links'])->firstWhere('active')['label'])->toBe('Bücher')
		->and($nav['about']['active'])->toBeFalse();
});

it('renders the navigation on public pages', function () {
	$this->get($this->project->url)
		->assertOk()
		->assertSee('data-menu', false)
		->assertSee('Hofwiesenweg, Winterthur')
		->assertSee('aria-current="page"', false);
});
