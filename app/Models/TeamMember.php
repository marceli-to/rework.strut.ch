<?php

namespace App\Models;

use App\Traits\HasPublish;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
	use HasFactory, HasPublish, HasUuid;

	protected $fillable = [
		'uuid', 
    'firstname', 
    'name', 
    'title', 
    'email', 
    'cv',
		'publish', 
    'former', 
    'sort_order',
	];

	protected $casts = [
		'publish' => 'boolean',
		'former' => 'boolean',
		'sort_order' => 'integer',
	];
}
