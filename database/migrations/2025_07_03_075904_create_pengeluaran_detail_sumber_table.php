<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengeluaranDetailSumberTable extends Migration
{
    public function up()
    {
        Schema::create('pengeluaran_detail_sumber', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pengeluaran_detail_id');
            $table->unsignedBigInteger('kategori_pemasukan_id');
            $table->unsignedBigInteger('nominal');
            

            
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengeluaran_detail_sumber');
    }
}
