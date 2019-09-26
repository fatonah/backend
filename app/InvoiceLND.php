<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceLND extends Model
{
	protected $table = 'invoice_lnd';
	protected $fillable = [
		'id',
		'uid',
		'hash',
		'amount',
		'expired',
		'date_expired',
		'memo',
		'status',
		'txid',
	];
}
