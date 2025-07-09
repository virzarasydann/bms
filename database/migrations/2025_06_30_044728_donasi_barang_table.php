<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donasi_barang', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_donasi');
            $table->string('nama_barang', 255);
            $table->integer('jumlah');
            $table->string('satuan', 50);
            $table->string('nama_donatur', 255);
            $table->string('keterangan', 255)->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi_barang');
    }
};
