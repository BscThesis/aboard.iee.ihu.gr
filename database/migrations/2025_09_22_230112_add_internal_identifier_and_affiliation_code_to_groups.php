<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalIdentifierAndAffiliationCodeToGroups extends Migration
{
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            if (!Schema::hasColumn('groups', 'internal_identifier')) {
                $table->string('internal_identifier')->nullable()->after('name');
                $table->unique('internal_identifier', 'groups_internal_identifier_unique');
            }

            if (!Schema::hasColumn('groups', 'affiliation_code')) {
                $table->string('affiliation_code')->nullable()->after('internal_identifier');
                $table->unique('affiliation_code', 'groups_affiliation_code_unique');
            }
        });
    }

    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'internal_identifier')) {
                $table->dropUnique('groups_internal_identifier_unique');
            }
            if (Schema::hasColumn('groups', 'affiliation_code')) {
                $table->dropUnique('groups_affiliation_code_unique');
            }
            $table->dropColumn(['internal_identifier', 'affiliation_code']);
        });
    }
}
