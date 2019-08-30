<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
	protected $table = 'tickets';
	protected $fillable = [
		'id',
		'type',
		'subject', 
		'details', 
		'uid', 
		'status', 
		'created_at',
		'updated_at',
		];
	}
