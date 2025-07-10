<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';

    protected $fillable = [
        'id_kategori_project',
        'nama_project',
        'id_customer',
        'tgl_kontrak',
        'tanggal_selesai',
        'nilai_project',
        'penanggung_jawab',
        'status_pembayaran',
    ];

    public $timestamps = false;

    // Relasi ke KategoriProject
    public function kategoriProject()
    {
        return $this->belongsTo(KategoriProject::class, 'id_kategori_project');
    }

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }
}
