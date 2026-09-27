<?php

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

class DeleteAction
{
	public function execute(Media $media): void
	{
		Storage::disk('public')->delete('uploads/' . $media->file);

		$media->delete();
	}
}
