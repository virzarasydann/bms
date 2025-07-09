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
    Schema::table('pengeluaran', function (Blueprint $table) {
        $table->renameColumn('nominal', 'jumlah');
        $table->dropColumn('kategori_pengeluaran');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
