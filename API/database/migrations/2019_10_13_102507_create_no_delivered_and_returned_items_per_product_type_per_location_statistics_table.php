<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoDeliveredAndReturnedItemsPerProductTypePerLocationStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivered_returned_items_statistics', function (Blueprint $table) {

            $table->char('cuid', 25);
            $table->primary('cuid');

            $table->timestamps();

            $table->timestamp('date')->useCurrent();

            $table->char('location_id', 25)->nullable();
            $table->string('location_type')->nullable();

            $table->char('product_type_id', 25);
            $table->foreign('product_type_id')
                ->references('cuid')->on('product_types')
                ->onDelete('cascade');

            $table->integer('no_items_clean_in')->nullable();
            $table->integer('no_items_soil_in')->nullable();
            $table->integer('no_items_unknown_in')->nullable();
            $table->integer('no_items_out')->nullable();

            // Customer (VisBOS client) relation
            $table->char('customer_id', 25)->nullable();
            $table->foreign('customer_id')
                ->references('cuid')->on('customers')
                ->onDelete('cascade');

            $table->index(['date', 'location_id', 'product_type_id'], 'in_out_per_product_type_per_location_data_index');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('no_delivered_and_returned_items_per_product_type_per_location_statistics');
    }
}
