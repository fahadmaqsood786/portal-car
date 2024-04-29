<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterJobs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('jobs', function (Blueprint $table) {
            $table->integer('status')->default(1)->after('company_name');
            $table->integer('isFeatured')->after('status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            $table->dropColumn('status');
            $table->dropColumn('isFeatured');
    }
}
