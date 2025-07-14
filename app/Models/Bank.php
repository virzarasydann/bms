<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'bank';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_bank',
        'no_rekening',
        'pemilik',
    ];

    public $timestamps = false;
}
