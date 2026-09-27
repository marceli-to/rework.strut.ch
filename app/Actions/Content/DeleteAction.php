<?php

namespace App\Actions\Content;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Deletes a content model. Media and grid rows are cleaned up by the
 * HasMedia / HasGrid traits.
 */
class DeleteAction
{
	public function execute(Model $model): void
	{
		DB::transaction(fn () => $model->delete());
	}
}
