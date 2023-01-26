<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewTagsLeafs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $query = 'CREATE    VIEW `tags_leafs`
                    AS
                    (SELECT T.*, 1-COUNT( DISTINCT C.parent_id) AS is_leaf
                    FROM tags T LEFT JOIN tags C ON T.id = C.`parent_id`
                    GROUP BY T.id)';

        DB::statement($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW tags_leafs');
    } 
}
