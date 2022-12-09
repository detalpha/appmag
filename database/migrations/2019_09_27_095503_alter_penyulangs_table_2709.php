<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPenyulangsTable2709 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penyulangs', function (Blueprint $table) {
            
            $table->integer('gardu_hubung_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('penyulangs', function (Blueprint $table) {
            $table->dropColumn('gardu_hubung_id');
        });
    }
}
