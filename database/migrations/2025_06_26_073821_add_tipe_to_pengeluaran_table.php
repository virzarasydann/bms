<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipeToPengeluaranTable extends Migration
{
    public function up()
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->string('tipe')->nullable()->after('lampiran');
        });
    }

    public function down()
    {
        Schema::table('pengeluaran', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
}
