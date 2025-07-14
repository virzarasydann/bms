<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MutasiSaldo extends Model
{
    use HasFactory;

    protected $table = 'mutasi_saldo';
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'rekening_asal',
        'rekening_tujuan',
        'nominal',
        'lampiran',
        'keterangan',
    ];

    public function asal()
    {
        return $this->belongsTo(Bank::class, 'rekening_asal');
    }

    public function tujuan()
    {
        return $this->belongsTo(Bank::class, 'rekening_tujuan');
    }
}
