<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeskripsiToPengeluaranTable extends Migration
{
    public function up()
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->string('deskripsi')->nullable()->after('tipe');
        });
    }

    public function down()
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropColumn('deskripsi');
        });
    }
}
