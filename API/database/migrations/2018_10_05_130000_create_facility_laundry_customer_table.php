<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacilityLaundryCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_laundry_customer', function (Blueprint $table) {
            $table->char('cuid', 25);
            $table->primary('cuid');
            $table->timestamps();

            $table->char('facility_id', 25);
            $table->foreign('facility_id')
                ->references('cuid')->on('facilities')
                ->onDelete('cascade');

            $table->char('laundry_customer_id', 25);
            $table->foreign('laundry_customer_id')
                ->references('cuid')->on('laundry_customers')
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
        Schema::dropIfExists('facility_laundry_customers');
    }
}
