<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTutupBukuBulananTable extends Migration
{
    public function up()
    {
        Schema::table('tutup_buku_bulanan', function (Blueprint $table) {
            // Hapus kolom
            if (Schema::hasColumn('tutup_buku_bulanan', 'sisa_saldo_lalu')) {
                $table->dropColumn('sisa_saldo_lalu');
            }
            if (Schema::hasColumn('tutup_buku_bulanan', 'total_pemasukan')) {
                $table->dropColumn('total_pemasukan');
            }
            if (Schema::hasColumn('tutup_buku_bulanan', 'total_pengeluaran')) {
                $table->dropColumn('total_pengeluaran');
            }

            // Tambah kolom baru
            $table->tinyInteger('bulan')->after('id')->comment('1 - 12');
            $table->year('tahun')->after('bulan');
            $table->unsignedBigInteger('id_penerimaan')->after('tahun');

            // (Optional) Jika ingin foreign key
            // $table->foreign('id_penerimaan')->references('id')->on('kategori_penerimaan')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('tutup_buku_bulanan', function (Blueprint $table) {
            // Tambahkan kembali kolom yang dihapus
            $table->decimal('sisa_saldo_lalu', 15, 2)->nullable();
            $table->decimal('total_pemasukan', 15, 2)->nullable();
            $table->decimal('total_pengeluaran', 15, 2)->nullable();

            // Hapus kolom yang ditambahkan
            $table->dropColumn(['bulan', 'tahun', 'id_penerimaan']);
        });
    }
}
