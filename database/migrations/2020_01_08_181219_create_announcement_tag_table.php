<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcement_tag', function (Blueprint $table) {
            $table->bigInteger('announcement_id')->unsigned()->index();
            $table->foreign('announcement_id')->references('id')->on('announcements')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('tag_id')->unsigned()->index();
            $table->foreign('tag_id')->references('id')->on('tags')->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['announcement_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcement_tag');
    }
}
