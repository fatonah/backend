<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransUser extends Model
{
	protected $table = 'trans_user';
	protected $fillable = [
		'uid', 
		'type', 
		'crypto', 
		'category', 
		'using', 
		'status', 
		'error_code', 
		'recipient_id', 
		'recipient', 
		'txid',
		'confirmation', 
		'amount', 
		'before_bal', 
		'after_bal', 
		'myr_amount', 
		'rate', 
		'currency', 
		'netfee', 
		'walletfee', 
		'remarks',
		'time', 
		'timereceived',
		'txdate'
	];
}
