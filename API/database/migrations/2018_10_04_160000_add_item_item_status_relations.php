<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemItemStatusRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('items', function (Blueprint $table) {
            $table->char('last_status_id', 25)
                ->comment('The id of the last status (performance reasons)')
                ->nullable();
            $table->foreign('last_status_id')
                ->references('cuid')->on('item_statuses')
                ->onDelete('set null');
        });

        Schema::table('item_statuses', function (Blueprint $table) {
            $table->char('item_id', 25);
            $table->foreign('item_id')
                ->references('cuid')->on('items')
                ->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
