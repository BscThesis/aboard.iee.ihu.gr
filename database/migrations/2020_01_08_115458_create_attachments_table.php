<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('announcement_id')->onDelete('cascade')->onUpdate('cascade');
            $table->string('filename');
            $table->unsignedInteger('filesize');
            $table->string('mime_type');
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("alter table attachments add content mediumblob not null after filename");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
