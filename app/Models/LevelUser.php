<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LevelUser extends Model
{
     use HasFactory;

    protected $table = 'level_user';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [

    ];
}
