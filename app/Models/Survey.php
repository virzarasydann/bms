<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;
    protected $table = 'survey';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_pengajuan',
        'tgl_survey',
        'nama_lengkap',
        'nik',
        'alamat',
        'tempat_lahir',
        'tgl_lahir',
        'usia',
        'jenis_kelamin',
        'status',
        'pekerjaan',
        'penghasilan',
        'no_hp',
        'lama_tinggal',
        'stt_tempat_tinggal',
        'membantu',
        'nama_lembaga_membantu',
        'orang_terdekat',
        'masalah',
        'jumlah_tanggungan',
        'usaha_dilakukan',
        'pengeluaran_bulan',
        'tabungan',
        'hutang',
        'jumlah_hutang',
        'harapan_bantuan',
        'bersedia_kajian_islam',
        'no_survey'
    ];

    public $timestamps = false;

}
