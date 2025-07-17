<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataAkun extends Model
{
    protected $table = 'data_akun';

    protected $fillable = [
        'nama_akun',
        'user_id',
        'password',
    ];

    public $timestamps = false;
}
