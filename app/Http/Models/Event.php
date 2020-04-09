<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $connection = 'mysql';
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    // public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'location',
        'created_at',
        'updated_at',
        'deleted_at'       
    ];
}