<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutupBuku extends Model
{
    use HasFactory;
    protected $table = 'tutup_buku_bulanan';

    protected $primaryKey = 'id';

    protected $fillable = [
        'bulan',
        'tahun',
        'id_penerimaan',
        'saldo',
        'tipe'
        
    ];

    public $timestamps = false;
}
