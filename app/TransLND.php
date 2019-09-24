<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransLND extends Model
{
	protected $table = 'trans_lnd';
	protected $fillable = [
		'id','uid','type','invoice_id','before_bal','after_bal','status','error_code','txid','amount',
		'myr_amount','rate','currency','recipient','netfee','walletfee',
		'category','using','remarks',
	];
}
