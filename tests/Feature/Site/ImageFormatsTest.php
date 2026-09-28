<?php

use App\Models\Media;
use App\Models\Page;
use App\Models\Project;
use App\Support\ImageSupport;
use Illuminate\Support\Facades\File;

it('adds the format and its quality to the Glide params', function () {
	$media = Media::factory()->make(['file' => 'a.jpg', 'width' => 2500, 'height' => 1667, 'crop' => null]);

	expect($media->imageUrl('lg'))->toBe('/img/uploads/a.jpg?w=1600&h=1067&fit=stretch')
		->and($media->imageUrl('lg', 'webp'))->toBe('/img/uploads/a.jpg?w=1600&h=1067&fit=stretch&fm=webp&q=80')
		->and($media->imageUrl('lg', 'avif'))->toBe('/img/uploads/a.jpg?w=1600&h=1067&fit=stretch&fm=avif&q=70');
});

it('renders images as <picture> with the modern formats the server supports', function () {
	$page = Page::factory()->create(['key' => 'jobs', 'text' => '<p>Text</p>']);
	Media::factory()->create(['mediable_type' => 'page', 'mediable_id' => $page->id, 'file' => 'buero.jpg', 'width' => 2000, 'height' => 2022]);

	$html = $this->get('/jobs')->assertOk()->getContent();

	expect($html)->toContain('<picture class="contents">')
		->and($html)->toContain('src="/img/uploads/buero.jpg?w=791&amp;h=800&amp;fit=stretch"');

	foreach (ImageSupport::modernFormats() as $format) {
		expect($html)->toContain('<source type="image/' . $format . '" srcset="/img/uploads/buero.jpg?w=791&amp;h=800&amp;fit=stretch&amp;fm=' . $format)
			->and($html)->toContain('data-' . $format . '="/img/uploads/buero.jpg?w=1088&amp;h=1100');
	}
});

it('warms the Glide cache for every size and format in use', function () {
	$project = Project::factory()->create();
	File::ensureDirectoryExists(storage_path('app/public/uploads'));
	$image = new Imagick();
	$image->newImage(40, 30, 'gray');
	$image->setImageFormat('jpeg');
	$image->writeImage(storage_path('app/public/uploads/warm-test.jpg'));
	Media::factory()->create(['mediable_type' => 'project', 'mediable_id' => $project->id, 'file' => 'warm-test.jpg', 'width' => 40, 'height' => 30]);

	try {
		$this->artisan('images:warm', ['--format' => ['original', 'webp']])
			->expectsOutputToContain('1 images done.')
			->assertSuccessful();

		expect(File::files(storage_path('app/.glide-cache/uploads/warm-test.jpg')))->toHaveCount(2);
	} finally {
		File::delete(storage_path('app/public/uploads/warm-test.jpg'));
		File::deleteDirectory(storage_path('app/.glide-cache/uploads/warm-test.jpg'));
	}
});
