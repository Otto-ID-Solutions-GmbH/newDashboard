<?php

use Cintas\NoForeignKeyCheckMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->string('name');

            $table->integer('expected_lifetime')
                ->unsigned()
                ->comment('The number of washing cycles the item shall survive at minimum.')
                ->nullable();

            $table->integer('bundle_target')
                ->unsigned()
                ->comment('The number of items required within a bundle')
                ->nullable();


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
        Schema::dropIfExists('product_types');
    }
}
