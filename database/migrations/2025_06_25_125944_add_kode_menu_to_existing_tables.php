<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeMenuToExistingTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->string('no_pengajuan')->nullable()->after('id');
        });

        Schema::table('survey', function (Blueprint $table) {
            $table->string('no_survey')->nullable()->after('id');
        });

        Schema::table('pemasukan', function (Blueprint $table) {
            $table->string('no_transaksi')->nullable()->after('id');
        });

        Schema::table('pengeluaran', function (Blueprint $table) {
                    $table->string('no_pengeluaran')->nullable()->after('id');
                });

        // Catatan: tabel 'pengeluaran' akan menyusul jika sudah tersedia
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan', function (Blueprint $table) {
            $table->dropColumn('no_pengajuan');
        });

        Schema::table('survey', function (Blueprint $table) {
            $table->dropColumn('no_survey');
        });

        Schema::table('pemasukan', function (Blueprint $table) {
            $table->dropColumn('no_transaksi');
        });
Schema::table('pengeluaran', function (Blueprint $table) {
            $table->string('no_pengeluaran')->nullable()->after('id');
        });
    }
}