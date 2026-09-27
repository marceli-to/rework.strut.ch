<?php

namespace App\Http\Controllers\Api;

use App\Actions\Contact\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;

class KontaktController extends Controller
{
	public function show()
	{
		return new ContactResource(Contact::firstOrFail());
	}

	public function update(UpdateContactRequest $request)
	{
		$contact = Contact::firstOrFail();
		$contact = (new UpdateAction)->execute($contact, $request->validated());
		return new ContactResource($contact);
	}
}
