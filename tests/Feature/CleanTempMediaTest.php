<?php

use Illuminate\Support\Facades\Storage;

it('deletes only abandoned temp uploads older than the threshold', function () {
	Storage::fake('public');
	$disk = Storage::disk('public');

	$disk->put('temp/old.jpg', 'x');
	$disk->put('temp/fresh.jpg', 'x');
	$disk->put('uploads/kept.jpg', 'x');
	touch($disk->path('temp/old.jpg'), now()->subHours(30)->getTimestamp());

	$this->artisan('media:clean-temp --dry-run')->assertSuccessful();
	$disk->assertExists('temp/old.jpg');

	$this->artisan('media:clean-temp')->assertSuccessful();

	$disk->assertMissing('temp/old.jpg');
	$disk->assertExists('temp/fresh.jpg');
	$disk->assertExists('uploads/kept.jpg');
});

it('is scheduled daily', function () {
	$event = collect(app(\Illuminate\Console\Scheduling\Schedule::class)->events())
		->first(fn ($event) => str_contains($event->command ?? '', 'media:clean-temp'));

	expect($event)->not->toBeNull()->and($event->expression)->toBe('30 3 * * *');
});
