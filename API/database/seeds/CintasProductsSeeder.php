<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CintasProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_types')->insert([
            ['cuid' => \EndyJasmi\Cuid::make()]
        ]);
    }
}
