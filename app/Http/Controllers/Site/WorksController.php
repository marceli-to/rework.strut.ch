<?php

namespace App\Http\Controllers\Site;

use App\Actions\Site\GetWorks;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Werkliste by status (also /werkliste), year and type.
 */
class WorksController extends Controller
{
	public function __construct(private GetWorks $works) {}

	public function status(): View
	{
		return $this->view('status', $this->works->byStatus());
	}

	public function year(): View
	{
		return $this->view('year', ['columns' => $this->works->byYear()]);
	}

	public function type(): View
	{
		return $this->view('type', ['categories' => $this->works->byType()]);
	}

	private function view(string $by, array $data): View
	{
		return view("pages.works.{$by}", $data + ['by' => $by, 'page' => Page::findByKey('works')]);
	}
}
