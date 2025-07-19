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
        'deskripsi_komplen',
        'penanggung_jawab',
        'status_komplen',
        'catatan_penanggung_jawab'
    ];

    protected $casts = [
        'deskripsi_komplen' => 'array',
    ];
    

    public $timestamps = false;

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }
}
