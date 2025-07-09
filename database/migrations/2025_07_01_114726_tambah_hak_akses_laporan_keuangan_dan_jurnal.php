<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TambahHakAksesLaporanKeuanganDanJurnal extends Migration
{
    public function up(): void
    {
        DB::table('hak_akses')->insert([
            [
                'id_user' => 1,
                'id_menu' => 17,
                'lihat'   => 1,
                'tambah'  => 1,
                'edit'    => 1,
                'hapus'   => 1,
            ],
            [
                'id_user' => 1,
                'id_menu' => 18,
                'lihat'   => 1,
                'tambah'  => 1,
                'edit'    => 1,
                'hapus'   => 1,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('hak_akses')->where('id_user', 1)->whereIn('id_menu', [17, 18])->delete();
    }
}

