<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTypeTargetContainerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_type_target_container', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->char('target_container_id', 25);
            $table->foreign('target_container_id')
                ->references('cuid')->on('target_containers')
                ->onDelete('cascade');

            $table->char('product_type_id', 25);
            $table->foreign('product_type_id')
                ->references('cuid')->on('product_types')
                ->onDelete('cascade');

            $table->integer('target_container_content')
                ->comment('The target amount of items within a container')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laundry_customer_product');
    }
}
