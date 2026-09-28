<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Maps legacy strut.ch rows to new models (idempotent import).
 */
class LegacyMap extends Model
{
	protected $table = 'legacy_map';

	protected $fillable = [
		'legacy_table',
		'legacy_id',
		'legacy_column',
		'legacy_file',
		'model_type',
		'model_id',
	];
}
