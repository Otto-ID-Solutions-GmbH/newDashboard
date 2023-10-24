<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Cintas\Models\Facility\LaundryCustomer;


class TrialCustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = \Carbon\Carbon::now('UTC')->toDateTimeString();
        for ($i = 1; $i <= 5; $i++) {
            $customerName = "trial$i";

            DB::table('laundry_customers')->insert([
                ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'created_at' => $time, 'updated_at' => $time, 'name' => $customerName, 'display_label' => null],
            ]);
    
        }
        
        $facility = \Cintas\Models\Facility\Facility::find(config('cintas.facility_cuid'));

        foreach (LaundryCustomer::all() as $customer) {
            $customer->served_by_facilities()->save($facility);
        }
    }
}
