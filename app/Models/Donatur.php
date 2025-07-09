<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donatur extends Model
{
    use HasFactory;
    protected $table = 'donatur';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_lengkap',
        'jenis_kelamin',
        'alamat',
        'no_telp'
    ];

    public $timestamps = false;
}
