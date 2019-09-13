<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	protected $table = 'settings';
	protected $fillable = [
		'title',
		'description',
		'keywords',
		'name',
		'infoemail',
		'supportemail',
		'url',
		'commission_withdraw', 
		'fee_btc',
		'fee_bch',
		'fee_doge',
		'template_email',
		'url_img_ocean',
		'url_gecko',
		];
	}
