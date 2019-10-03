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
		'value_display',
		'logo',
		'appear',
		'percentage',
		'url_api',
		'url_img',
		'ip_getinfo',
		'id_gecko',
	];
}
