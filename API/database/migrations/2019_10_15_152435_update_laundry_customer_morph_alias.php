<?php

use Illuminate\Database\Migrations\Migration;

class UpdateLaundryCustomerMorphAlias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::table('delivered_returned_items_statistics')
            ->where('location_type', '=', 'Laundry_Customer')
            ->update([
                'location_type' => 'LaundryCustomer'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
