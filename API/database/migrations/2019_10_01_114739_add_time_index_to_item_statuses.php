<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeIndexToItemStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_statuses', function (Blueprint $table) {
            $table->index(['created_at', 'item_id'], 'item_statuses_created_at_index');
            $table->index(['location_id', 'location_type'], 'item_statuses_location_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_statuses', function (Blueprint $table) {
            $table->dropIndex('item_statuses_created_at_index');
            $table->dropIndex('item_statuses_location_index');
        });
    }
}
