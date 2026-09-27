<?php

/*
|--------------------------------------------------------------------------
| Grids
|--------------------------------------------------------------------------
|
| One definition for the project grid and the homepage grid. The same spec
| drives validation, the admin editor (GridEditor.vue) and the Blade grid.
|
| A layout is a list of columns; each column has a width (fr) and a stack of
| cells. Slot positions are numbered column by column, top to bottom
| (legacy numbering). A `spacer` cell takes space but no position.
| A layout with `slots => null` takes any number of items (slideshow).
|
| `ratio` = height in % of the width (legacy padding-top boxes).
|
*/

$cell = fn (string $size, bool $news = false) => ['size' => $size, 'news' => $news];

return [

	'contexts' => [

		'project' => [
			'label' => 'Projekt',
			'media' => 'own', // images/videos of the owning project
			'areas' => [
				'main' => [
					'label' => 'Bilder',
					'layouts' => ['2fr', '1fr_stacked-1fr', '1fr-1fr_stacked', '1fr_sm_lg-1fr_lg_sm', '1fr_lg_sm-1fr_sm_lg', '1fr_sm_lg-1fr_lg', '1fr_lg-1fr_sm_lg'],
				],
			],
		],

		'home' => [
			'label' => 'Startseite',
			'media' => 'published_projects', // images/videos of any published project
			'areas' => [
				'highlight' => [
					'label' => 'Highlights',
					'layouts' => ['slideshow'],
					'max_rows' => 1,
				],
				'main' => [
					'label' => 'Raster',
					'layouts' => ['1fr', '2fr', '3fr', '3fr-landscape', '2fr-1fr', '1fr-2fr', '2fr-1fr_stacked', '1fr_stacked-2fr', '1fr-1fr-1fr_stacked', '1fr-1fr_stacked-1fr', '1fr_stacked-1fr-1fr'],
				],
			],
		],

	],

	'sizes' => [
		// project grid
		'sm' => 66.6667, // 687 × 458
		'lg' => 136.8268, // 687 × 940
		// homepage grid
		'a' => 66.6667,
		'b' => 66.6667,
		'c' => 68.4444,
		'd' => 68.4444,
		'e' => 136.8889,
	],

	'layouts' => [

		'project' => [
			'2fr' => ['label' => 'Zwei Spalten', 'columns' => [
				['fr' => 1, 'cells' => [$cell('sm')]],
				['fr' => 1, 'cells' => [$cell('sm')]],
			]],
			'1fr_stacked-1fr' => ['label' => 'Links gestapelt, rechts hoch', 'columns' => [
				['fr' => 1, 'cells' => [$cell('sm'), $cell('sm')]],
				['fr' => 1, 'cells' => [$cell('lg')]],
			]],
			'1fr-1fr_stacked' => ['label' => 'Links hoch, rechts gestapelt', 'columns' => [
				['fr' => 1, 'cells' => [$cell('lg')]],
				['fr' => 1, 'cells' => [$cell('sm'), $cell('sm')]],
			]],
			'1fr_sm_lg-1fr_lg_sm' => ['label' => 'Links klein/hoch, rechts hoch/klein', 'columns' => [
				['fr' => 1, 'cells' => [$cell('sm'), $cell('lg')]],
				['fr' => 1, 'cells' => [$cell('lg'), $cell('sm')]],
			]],
			'1fr_lg_sm-1fr_sm_lg' => ['label' => 'Links hoch/klein, rechts klein/hoch', 'columns' => [
				['fr' => 1, 'cells' => [$cell('lg'), $cell('sm')]],
				['fr' => 1, 'cells' => [$cell('sm'), $cell('lg')]],
			]],
			'1fr_sm_lg-1fr_lg' => ['label' => 'Links klein/hoch, rechts hoch', 'columns' => [
				['fr' => 1, 'cells' => [$cell('sm'), $cell('lg')]],
				['fr' => 1, 'cells' => [$cell('lg'), $cell('spacer')]],
			]],
			'1fr_lg-1fr_sm_lg' => ['label' => 'Links hoch, rechts klein/hoch', 'columns' => [
				['fr' => 1, 'cells' => [$cell('lg'), $cell('spacer')]],
				['fr' => 1, 'cells' => [$cell('sm'), $cell('lg')]],
			]],
		],

		'home' => [
			'slideshow' => ['label' => 'Slideshow', 'slots' => null, 'columns' => []],
			'1fr' => ['label' => 'Volle Breite', 'columns' => [
				['fr' => 1, 'cells' => [$cell('a')]],
			]],
			'2fr' => ['label' => 'Zwei Spalten', 'columns' => [
				['fr' => 1, 'cells' => [$cell('b')]],
				['fr' => 1, 'cells' => [$cell('b')]],
			]],
			'3fr' => ['label' => 'Drei Spalten hoch', 'columns' => [
				['fr' => 1, 'cells' => [$cell('e', true)]],
				['fr' => 1, 'cells' => [$cell('e', true)]],
				['fr' => 1, 'cells' => [$cell('e', true)]],
			]],
			'3fr-landscape' => ['label' => 'Drei Spalten quer', 'columns' => [
				['fr' => 1, 'cells' => [$cell('b', true)]],
				['fr' => 1, 'cells' => [$cell('b', true)]],
				['fr' => 1, 'cells' => [$cell('b', true)]],
			]],
			'2fr-1fr' => ['label' => 'Breit + schmal', 'columns' => [
				['fr' => 2, 'cells' => [$cell('d')]],
				['fr' => 1, 'cells' => [$cell('e', true)]],
			]],
			'1fr-2fr' => ['label' => 'Schmal + breit', 'columns' => [
				['fr' => 1, 'cells' => [$cell('e', true)]],
				['fr' => 2, 'cells' => [$cell('d')]],
			]],
			'2fr-1fr_stacked' => ['label' => 'Breit + gestapelt', 'columns' => [
				['fr' => 2, 'cells' => [$cell('d')]],
				['fr' => 1, 'cells' => [$cell('c', true), $cell('c', true)]],
			]],
			'1fr_stacked-2fr' => ['label' => 'Gestapelt + breit', 'columns' => [
				['fr' => 1, 'cells' => [$cell('c', true), $cell('c', true)]],
				['fr' => 2, 'cells' => [$cell('d')]],
			]],
			'1fr-1fr-1fr_stacked' => ['label' => 'Hoch, hoch, gestapelt', 'columns' => [
				['fr' => 1, 'cells' => [$cell('e', true)]],
				['fr' => 1, 'cells' => [$cell('e', true)]],
				['fr' => 1, 'cells' => [$cell('c', true), $cell('c', true)]],
			]],
			'1fr-1fr_stacked-1fr' => ['label' => 'Hoch, gestapelt, hoch', 'columns' => [
				['fr' => 1, 'cells' => [$cell('e', true)]],
				['fr' => 1, 'cells' => [$cell('c', true), $cell('c', true)]],
				['fr' => 1, 'cells' => [$cell('e', true)]],
			]],
			'1fr_stacked-1fr-1fr' => ['label' => 'Gestapelt, hoch, hoch', 'columns' => [
				['fr' => 1, 'cells' => [$cell('c', true), $cell('c', true)]],
				['fr' => 1, 'cells' => [$cell('e', true)]],
				['fr' => 1, 'cells' => [$cell('e', true)]],
			]],
		],

	],

];
