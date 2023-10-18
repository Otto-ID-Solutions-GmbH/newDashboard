<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->string('name');

            $table->string('product_number')->nullable();

            $table->integer('expected_lifetime')
                ->unsigned()
                ->comment('The number of washing cycles the item shall survive at minimum. Can override the attribute in product_types.')
                ->nullable();

            $table->char('product_type_id', 25);
            $table->foreign('product_type_id')
                ->references('cuid')->on('product_types')
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
        Schema::dropIfExists('products');
    }
}
