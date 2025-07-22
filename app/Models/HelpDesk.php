<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpDesk extends Model
{
    protected $table = 'help_desk';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_project',
        'tgl_komplen',
        'tgl_target_selesai',
        'komplain',
        'catatan_komplain',
        'penanggung_jawab',
        'status_komplen',
        'deskripsi'
    ];

   protected $casts = [
    'komplain' => 'array',
    'catatan_komplain' => 'array',
];


    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }
}
