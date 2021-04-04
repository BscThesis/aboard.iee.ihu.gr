<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValueOnParentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('tags', function (Blueprint $table) {
        //     $table->dropForeign('tags_parent_id_foreign');
        // });

        Schema::table('tags', function (Blueprint $table) {
            $table->bigInteger('parent_id')->default(1)->change();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('tags')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            //
        });
    }
}
