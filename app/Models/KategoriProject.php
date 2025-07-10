<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriProject extends Model
{
    protected $table = 'kategori_project';

    protected $primaryKey = 'id';

    protected $fillable = [
        'kategori',
        'keterangan',
        
    ];

    public $timestamps = false;
}
