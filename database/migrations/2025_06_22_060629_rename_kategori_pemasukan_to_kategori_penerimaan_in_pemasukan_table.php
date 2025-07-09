<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameKategoriPemasukanToKategoriPenerimaanInPemasukanTable extends Migration
{
    public function up()
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            $table->renameColumn('kategori_pemasukan', 'kategori_penerimaan');
        });
    }

    public function down()
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            $table->renameColumn('kategori_penerimaan', 'kategori_pemasukan');
        });
    }
}
