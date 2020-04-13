<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
class Runner extends Model
{
    protected $connection = 'mysql';
    protected $table = 'runners';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id',
        'card_no',
        't_name',
        'f_name',
        'l_name',
        'telephone',
        'email',
        'created_at',
        'updated_at'
    ];
}