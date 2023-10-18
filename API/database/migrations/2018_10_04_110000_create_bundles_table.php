<?php

use Cintas\NoForeignKeyCheckMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBundlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->char('container_id', 25)->nullable();
            $table->foreign('container_id')
                ->references('cuid')->on('containers')
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
        Schema::dropIfExists('bundles');
    }
}
