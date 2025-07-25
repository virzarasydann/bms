<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Progres extends Model
{
    use HasFactory;

    protected $table = 'progres';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'tgl_progres',
        'stt_progres',
        'catatan',
    ];


}
