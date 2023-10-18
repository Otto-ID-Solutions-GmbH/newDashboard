<?php

use Cintas\Models\Facility\LaundryCustomer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChildrenHospitalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = \Carbon\Carbon::now('UTC')->toDateTimeString();
        $wipesCuid = 'cjneey1d600011vr9s3vuumcf';
        $mopsCuid = 'cjneey1d600021vr940kmo9oo';
        $dusterCuid = 'cjneey1d700031vr9fnnwfwtd';

        DB::transaction(function () use ($time, $wipesCuid, $mopsCuid, $dusterCuid) {
            $this->seedCustomers($time);
            $this->seedTargetContainer($wipesCuid, $mopsCuid, $dusterCuid);
        });

    }

    private function seedCustomers($time)
    {
        $cuid = \EndyJasmi\Cuid::make();

        DB::table('laundry_customers')->insert([
            ['cuid' => $cuid, 'customer_id' => config('cintas.customer_cuid'), 'created_at' => $time, 'updated_at' => $time, 'name' => 'Nationwide Children\'s Hospital', 'display_label' => null]
        ]);

        $facility = \Cintas\Models\Facility\Facility::find(config('cintas.facility_cuid'));
        $customer = LaundryCustomer::find($cuid);
        $customer->served_by_facilities()->save($facility);

    }

    /**
     * @param $wipesCuid
     * @param $mopsCuid
     * @param $dusterCuid
     */
    private function seedTargetContainer($wipesCuid, $mopsCuid, $dusterCuid): void
    {
        $customers = LaundryCustomer::all();
        foreach ($customers as $customer) {
            $tC = new \Cintas\Models\Facility\TargetContainer();
            $customer->target_container()->save($tC);

            $tC->product_types()->attach($wipesCuid, ['target_container_content' => 800]);
            $tC->product_types()->attach($mopsCuid, ['target_container_content' => 200]);
            $tC->product_types()->attach($dusterCuid, ['target_container_content' => 0]);

        }
    }
}
