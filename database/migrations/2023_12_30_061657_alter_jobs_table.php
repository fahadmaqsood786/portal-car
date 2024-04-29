<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('user_id')->after('job_type_id')->constraned()->onDelete('casecade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            $table->dropForeign('[user_id]');
            $table->dropColumn('user_id');
    }
}
