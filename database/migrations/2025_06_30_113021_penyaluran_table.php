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
        Schema::create('penyaluran', function (Blueprint $table) {
            $table->id();
            $table->integer('id_donasi_barang');
            $table->date('tgl_penyaluran');
            $table->string('jenis_penyaluran', 50);
            $table->string('nama_penerima', 255);
            $table->string('keterangan', 255)->nullable();
            $table->string('tipe', 50)->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyaluran');
    }
};
