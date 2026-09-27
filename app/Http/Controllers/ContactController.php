<?php

namespace App\Http\Controllers;

use App\Models\Contact;

class ContactController extends Controller
{
	public function __invoke()
	{
		$contact = Contact::first();
		return view('pages.contact', compact('contact'));
	}
}
