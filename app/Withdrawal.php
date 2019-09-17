<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
	protected $table = 'withdrawal';
	protected $fillable = [
		'uid',
		'status',
		'amount',
		'before_bal',
		'after_bal',
		'myr_amount',
		'rate',
		'currency',
		'recipient_id',
		'recipient',
		'netfee', 
		'walletfee', 
		'txid',
		'crypto',
		'using',
		'type',
		'remarks'
	];
}
