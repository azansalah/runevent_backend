<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
class Registration extends Model
{
    protected $connection = 'mysql';
    protected $table = 'registrations';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    
    public $timestamps = false;

    protected $fillable = [
        'id',
        'runner_id',
        'package_id',
        'register_date'
    ];
}