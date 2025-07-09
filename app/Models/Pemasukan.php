<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;
    protected $table = 'pemasukan';

    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal_pemasukan',
        'id_donatur',
        'nominal',
        'lampiran',
        'kategori_penerimaan',
        'no_transaksi',
        'tipe',
        'deskripsi'
    ];

    public $timestamps = false;

    public function donatur()
    {
        return $this->belongsTo(Donatur::class, 'id_donatur');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPenerimaan::class, 'kategori_penerimaan');
    }

}
