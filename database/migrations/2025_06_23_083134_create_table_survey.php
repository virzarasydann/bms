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
        Schema::create('survey', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_survey');
            $table->string('nama_lengkap', 255);
            $table->string('nik', 50);
            $table->string('alamat', 255);
            $table->string('tempat_lahir', 255);
            $table->date('tgl_lahir');
            $table->integer('usia');
            $table->string('jenis_kelamin', 25);
            $table->string('status', 25);
            $table->string('pekerjaan', 50);
            $table->string('penghasilan', 50);
            $table->string('no_hp', 25);
            $table->string('lama_tinggal', 50);
            $table->string('stt_tempat_tinggal', 50);
            $table->string('membantu', 20);
            $table->string('nama_lembaga_membantu', 100)->nullable();
            $table->string('orang_terdekat', 255)->nullable();
            $table->string('masalah', 255);
            $table->string('jumlah_tanggungan', 25);
            $table->string('usaha_dilakukan', 255);
            $table->string('pengeluaran_bulan', 50);
            $table->integer('tabungan');
            $table->string('hutang', 25);
            $table->string('harapan_bantuan', 255);
            $table->string('bersedia_kajian_islam', 25);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey');
    }
};
