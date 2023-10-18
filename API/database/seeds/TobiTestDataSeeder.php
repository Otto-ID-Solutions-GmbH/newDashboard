<?php

use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

const TOBI_EPCs = ["E28011602000745C4227090A", "E2801160200061294244090A", "E2801160200075DF4235090A", "E2801160200065CA4230090A", "E2801160200065DE4228090A", "E2801160200062ED4232090A", "E2801160200064044241090A", "E28011602000654D422B090A", "E2801160200070144238090A", "E28011602000746B4223090A", "E28011602000701B422A090A", "E2801160200065B3423E090A", "E28011602000710E422F090A", "E2801160200075B7423E090A", "E2801160200064CA4223090A", "E280116020006558422F090A", "E2801160200071334233090A", "E2801160200061334246090A", "E280116020007584422F090A", "E2801160200064D14223090A"];

class TobiTestDataSeeder extends Seeder
{

    private $itemCount = 100;
    private $readerRepository;
    private $faker;

    /**
     * CintasTestDataSeeder constructor.
     * @param $readerRepository
     */
    public function __construct(\Cintas\Repositories\ReaderRepository $readerRepository, Faker $faker)
    {
        $this->readerRepository = $readerRepository;
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $product = \Cintas\Models\Items\Product::where('product_number', '=', '07116')->first();
        $tagItronEpcs = collect(TOBI_EPCs);

        $this->registerEPCsWithProduct($tagItronEpcs, $product);

    }

    private function registerEPCsWithProduct($epcs, \Cintas\Models\Items\Product $product)
    {
        $timeStamp = \Carbon\Carbon::now('UTC')->toDateTimeString();
        $newStatus = \Cintas\Models\Items\ItemStatusType::findByName('NewStatus');

        $statusData = collect();
        $epcData = collect();
        $itemData = collect();

        foreach ($epcs as $epc) {
            $statusCuid = \EndyJasmi\Cuid::make();
            $itemCuid = \EndyJasmi\Cuid::make();

            $statusData->push([
                'cuid' => $statusCuid,
                'created_at' => $timeStamp,
                'updated_at' => $timeStamp,
                'item_status_type_id' => $newStatus->cuid,
                'item_id' => $itemCuid,
                'customer_id' => config('cintas.customer_cuid')
            ]);

            $itemData->push([
                'cuid' => $itemCuid,
                'created_at' => $timeStamp,
                'updated_at' => $timeStamp,
                'cycle_count' => 0,
                'product_id' => $product->cuid,
                'last_status_id' => $statusCuid,
                'customer_id' => config('cintas.customer_cuid')
            ]);

            $epcData->push([
                'cuid' => \EndyJasmi\Cuid::make(),
                'created_at' => $timeStamp,
                'updated_at' => $timeStamp,
                'epc' => $epc,
                'epc_type' => 'SGTIN',
                'identifiable_id' => $itemCuid,
                'identifiable_type' => 'Item',
                'customer_id' => config('cintas.customer_cuid')
            ]);
        }

        DB::transaction(function () use ($statusData, $itemData, $epcData) {
            Schema::disableForeignKeyConstraints();
            \Cintas\Models\Items\ItemStatus::insert($statusData->toArray());
            \Cintas\Models\Items\Item::insert($itemData->toArray());
            \Cintas\Models\Identifiables\RFIDTag::insert($epcData->toArray());
            Schema::enableForeignKeyConstraints();
        }, 5);

    }

}
