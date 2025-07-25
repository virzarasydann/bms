<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'tgl_pembayaran',
        'id_project',
        'nominal',
        'catatan',
    ];

      protected $casts = [
        'nominal' => 'array',
    ];


}
