<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScanActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scan_actions', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->char('reader_id', 25)->nullable();
            $table->foreign('reader_id')
                ->references('cuid')->on('readers')
                ->onDelete('set null');

            $table->char('scan_type_id', 25)->nullable();
            $table->foreign('scan_type_id')
                ->references('cuid')->on('scan_action_types')
                ->onDelete('set null');

            // Customer (VisBOS client) relation
            $table->char('customer_id', 25)->nullable();
            $table->foreign('customer_id')
                ->references('cuid')->on('customers')
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
        Schema::dropIfExists('scan_actions');
    }
}
