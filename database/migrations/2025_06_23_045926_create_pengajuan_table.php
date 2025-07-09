<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->increments('id'); // int, auto increment, primary key
            $table->string('nama_lengkap', 255);
            $table->string('alamat', 255);
            $table->string('no_telp', 50);
            $table->string('permasalahan', 255);
            $table->string('penyelesaian', 255);
            $table->string('nama_perekomendasi', 255);
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
}
