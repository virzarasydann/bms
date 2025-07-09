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
        Schema::table('tutup_buku_bulanan', function (Blueprint $table) {
            $table->string('tipe')->after('tahun'); // 'Bank' atau 'Kas'
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutup_buku_bulanan', function (Blueprint $table) {
            //
        });
    }
};
