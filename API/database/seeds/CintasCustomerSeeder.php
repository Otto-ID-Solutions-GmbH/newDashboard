<?php

use Cintas\Models\Facility\LaundryCustomer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CintasCustomerSeeder extends Seeder
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

        $this->seedBasicData($time);

        $this->seedCustomers($time);

        $this->seedProducts($wipesCuid, $time, $mopsCuid, $dusterCuid);

        $this->seedTargetContainer($wipesCuid, $mopsCuid, $dusterCuid);

        $this->seedReaders($time);

        $this->seedTags('import');


    }

    private function seedCustomers($time)
    {
        //TODO: Adapt to final list
        DB::table('laundry_customers')->insert([
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'created_at' => $time, 'updated_at' => $time, 'name' => 'Memorial HealthCare', 'display_label' => null],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'created_at' => $time, 'updated_at' => $time, 'name' => 'Centrestreet', 'display_label' => null],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'created_at' => $time, 'updated_at' => $time, 'name' => 'Medicine HealthCare', 'display_label' => null],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'created_at' => $time, 'updated_at' => $time, 'name' => 'Other', 'display_label' => null],
        ]);

        $facility = \Cintas\Models\Facility\Facility::find(config('cintas.facility_cuid'));

        foreach (LaundryCustomer::all() as $customer) {
            $customer->served_by_facilities()->save($facility);
        }
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

    /**
     * @param $wipesCuid
     * @param $time
     * @param $mopsCuid
     * @param $dusterCuid
     */
    private function seedProducts($wipesCuid, $time, $mopsCuid, $dusterCuid): void
    {
        // Cintas product types
        //TODO: Adapt expected lifetimes and bundle targets of product types!
        DB::table('product_types')->insert([
            ['cuid' => $wipesCuid, 'customer_id' => config('cintas.customer_cuid'), 'name' => 'Wiper', 'expected_lifetime' => 30, 'created_at' => $time, 'updated_at' => $time, 'bundle_target' => 10],
            ['cuid' => $mopsCuid, 'customer_id' => config('cintas.customer_cuid'), 'name' => 'Mop', 'expected_lifetime' => 30, 'created_at' => $time, 'updated_at' => $time, 'bundle_target' => 10],
            ['cuid' => $dusterCuid, 'customer_id' => config('cintas.customer_cuid'), 'name' => 'Duster', 'expected_lifetime' => 30, 'created_at' => $time, 'updated_at' => $time, 'bundle_target' => 10],
        ]);

        // Cintas products
        DB::table('products')->insert([
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $mopsCuid, 'name' => '12" Microfiber Mop Head', 'product_number' => '07116', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $mopsCuid, 'name' => '20" Microfiber Mop Head', 'product_number' => '07000', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $mopsCuid, 'name' => '36" Microfiber Mop Head', 'product_number' => '07001', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $mopsCuid, 'name' => 'Microfiber Tube Mop', 'product_number' => '08020', 'created_at' => $time, 'updated_at' => $time],

            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $wipesCuid, 'name' => '12" x 12" Microfiber Wiper (Blue)', 'product_number' => '07432', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $wipesCuid, 'name' => '12" x 12" Microfiber Wiper (Orange)', 'product_number' => '07433', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $wipesCuid, 'name' => '15" x 18" Microfiber Wiper (White)', 'product_number' => '07717', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $wipesCuid, 'name' => '15" x 18" Microfiber Wiper (Orange)', 'product_number' => '07540', 'created_at' => $time, 'updated_at' => $time],

            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $dusterCuid, 'name' => 'MF High Duster Frame', 'product_number' => '08118', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'product_type_id' => $dusterCuid, 'name' => 'MF High Duster Frame', 'product_number' => '08119', 'created_at' => $time, 'updated_at' => $time],
        ]);
    }

    /**
     * @param $time
     */
    private function seedBasicData($time): void
    {
        DB::table('customers')->insert([
            ['cuid' => config('cintas.customer_cuid'), 'name' => 'Cintas', 'display_label' => 'Cintas', 'created_at' => $time, 'updated_at' => $time]
        ]);

        DB::table('facilities')->insert([
            ['cuid' => config('cintas.facility_cuid'), 'customer_id' => config('cintas.customer_cuid'), 'name' => 'Cintas Uniform Services, Columbus, OH', 'display_label' => 'Cintas Columbus, OH', 'created_at' => $time, 'updated_at' => $time]
        ]);

        DB::table('item_status_types')->insert([
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => null, 'name' => 'NewStatus', 'status_text' => 'new in the system', 'created_at' => $time, 'updated_at' => $time],

            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => null, 'name' => 'AtFacilityStatus', 'status_text' => 'at facility \'%s\'', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => null, 'name' => 'AtFacilityTableStatus', 'status_text' => 'bundled at facility \'%s\'', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => null, 'name' => 'AtFacilityCleanStatus', 'status_text' => 'clean return at facility \'%s\'', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => null, 'name' => 'AtFacilityDryerStatus', 'status_text' => 'dried at facility \'%s\'', 'created_at' => $time, 'updated_at' => $time],

            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => null, 'name' => 'AtCustomerStatus', 'status_text' => 'at customer \'%s\'', 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => null, 'name' => 'AtUnknownCustomerStatus', 'status_text' => 'at unknown customer', 'created_at' => $time, 'updated_at' => $time],

            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => null, 'name' => 'SortedOutStatus', 'status_text' => 'sorted out on \'%s\'', 'created_at' => $time, 'updated_at' => $time],

            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => null, 'name' => 'UnknownStatus', 'status_text' => 'unknown', 'created_at' => $time, 'updated_at' => $time]
        ]);

        DB::table('scan_action_types')->insert([
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'name' => 'DirtyInScan', 'display_label' => "Scan at dryer", 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'name' => 'CleanInScan', 'display_label' => "Scan at Gate/In", 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'name' => 'OutScan', 'display_label' => "Scan at Gate/Out", 'created_at' => $time, 'updated_at' => $time],
            ['cuid' => \EndyJasmi\Cuid::make(), 'customer_id' => config('cintas.customer_cuid'), 'name' => 'TableScan', 'display_label' => "Scan at table", 'created_at' => $time, 'updated_at' => $time]
        ]);
    }

    private function seedReaders($time): void
    {
        DB::table('readers')->insert([

            // Gates
            ['cuid' => \EndyJasmi\Cuid::make(), 'created_at' => $time, 'updated_at' => $time,
                'id' => 'Gate', 'ip_address' => '172.16.0.10',
                'customer_id' => config('cintas.customer_cuid'), 'facility_id' => config('cintas.facility_cuid')],

            // Dryers
            ['cuid' => \EndyJasmi\Cuid::make(), 'created_at' => $time, 'updated_at' => $time,
                'id' => 'Dryer 1', 'ip_address' => '172.16.0.20',
                'customer_id' => config('cintas.customer_cuid'), 'facility_id' => config('cintas.facility_cuid')],
            ['cuid' => \EndyJasmi\Cuid::make(), 'created_at' => $time, 'updated_at' => $time,
                'id' => 'Dryer 2', 'ip_address' => '172.16.0.22',
                'customer_id' => config('cintas.customer_cuid'), 'facility_id' => config('cintas.facility_cuid')],

            // Tables
            ['cuid' => \EndyJasmi\Cuid::make(), 'created_at' => $time, 'updated_at' => $time,
                'id' => 'Table 1', 'ip_address' => '172.16.0.31',
                'customer_id' => config('cintas.customer_cuid'), 'facility_id' => config('cintas.facility_cuid')],
            ['cuid' => \EndyJasmi\Cuid::make(), 'created_at' => $time, 'updated_at' => $time,
                'id' => 'Table 2', 'ip_address' => '172.16.0.32',
                'customer_id' => config('cintas.customer_cuid'), 'facility_id' => config('cintas.facility_cuid')],
            ['cuid' => \EndyJasmi\Cuid::make(), 'created_at' => $time, 'updated_at' => $time,
                'id' => 'Table 3', 'ip_address' => '172.16.0.33',
                'customer_id' => config('cintas.customer_cuid'), 'facility_id' => config('cintas.facility_cuid')],
            ['cuid' => \EndyJasmi\Cuid::make(), 'created_at' => $time, 'updated_at' => $time,
                'id' => 'Table 4', 'ip_address' => '172.16.0.34',
                'customer_id' => config('cintas.customer_cuid'), 'facility_id' => config('cintas.facility_cuid')],

        ]);
    }

    private function seedTags($dir): void
    {
        foreach (Storage::disk('local')->allFiles($dir) as $filename) {
            Excel::import(new \Cintas\Imports\TagDataImport(), $filename, 'local');
        }
    }

}
