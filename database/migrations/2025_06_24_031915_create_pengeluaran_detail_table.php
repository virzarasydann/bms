<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('pengeluaran_detail', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('id_pengeluaran');
        $table->unsignedBigInteger('nominal');
        $table->unsignedBigInteger('kategori_pengeluaran_id');
       
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_detail');
    }
};
