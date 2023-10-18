<?php

namespace Tests\Feature\API;

use Carbon\Carbon;
use Cintas\Facades\Statistics;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Item;
use Cintas\Models\Polymorphism\LocationContract;
use EndyJasmi\Cuid;
use Tests\TestCase;

class CreateStocktakingTest extends TestCase
{
    const noEPCs = 10;

    private function getEpcsFromRead()
    {
        return RFIDTag::query()->inRandomOrder()->limit(self::noEPCs)->get();
    }

    private function resolveItemsAndProducts($epcs)
    {
        $epcs->loadMissing(['identifiable.product']);
        $items = $epcs->pluck('identifiable')->filter(function ($identifiable) {
            return $identifiable instanceof Item;
        });
        return $items;
    }

    private function resolveLocation($epcs)
    {
        $epcs->loadMissing(['identifiable']);
        $location = $epcs->first(function ($epc) {
            return $epc->identifiable instanceof LocationContract;
        });
        return $location;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateStocktaking()
    {

        // Simulate RFID read and return random EPCs
        $epcs = $this->getEpcsFromRead();

        // Resolve EPCs to items
        $items = $this->resolveItemsAndProducts($epcs);

        // Check, if one of the EPCs resolves to a location. If a location was resolved, use that location; otherwise, use a customer selected location
        $location = $this->resolveLocation($epcs);
        if (!$location) {
            $location = LaundryCustomer::query()->first();
        }

        // Compute stocktaking entries by grouping items by their product and counting the number of items (and associating these items to the entry)
        $entries = $items->groupBy('product_id')
            ->map(function ($group, $id) {
                $product = $group->first()->product;
                return [
                    'cuid' => Cuid::make(),
                    'stock_id' => $product->cuid,
                    'stock_label' => $product->label,
                    'is_amount' => $group->count(),
                    'item_ids' => $group->pluck('cuid')
                ];
            });

        // Create stocktaking data
        $date = Carbon::now('UTC');
        $stocktakingData = [
            'cuid' => Cuid::make(),
            'created_at' => $date->toIso8601String(),
            'updated_at' => $date->toIso8601String(),
            'responsible_person_name' => 'Andreas Fuhr',
            'notes' => 'Die ist ein Test',
            'location_id' => $location->cuid,
            'location_type' => Statistics::getMorphAliasFromModel($location),
            'stocktaking_entries' => $entries->values()
        ];

        // Execute API call
        $start = microtime(true);
        $response = $this->json('POST', 'api/stocktakings', ["data" => $stocktakingData]);
        $time = microtime(true) - $start;

        // Verify results
        $response->assertStatus(200);

        /*$response->assertJsonStructure([
            "data" => [
                ["cuid", "name", "label"]
            ]
        ]);*/
    }
}
