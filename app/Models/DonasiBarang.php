<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonasiBarang extends Model
{
    use HasFactory;
    protected $table = 'donasi_barang';

    protected $primaryKey = 'id';

    protected $fillable = [
        'tgl_donasi',
        'nama_barang',
        'jumlah',
        'satuan',
        'nama_donatur',
        'keterangan',
    ];

    public $timestamps = false;
}
