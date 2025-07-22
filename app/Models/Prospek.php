<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Prospek extends Model
{
    protected $table = 'prospek';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama',
        'alamat',
        'no_telp',
    ];

    public $timestamps = false;


}
