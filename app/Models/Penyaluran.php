<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penyaluran extends Model
{
    use HasFactory;
    protected $table = 'penyaluran';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_donasi_barang',
        'tgl_penyaluran',
        'jenis_penyaluran',
        'nama_penerima',
        'keterangan',
        'tipe',
    ];

    public $timestamps = false;
}
