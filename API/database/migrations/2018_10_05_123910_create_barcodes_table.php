<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barcodes', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->string('code')->unique();
            $table->string('code_type')->nullable();

            $table->char('identifiable_id', 25)->nullable();
            $table->string('identifiable_type')->nullable();

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
        Schema::dropIfExists('barcodes');
    }
}
