<?php

namespace App\Http\Controllers;

use App\Models\AtelierPage;
use App\Models\TeamMember;
use App\Models\JobListing;

class AtelierController extends Controller
{
	public function profile()
	{
		$profile = AtelierPage::with('media')->where('slug', 'profile')->first();
		return view('pages.atelier.profile', compact('profile'));
	}

	public function team()
	{
		$page = AtelierPage::with('media')->where('slug', 'team')->first();
		$members = TeamMember::published()->orderBy('sort_order')->get()->groupBy(fn ($m) => $m->former ? 'former' : 'current');
		return view('pages.atelier.team', compact('page', 'members'));
	}

	public function jobs()
	{
		$page = AtelierPage::with('media')->where('slug', 'jobs')->first();
		$jobs = JobListing::published()->orderBy('sort_order')->get();
		return view('pages.atelier.jobs', compact('page', 'jobs'));
	}
}
