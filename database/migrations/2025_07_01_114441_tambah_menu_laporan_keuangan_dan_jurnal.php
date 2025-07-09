<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TambahMenuLaporanKeuanganDanJurnal extends Migration
{
    public function up(): void
    {
        DB::table('menu')->insert([
            [
                'id_parent'  => 10,
                'title'      => 'Laporan Keuangan',
                'route_name' => 'laporanKeuangan.index',
                'icon'       => 'far fa-circle',
                'urutan'     => 4,
                'lihat'      => 1,
                'tambah'     => 1,
                'edit'       => 1,
                'hapus'      => 1,
            ],
            [
                'id_parent'  => 10,
                'title'      => 'Laporan Jurnal',
                'route_name' => 'laporanJurnal.index',
                'icon'       => 'far fa-circle',
                'urutan'     => 5,
                'lihat'      => 1,
                'tambah'     => 1,
                'edit'       => 1,
                'hapus'      => 1,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('menu')->whereIn('route_name', [
            'laporan.keuangan',
            'laporan.jurnal'
        ])->delete();
    }
}

