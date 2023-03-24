<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use  HasFactory, Notifiable;
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'registered_timestamp',
        'last_login_timestamp'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
