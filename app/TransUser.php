<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransUser extends Model
{
	protected $table = 'trans_lnd';
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
		'myr_amt', 
		'rate', 
		'currency', 
		'netfee', 
		'wallet_fee', 
		'remarks', 
		'timestamp'
	];
}
