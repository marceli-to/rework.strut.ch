<?php

use App\Models\GridItem;
use App\Models\GridRow;
use App\Models\Media;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;

beforeEach(function () {
	$this->home = Page::factory()->home()->create();
	$this->project = Project::factory()->create(['name' => 'Hofwiesenweg', 'location' => 'Winterthur', 'title' => null, 'publish' => true]);
});

function homeRow(Page $home, string $area, string $layout, int $sort = 0): GridRow
{
	return GridRow::factory()->create(['gridable_type' => 'page', 'gridable_id' => $home->id, 'area' => $area, 'layout' => $layout, 'sort_order' => $sort]);
}

it('shows the highlight slides with their project caption and link', function () {
	$media = Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $this->project->id, 'width' => 1600, 'height' => 1000]);
	GridItem::factory()->create(['grid_row_id' => homeRow($this->home, 'highlight', 'slideshow')->id, 'media_id' => $media->id]);

	$this->get('/')
		->assertOk()
		->assertSee('data-slideshow', false)
		->assertSee('href="' . $this->project->url . '"', false)
		->assertSee('Hofwiesenweg, Winterthur');
});

it('renders the grid with project tiles and published news only', function () {
	$row = homeRow($this->home, 'main', '3fr');
	$media = Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $this->project->id]);
	GridItem::factory()->create(['grid_row_id' => $row->id, 'position' => 0, 'media_id' => $media->id]);
	GridItem::factory()->create(['grid_row_id' => $row->id, 'position' => 1, 'media_id' => null, 'news_id' => News::factory()->create(['title' => '1. Rang', 'link_url' => 'https://example.com', 'link_label' => 'mehr lesen'])->id]);
	GridItem::factory()->create(['grid_row_id' => $row->id, 'position' => 2, 'media_id' => null, 'news_id' => News::factory()->create(['title' => 'Entwurf', 'publish' => false])->id]);

	$this->get('/')
		->assertSee('pt-[136.888888888888889%]', false)
		->assertSee('1. Rang')
		->assertSee('target="_blank"', false)
		->assertSee('mehr lesen')
		->assertDontSee('Entwurf')
		->assertDontSee('data-slideshow', false);
});
