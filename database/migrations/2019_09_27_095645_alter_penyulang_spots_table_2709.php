<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPenyulangSpotsTable2709 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penyulang_spots', function (Blueprint $table) {
            $table->string('header')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penyulang_spots', function (Blueprint $table) {
            $table->dropColumn('header');
            $table->dropColumn('name');
            $table->dropColumn('type');
        });
    }
}
