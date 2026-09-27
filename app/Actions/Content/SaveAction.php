<?php

namespace App\Actions\Content;

use App\Actions\Media\AttachAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Create or update any content model; attaches uploaded media (if any).
 */
class SaveAction
{
	public function execute(Model $model, array $data): Model
	{
		$media = $data['media'] ?? [];
		unset($data['media']);

		return DB::transaction(function () use ($model, $data, $media) {
			$model->fill($data)->save();

			if (!empty($media)) {
				(new AttachAction)->execute($media, $model);
			}

			return $model->fresh();
		});
	}
}
