<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriSewa extends Model
{
    protected $table = 'kategori_sewa';

    protected $primaryKey = 'id';

    protected $fillable = [
        'jenis_sewa',
        'keterangan',
        
    ];

    public $timestamps = false;
}
