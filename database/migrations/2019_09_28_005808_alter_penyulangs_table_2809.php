<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPenyulangsTable2809 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('penyulangs', function (Blueprint $table) {
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->float('arus_hs_3_phs', 20, 4);
            $table->float('teg_primer', 20, 4);
            $table->float('teg_skunder', 20, 4);
            $table->float('imp_trafo', 20, 4);
            $table->softDeletes();
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
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropColumn('arus_hs_3_phs');
            $table->dropColumn('teg_primer');
            $table->dropColumn('teg_skunder');
            $table->dropColumn('imp_trafo');
            $table->dropSoftDeletes();
        });
    }
}
