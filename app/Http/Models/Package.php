<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $connection = 'mysql';
    protected $table = 'packages';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'event_id',
        'name',
        'time',
        'price',
        'is_limit',
        'limit_count'
    ];
}