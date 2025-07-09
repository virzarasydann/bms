<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;
    protected $table = 'pengajuan';

    protected $primaryKey = 'id';

    protected $fillable = [
        'tgl_pengajuan',
        'nama_lengkap',
        'alamat',
        'no_telp',
        'permasalahan',
        'penyelesaian',
        'nama_perekomendasi',
        'stt_pengajuan',
        'no_pengajuan'
    ];

    public $timestamps = false;

}
