<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
	protected $table = 'messages';
	protected $fillable = [
		'id',
		'ticket_id',
		'uid',
		'typeP', 
		'attachment',
		'contents',
		'created_at',
		'updated_at',
		];
	}
