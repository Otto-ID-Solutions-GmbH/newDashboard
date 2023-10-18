<?php

use Cintas\NoForeignKeyCheckMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('items', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->softDeletes();

            $table->integer('cycle_count')
                ->comment('The count of washing cycles the item passed')
                ->default(0);

            $table->char('product_id', 25);
            $table->foreign('product_id')
                ->references('cuid')->on('products')
                ->onDelete('cascade');

            $table->char('bundle_id', 25)->nullable();
            $table->foreign('bundle_id')
                ->references('cuid')->on('bundles')
                ->onDelete('set null');

            // Customer (VisBOS client) relation
            $table->char('customer_id', 25)->nullable();
            $table->foreign('customer_id')
                ->references('cuid')->on('customers')
                ->onDelete('cascade');

        });

        Schema::create('item_statuses', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->text('notes')->nullable();

            $table->char('location_id', 25)->nullable();
            $table->string('location_type')->nullable();

            $table->char('item_status_type_id', 25);
            $table->foreign('item_status_type_id')
                ->references('cuid')->on('item_status_types')
                ->onDelete('cascade');

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
        Schema::dropIfExists('items');
    }
}
