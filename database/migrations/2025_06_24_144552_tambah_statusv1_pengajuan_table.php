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
    Schema::table('pengajuan', function (Blueprint $table) {
        $table->integer('stt_pengajuan')->after('nama_perekomendasi');
    });
}

public function down(): void
{
    Schema::table('pengajuan', function (Blueprint $table) {
        $table->dropColumn('stt_pengajuan');
    });
}

};
