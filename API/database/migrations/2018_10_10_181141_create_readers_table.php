<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('readers', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->string('id')->unique();
            $table->string('display_label')->nullable();
            $table->ipAddress('ip_address')->nullable();

            $table->char('facility_id', 25)->nullable();
            $table->foreign('facility_id')
                ->references('cuid')->on('facilities')
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
        Schema::dropIfExists('readers');
    }
}
