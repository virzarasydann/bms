<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranDetail extends Model
{
    use HasFactory;
    protected $table = 'pengeluaran_detail';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id_pengeluaran',
        'nominal',
        'kategori_pengeluaran_id',
    ];

    public $timestamps = false;

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'id_pengeluaran');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_pengeluaran_id');
    }

    public function sumberDana()
{
    return $this->hasMany(PengeluaranDetailSumber::class, 'pengeluaran_detail_id');
}
}
