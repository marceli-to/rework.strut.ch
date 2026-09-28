<?php

namespace App\Http\Controllers\Site;

use App\Actions\Site\GetHome;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
	public function __invoke(GetHome $action): View
	{
		return view('pages.home', $action->execute());
	}
}
