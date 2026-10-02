<?php

/*
|--------------------------------------------------------------------------
| Media profiles
|--------------------------------------------------------------------------
|
| One profile per kind of media field: allowed file types (validated on
| upload) and crop ratios offered in the admin (label => width/height,
| null = free). Ratios follow the display formats of the legacy frontend.
| A profile with a single ratio locks the crop to it.
|
*/

return [

	'types' => [
		'image' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
		'video' => ['mp4', 'webm', 'mov'],
		'pdf' => ['pdf'],
	],

	'profiles' => [
		// project images/videos: grids, highlight slideshow, lightbox
		'project' => ['types' => ['image', 'video'], 'crops' => ['Frei' => null, 'Raster 3:2' => [687, 458], 'Raster hoch' => [687, 940], 'Highlight 16:10' => [16, 10]]],
		// team portrait: legacy delivers 2:3 (originals 667 × 1000, shown at 334 × 500;
		// the legacy width/height attributes 432 × 500 don't match the images)
		'portrait' => ['types' => ['image'], 'crops' => ['Portrait 2:3' => [2, 3]]],
		// press, awards, lectures (600 × 400)
		'entry' => ['types' => ['image'], 'crops' => ['3:2' => [3, 2]]],
		// content page images: Über uns, Jobs (960 × 650)
		'page' => ['types' => ['image'], 'crops' => ['Frei' => null, 'Seite' => [960, 650]]],
		// book cover and news image keep their natural ratio (masonry / news tile)
		'cover' => ['types' => ['image'], 'crops' => ['Frei' => null]],
		'news' => ['types' => ['image'], 'crops' => ['Frei' => null]],
		// social media share image (1200 × 630)
		'og' => ['types' => ['image'], 'crops' => ['Opengraph 1200×630' => [1200, 630]]],
		// PDF downloads
		'document' => ['types' => ['pdf'], 'crops' => []],
	],

];
