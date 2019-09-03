<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceCrypto extends Model
{
	protected $table = 'price_api';
	protected $fillable = [
		'name',
		'crypto',
		'price',
		'logo',
		'percentage',
		'url_api',
		'ip_getinfo',
	];
}
