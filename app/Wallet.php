<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
	protected $table = 'wallet';
	protected $fillable = [
		'uid',
		'address',
		'balance',
		'crypto', 
	];
}
