<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalletAddress extends Model
{
	protected $table = 'wallet_address';
	protected $fillable = [
		'uid',
		'label',
		'address',
		'balance',
		'crypto', 
		'title', 
		'value_display',
	];
}
