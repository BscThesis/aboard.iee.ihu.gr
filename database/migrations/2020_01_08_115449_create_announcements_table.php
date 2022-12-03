<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->mediumText('body');
            $table->boolean('has_eng')->nullable()->default(false);
            $table->string('eng_title')->nullable();
            $table->mediumText('eng_body')->nullable();
            $table->boolean('gmaps')->default(0)->nullable();
            $table->boolean('is_event')->default(0)->nullable();
            $table->dateTime('pinned_until')->nullable();
            $table->dateTime('event_start_time')->nullable();
            $table->dateTime('event_end_time')->nullable();
            $table->string('event_location')->nullable();
            $table->boolean('is_pinned')->nullable()->default(false);
            $table->timestamps();
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
        Schema::dropIfExists('announcements');
    }
}
