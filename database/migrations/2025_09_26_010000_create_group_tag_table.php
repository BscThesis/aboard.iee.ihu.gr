<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupTagTable extends Migration
{
    public function up(): void
    {
        Schema::create('group_tag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->unique(['group_id', 'tag_id']);

            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_tag');
    }
}
