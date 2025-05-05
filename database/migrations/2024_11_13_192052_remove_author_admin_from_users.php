<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveAuthorAdminFromUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_author', 'is_admin']);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Ensure columns are added back in an appropriate order.
            $table->tinyInteger('is_author')->default(0)->after('email');
            $table->tinyInteger('is_admin')->default(0)->after('is_author');
        });
    }
}
