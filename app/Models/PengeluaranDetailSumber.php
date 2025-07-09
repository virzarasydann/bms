<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengeluaranDetailSumber extends Model
{
    protected $table = 'pengeluaran_detail_sumber';
    protected $fillable = [
        'pengeluaran_detail_id',
        'kategori_pemasukan_id',
        'nominal',
    ];

    public $timestamps = false;
    public function detail()
    {
        return $this->belongsTo(PengeluaranDetail::class, 'pengeluaran_detail_id');
    }

    public function kategoriPemasukan()
{
    return $this->belongsTo(KategoriPenerimaan::class, 'kategori_pemasukan_id');
}

}
