<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Piutang extends Model
{
    use HasFactory;

    protected $table = 'piutang';
    public $timestamps = false;

    protected $fillable = [
        'id_invoice',
        'tanggal_piutang',
        'id_bank',
        'deskripsi',
        'nominal',
        'lampiran',
        'status',
        'terbayar',
        'sisa_bayar',
        'tgl_pelunasan',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank');
    }
}
