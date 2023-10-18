<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemScanActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_scan_action', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->timestamp('read_at')->nullable();
            $table->string('antenna')->nullable();
            $table->enum('find_type', ['directEpc', 'bundle'])->nullable();

            $table->char('item_id', 25);
            $table->foreign('item_id')
                ->references('cuid')->on('items')
                ->onDelete('cascade');

            $table->char('scan_action_id', 25);
            $table->foreign('scan_action_id')
                ->references('cuid')->on('scan_actions')
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
        Schema::dropIfExists('item_scan_action');
    }
}
