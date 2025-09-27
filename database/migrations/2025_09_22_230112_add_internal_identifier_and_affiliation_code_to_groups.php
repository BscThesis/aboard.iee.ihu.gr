<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalIdentifierAndAffiliationCodeToGroups extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'internal_identifier')) {
                $table->dropColumn('internal_identifier');
            }
            if (Schema::hasColumn('groups', 'affiliation_code')) {
                $table->dropColumn('affiliation_code');
            }
            if (!Schema::hasColumn('groups', 'is_user')) {
                $table->text('is_user')->nullable()->after('parent_group');
            }
            if (!Schema::hasColumn('groups', 'is_author')) {
                $table->text('is_author')->nullable()->after('is_user');
            }
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['is_user', 'is_author']);
            $table->string('internal_identifier')->nullable();
            $table->string('affiliation_code')->nullable();
        });
    }
}
