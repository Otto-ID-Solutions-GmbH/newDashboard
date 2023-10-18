<?php

namespace Cintas\Imports;

use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToCollection;

class TagDataImport implements ToCollection
{


    public function collection(Collection $rows)
    {

        $existingEPCs = RFIDTag::all()->pluck('epc');

        if ($rows->first()[4] == 'Flatmpos') {
            $product = Product::where('product_number', '=', '07000')->first();
        }

        if ($rows->first()[4] == 'Wipes') {
            $product = Product::where('product_number', '=', '07432')->first();
        }

        if ($rows->first()[4] == 'Duster') {
            $product = Product::where('product_number', '=', '08119')->first();
        }

        $epcs = collect();
        foreach ($rows as $row) {
            if ($row[0] != null) {
                $epcs->push($row[0]);
            }
        }

        $newEpcs = $epcs->diff($existingEPCs);
        $existingEPCs = $epcs->diff($newEpcs);

        if ($existingEPCs->count() > 0) {
            Log::error("Tried to import {$existingEPCs->count()} existing EPCs from CSV!", ['Existing EPCs' => $existingEPCs->all()]);
        }

        $this->registerEPCsWithProduct($newEpcs->unique(), $product);
    }

    private function registerEPCsWithProduct($epcs, Product $product)
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
