<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KategoriTransaksi extends Model
{
    use HasFactory;

    protected $table = 'kategori_transaksi';
    public $timestamps = false;

    protected $fillable = [
        'nama_kategori',
        'jenis_transaksi',
    ];
}
