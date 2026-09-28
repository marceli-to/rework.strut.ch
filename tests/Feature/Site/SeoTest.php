<?php

use App\Models\Category;
use App\Models\CategoryType;
use App\Models\Media;
use App\Models\Page;
use App\Models\Project;

beforeEach(function () {
	$this->press = Page::factory()->create(['key' => 'press', 'meta_description' => 'Publikationen von Strut']);
	Page::factory()->create(['key' => 'works', 'meta_description' => 'Werkliste von Strut']);
});

it('renders title, description, canonical and the default Opengraph image', function () {
	$this->get('/presse')
		->assertSee('<title>Presse - Strut Architekten</title>', false)
		->assertSee('<meta name="description" content="Publikationen von Strut">', false)
		->assertSee('<link rel="canonical" href="' . url('/presse') . '">', false)
		->assertSee('<meta property="og:image" content="' . asset('img/strut-og.png') . '">', false);
});

it('uses the page Opengraph image from the admin', function () {
	Media::factory()->create(['mediable_type' => 'page', 'mediable_id' => $this->press->id, 'collection' => 'og', 'file' => 'og.jpg', 'crop' => ['w' => 1200, 'h' => 630, 'x' => 10, 'y' => 20]]);

	$this->get('/presse')->assertSee('og.jpg?w=1200&amp;h=630&amp;fit=crop&amp;fm=jpg&amp;q=85&amp;crop=1200%2C630%2C10%2C20', false);
});

it('points all Werkliste views to /werkliste', function () {
	$this->get('/werkliste/jahr')->assertSee('<link rel="canonical" href="' . route('page.works') . '">', false);
});

it('gives projects their own title, description and the flagged Opengraph image', function () {
	$type = CategoryType::factory()->for(Category::factory())->create(['name_singular' => 'Wohnhaus']);
	$project = Project::factory()->for($type)->create(['name' => 'Hofwiesenweg', 'location' => 'Winterthur', 'description' => '<p>Drei Häuser</p>', 'meta_description' => null, 'publish' => true]);
	Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $project->id, 'file' => 'erstes.jpg']);
	Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $project->id, 'file' => 'og-bild.jpg', 'is_og' => true]);

	$this->get($project->url)
		->assertSee('<title>Hofwiesenweg, Winterthur - Wohnhaus - Strut Architekten</title>', false)
		->assertSee('<meta name="description" content="Drei Häuser">', false)
		->assertSee('og-bild.jpg?w=1200', false)
		->assertSee('<link rel="canonical" href="' . url($project->url) . '">', false);
});

it('lists the pages and detailed projects in the sitemap', function () {
	$project = Project::factory()->create(['publish' => true, 'has_detail' => true]);
	$hidden = Project::factory()->create(['publish' => true, 'has_detail' => false]);

	$this->get('/sitemap.xml')
		->assertOk()
		->assertHeader('Content-Type', 'application/xml')
		->assertSee('<loc>' . route('page.press') . '</loc>', false)
		->assertSee('<loc>' . url($project->url) . '</loc>', false)
		->assertDontSee(url($hidden->url), false);
});

it('allows indexing only in production', function () {
	$this->get('/robots.txt')->assertOk()->assertSee('Disallow: /');

	app()->detectEnvironment(fn () => 'production');
	$this->get('/robots.txt')->assertSee('Sitemap: ' . route('sitemap'))->assertDontSee('Disallow: /');
});
