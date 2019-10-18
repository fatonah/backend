<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthDebugUsers extends Model
{
	protected $table = 'auth_users_debug';
	protected $fillable = [
		'id',
		'email'
	];
}
