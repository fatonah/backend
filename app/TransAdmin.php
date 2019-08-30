<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransAdmin extends Model
{
	protected $table = 'trans_admin';
	protected $fillable = [
		'id',
		'uid',
		'account', 
		'toAddress', 
		'status', 
		'crypto', 
		'amount',
		'balBefore',
		'created_at',
		'updated_at',
		];
	}
