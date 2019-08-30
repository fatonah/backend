<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Appver extends Model
{
	protected $table = 'app_version';
	protected $fillable = [
		'version',
		'ios_version',
		'ios_version2',
	];
}
