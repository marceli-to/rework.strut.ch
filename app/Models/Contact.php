<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
	use HasFactory, HasPublish, HasUuid;

	protected $table = 'contact';

	protected $fillable = [
		'uuid',
		'name',
		'address',
		'email',
		'phone',
		'maps_url',
		'imprint',
		'publish',
	];

	protected $casts = [
		'publish' => 'boolean',
	];
}
