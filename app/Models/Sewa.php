<?php

namespace App\Models;

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
}
