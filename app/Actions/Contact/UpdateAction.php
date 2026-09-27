<?php

namespace App\Actions\Contact;

use App\Models\Contact;

class UpdateAction
{
	public function execute(Contact $contact, array $data): Contact
	{
		$contact->update($data);
		return $contact;
	}
}
