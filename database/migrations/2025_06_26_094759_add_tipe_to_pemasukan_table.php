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
        Schema::table('pemasukan', function (Blueprint $table) {
            $table->string('tipe')->nullable()->after('lampiran');
        });
    }

    public function down()
    {
        Schema::table('pemasukan', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
