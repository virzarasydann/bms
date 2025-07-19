<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sewa extends Model
{
    protected $table = 'sewa';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_kategori_sewa',
        'nama_layanan',
        'email',
        'password',
        'tgl_sewa',
        'tgl_expired',
        'vendor',
        'url_vendor',
    ];

    public $timestamps = false;

    public function kategori()
    {
        return $this->belongsTo(KategoriSewa::class, 'id_kategori_sewa');
    }


    public function scopeExpiringSoon($query)
    {
        $today = Carbon::today();
        $limit = Carbon::today()->addDays(50);

        return $query->whereBetween('tgl_expired', [$today, $limit]);
    }
}
