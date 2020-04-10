<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;
    protected $connection = 'mysql';
    protected $table = 'packages';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    //public $timestamps = false;

    protected $fillable = [
        'id',
        'event_id',
        'name',
        'date',
        'time',
        'price',
        'is_limit',
        'limit_count',
        'created_at',
        'updated_at',
        'deleted_at' 
    ];
}