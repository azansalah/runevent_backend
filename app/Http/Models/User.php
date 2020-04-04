<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'f_name',
        'l_name',
        'username',
        'password',
        'email'
    ];

    protected $hidden = [
        'password'
    ];
}