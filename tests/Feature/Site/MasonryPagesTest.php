<?php

use App\Models\Book;
use App\Models\Media;
use App\Models\Page;
use App\Models\TeamMember;

beforeEach(function () {
	$this->about = Page::factory()->create(['key' => 'about', 'title' => 'Über uns', 'text' => '<p>Gegründet 2015</p>']);
	Page::factory()->create(['key' => 'books']);
});

it('shows the about text, its images and the published team in order', function () {
	Media::factory()->create(['mediable_type' => 'page', 'mediable_id' => $this->about->id, 'width' => 1276, 'height' => 1277]);
	TeamMember::factory()->create(['firstname' => 'Roger', 'lastname' => 'Studerus', 'email' => 'rs@strut.ch', 'cv' => '<p>Lebenslauf RS</p>', 'publish' => true, 'sort_order' => 1]);
	TeamMember::factory()->create(['firstname' => 'Felix', 'lastname' => 'Rutishauser', 'email' => null, 'publish' => true, 'sort_order' => 0]);
	TeamMember::factory()->create(['firstname' => 'Entwurf', 'publish' => false]);

	$this->get('/ueber-uns')
		->assertOk()
		->assertSee('Gegründet 2015')
		->assertSee('data-lightbox="single"', false)
		->assertSeeInOrder(['Felix Rutishauser', 'Roger Studerus'])
		->assertSee('<a href="mailto:rs@strut.ch">Roger Studerus</a>', false)
		->assertSee('aria-controls="cv-', false)
		->assertSee('<p>Lebenslauf RS</p>', false)
		->assertDontSee('Entwurf');
});

it('lists published books with an order link by mail or URL', function () {
	Book::factory()->create(['title' => 'Leimenegg', 'description' => "Zeile 1\nZeile 2", 'url' => 'mail@strut.ch', 'publish' => true, 'sort_order' => 0]);
	Book::factory()->create(['title' => 'Peter Kunz', 'url' => 'https://www.quart.ch/produkt/peter-kunz/', 'publish' => true, 'sort_order' => 1]);
	Book::factory()->create(['title' => 'Entwurf', 'publish' => false]);

	$this->get('/buecher')
		->assertOk()
		->assertSeeInOrder(['Leimenegg', 'Peter Kunz'])
		->assertSee('Zeile 1<br />', false)
		->assertSee('href="mailto:mail@strut.ch?subject=Bestellung Leimenegg', false)
		->assertSee('href="https://www.quart.ch/produkt/peter-kunz/"', false)
		->assertDontSee('Entwurf');
});
