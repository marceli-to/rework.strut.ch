<?php

use App\Import\HtmlCleaner;
use App\Import\MediaCopier;
use App\Support\Slug;

it('removes ms word markup, spans and presentational attributes', function () {
	$html = '<p class="MsoNormal"><!-- [if gte mso 9]><xml><w:WordDocument><w:View>Normal</w:View></w:WordDocument></xml><![endif]--><span lang="DE" style="color: black;">Geistiges Vorbild</span></p><!--[if gte mso 10]><style>table{}</style><![endif]-->';

	expect((new HtmlCleaner)->html($html))->toBe('<p>Geistiges Vorbild</p>');
});

it('decodes text entities but keeps html syntax entities', function () {
	$html = '<p>Geb&auml;ude &ndash; 2015 &lt;neu&gt; &amp; mehr<br />zweite Zeile<\/p>';

	expect((new HtmlCleaner)->html($html))->toBe('<p>Gebäude – 2015 &lt;neu&gt; &amp; mehr<br>zweite Zeile</p>');
});

it('applies swiss orthography and counts replacements', function () {
	$clean = new HtmlCleaner;

	expect($clean->text('Straße &amp; Größe'))->toBe('Strasse & Grösse')
		->and($clean->eszett)->toBe(2);
});

it('returns null for empty values', function () {
	expect((new HtmlCleaner)->html('<p> </p>'))->toBeNull()
		->and((new HtmlCleaner)->text('  '))->toBeNull();
});

it('reproduces the legacy project slug algorithm', function () {
	expect(Slug::project('Lindenallee, Hofhaus-Ensemble', 'D-Köln', 2015))->toBe('lindenallee-hofhaus-ensemble-d-koeln-2015')
		->and(Slug::project('Casa da pégn', 'Flims', 2014))->toBe('casa-da-pegn-flims-2014');
});

it('strips the legacy upload prefix from file names', function () {
	expect(MediaCopier::originalName('5d8c78036f65b_strut.ch_strutkita02.jpg'))->toBe('strutkita02.jpg')
		->and(MediaCopier::originalName('5d961bb596db5_seiten-aus-arch2018-21.pdf'))->toBe('seiten-aus-arch2018-21.pdf');
});
