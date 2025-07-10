<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    
    protected $table = 'customer';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama',
        'alamat',
        'no_telp',
        'email'
    ];

    public $timestamps = false;
}
