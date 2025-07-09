<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;
    protected $table = 'pengeluaran';

    protected $primaryKey = 'id';

    protected $fillable = [
        'tanggal_pengeluaran',
        'id_mustahik',
        'jumlah',
        'lampiran',
        'tipe',
        'no_pengeluaran',
        'deskripsi'

    ];

    public $timestamps = false;

    public function mustahik()
    {
        return $this->belongsTo(Mustahik::class, 'id_mustahik');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
    }

    public function detail()
{
    return $this->hasMany(PengeluaranDetail::class, 'id_pengeluaran');
}

}
