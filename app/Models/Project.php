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

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_project');
    }
    
    public function pemasukan()
    {
        return $this->hasOne(Pemasukan::class, 'id_project');
    }
    
    
    public function helpDesk()
    {
        return $this->hasMany(HelpDesk::class, 'id_project');
    }



}
