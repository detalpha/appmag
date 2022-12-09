<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenyulangSpotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penyulang_spots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('penyulangs_id');
            $table->string('code', 191);
            $table->string('latitude', 191);
            $table->string('longitude', 191);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penyulang_spots');
    }
}
