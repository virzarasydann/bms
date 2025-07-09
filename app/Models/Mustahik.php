<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mustahik extends Model
{
    use HasFactory;
    protected $table = 'mustahik';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_lengkap',
        'alamat',
        'jenis_kelamin',
        'no_telp',
        'nik',
    ];

    public $timestamps = false;
}
