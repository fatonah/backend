<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuTicket extends Model
{
	protected $table = 'menuticket';
	protected $fillable = [
		'title',
		'id',
		];
	}
