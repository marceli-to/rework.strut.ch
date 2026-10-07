<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Several pages shared the same meta description (taken over from legacy).
 * Gives each page its own text; descriptions already edited in the admin
 * are left alone.
 */
return new class extends Migration
{
	protected const OLD = [
		'contact' => 'Strut Architekten AG aus Winterthur, Schweiz. Gegründet im Jahre 2015 durch Roger Studerus, Felix Rutishauser und Peter Kunz.',
		'press' => 'Strut Architekten AG zeigt in verschieden Publikationen eine breite Palette an ausgeführten Gebäuden: Schulgebäude, Private Wohnbauten und Siedlungen, Produktions- und Verwaltungsgebäude.',
		'books' => 'Strut Architekten AG zeigt in verschieden Publikationen eine breite Palette an ausgeführten Gebäuden: Schulgebäude, Private Wohnbauten und Siedlungen, Produktions- und Verwaltungsgebäude.',
		'downloads' => 'Strut Architekten AG zeigt in verschieden Publikationen eine breite Palette an ausgeführten Gebäuden: Schulgebäude, Private Wohnbauten und Siedlungen, Produktions- und Verwaltungsgebäude.',
		'jobs' => 'Roger Studerus und Felix Rutishauser tragen gemeinsam die Verantwortung für die Strut Architekten AG. Im Team mit Peter Kunz werden eigenständige Projekte entwickelt, welche den Menschen ins Zentrum rücken.',
		'awards' => 'Roger Studerus und Felix Rutishauser tragen gemeinsam die Verantwortung für die Strut Architekten AG. Im Team mit Peter Kunz werden eigenständige Projekte entwickelt, welche den Menschen ins Zentrum rücken.',
		'lectures' => 'Roger Studerus und Felix Rutishauser tragen gemeinsam die Verantwortung für die Strut Architekten AG. Im Team mit Peter Kunz werden eigenständige Projekte entwickelt, welche den Menschen ins Zentrum rücken.',
	];

	protected const NEW = [
		'contact' => 'Kontakt zu Strut Architekten AG: Neuwiesenstrasse 69, 8400 Winterthur, Telefon +41 52 213 33 60, mail@strut.ch.',
		'press' => 'Presseberichte über Strut Architekten AG: Artikel in Fachzeitschriften wie TEC21, archi und Modulor sowie in Tageszeitungen zu Wohnbauten, Schulen und Gewerbebauten.',
		'books' => 'Bücher von Strut Architekten AG: Dokumentationen ausgewählter Bauten wie Leimenegg im Park in Winterthur, Sky-Frame in Frauenfeld und Casa da pégn in Flims.',
		'downloads' => 'Downloads von Strut Architekten AG: Projektdokumentationen zu Wohnbauten, Gewerbebauten und öffentlichen Bauten sowie die Werkliste als PDF.',
		'jobs' => 'Stellenangebote der Strut Architekten AG in Winterthur: Mitarbeit im Team an Wohnbauten, Industrie- und Gewerbebauten sowie öffentlichen Bauten.',
		'awards' => 'Auszeichnungen für Strut Architekten AG, darunter mehrere best architects awards, der Prix Acier 2016 und der arc Award 2014, etwa für Sky-Frame, Casa da pégn und Landenberg.',
		'lectures' => 'Vorträge von Strut Architekten AG an Hochschulen und Kongressen, unter anderem an der ETH Zürich, der Bauhaus-Universität Weimar sowie in Valencia, Teheran und Isfahan.',
	];

	public function up(): void
	{
		foreach (self::NEW as $key => $text) {
			DB::table('pages')
				->where('key', $key)
				->where(fn ($q) => $q->whereNull('meta_description')->orWhere('meta_description', self::OLD[$key]))
				->update(['meta_description' => $text, 'updated_at' => now()]);
		}
	}

	public function down(): void
	{
		foreach (self::OLD as $key => $text) {
			DB::table('pages')
				->where('key', $key)
				->where('meta_description', self::NEW[$key])
				->update(['meta_description' => $text, 'updated_at' => now()]);
		}
	}
};
