<?php

use Cintas\Models\Facility\LaundryCustomer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RemoveCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $cuids = LaundryCustomer::query()
            ->where('name', '=', 'Other')
            ->orWhere('name', '=', 'Medicine HealthCare')
            ->pluck('cuid')
            ->toArray();

        DB::table('out_scan_actions')
            ->whereIn('location_id', $cuids)
            ->update([
                'location_id' => null,
                'location_type' => null
            ]);

        DB::table('item_statuses')
            ->whereIn('location_id', $cuids)
            ->update([
                'location_id' => null,
                'location_type' => null
            ]);

        LaundryCustomer::destroy($cuids);

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
