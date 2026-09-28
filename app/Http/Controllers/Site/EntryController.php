<?php

namespace App\Http\Controllers\Site;

use App\Actions\Site\GetEntries;
use App\Enums\EntryType;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Presse, Auszeichnungen and Vorträge: one list, the type comes from the route.
 */
class EntryController extends Controller
{
	private const PAGES = [
		'press' => 'press',
		'award' => 'awards',
		'lecture' => 'lectures',
	];

	public function __invoke(GetEntries $entries, string $type): View
	{
		$type = EntryType::from($type);

		return view('pages.entries', [
			'type' => $type,
			'page' => Page::findByKey(self::PAGES[$type->value]),
			'columns' => $entries->execute($type),
		]);
	}
}
