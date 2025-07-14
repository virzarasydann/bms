<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    public $timestamps = false;

    protected $fillable = [
        'id_hutang',
        'id_piutang',
        'tanggal',
        'id_bank',
        'nominal',
        'lampiran',
        'id_kategori_transaksi',
        'keterangan',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank');
    }

    public function kategoriTransaksi()
    {
        return $this->belongsTo(KategoriTransaksi::class, 'id_kategori_transaksi');
    }
}
