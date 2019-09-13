<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'name', 
        'label', 
        'username', 
        'email', 
        'email_hash', 
        'phone', 
        'remember_token',
        'ip', 
        'password', 
		'noic',
		'secretpin',
		'email_verify',
		'phone_verify',
		'currency',
		'status',
		'google_auth_code',
		'power_pin',
		'power_auth',
		'power_fp',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
 
}
