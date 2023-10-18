<?php

namespace Tests\Feature\API\TagItron;

use Cintas\Models\Actions\ScanAction;
use Cintas\Models\Facility\Bundle;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\Reader;
use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TableReadTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    use RefreshDatabase;

    private $jsonInput;

    private $bundle;
    private $bundledItems;

    private $unbundledItems;

    private $gate;
    private $facility;


    protected function setUp()
    {
        parent::setUp();

        Artisan::call('db:seed', [
            '--class' => \CintasCustomerSeeder::class,
        ]);

        $this->facility = Facility::find(config('cintas.facility_cuid'));

        $this->bundle = Bundle::create();

        $this->bundledItems = factory(Item::class, 10)
            ->create()
            ->each(function ($i) {
                $i->rfid_tags()->save(factory(RFIDTag::class)->create());
                $i->bundle()->associate($this->bundle);
                $i->save();
            });

        $this->unbundledItems = factory(Item::class, 10)
            ->create([
                'deleted_at' => null
            ])
            ->each(function ($i) {
                $i->rfid_tags()->save(factory(RFIDTag::class)->create());
            });

        $this->gate = factory(Reader::class)
            ->create([
                'id' => 'Table 1',
                'facility_id' => config('cintas.facility_cuid')
            ]);

        $this->jsonInput =
            <<<EOT
{
  "data": [
    {
      "IP": "169.254.1.5",
      "Name": "Table 1",
      "Optional": "Workwear",
      "Tags": [
        {
          "EPC": "A30A20130206000000000001",
          "Timestamp": "2018-10-24T16:00:00+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000002",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000003",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000004",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000005",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000011",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000012",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000013",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000014",
          "Timestamp": "2018-10-24T16:00:02+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000015",
          "Timestamp": "2018-10-24T16:00:02+02:00",
          "Antenna": "1"
        }
      ]
    }
  ]
}
EOT;
    }

    public function testTableBundling()
    {

        $response = $this->post("api/reads/table-read", json_decode($this->jsonInput, true));
        $response->assertStatus(200);

        $action = ScanAction::find($response->json('data.0.cuid') ?? null);
        $this->assertNotNull($action, 'Scan action missing');

        $this->assertEquals('TableScan', $action->type->name, 'Wrong scan action');

        $inputItemsOfBundle = $this->bundledItems->take(5);
        $remainingItemsOfBundle = $this->bundledItems->diff($inputItemsOfBundle);

        $inputItemsOfUnbundled = $this->unbundledItems->take(5);
        $remainingItemsOfUnbundled = $this->unbundledItems->diff($inputItemsOfUnbundled);

        $inputItems = $inputItemsOfBundle->concat($inputItemsOfUnbundled);
        $remainingItems = $remainingItemsOfBundle->concat($remainingItemsOfUnbundled);

        $actionItemCuids = $action->items->pluck('cuid');

        // Test if all input items are in the result and
        foreach ($inputItems as $item) {
            $item->refresh();

            //$response->assertJsonFragment(['cuid' => $item->cuid]);
            $this->assertContains($item->cuid, $actionItemCuids, 'Bundled item missing in scan action');

            $this->assertNotNull($item->bundle, 'Bundle information missing');
            $this->assertNotEquals($this->bundle->cuid, $item->bundle->cuid, 'Wrong bundle for item!');

            $this->assertEquals($item->last_status->status_type->name, 'AtFacilityTableStatus', 'Items has wrong status type');
            $this->assertEquals($item->location->cuid, $this->facility->cuid, 'Item at wrong location');
        }

        // Test if bundled items not in input are now unbundled
        foreach ($remainingItems as $item) {
            $item->refresh();
            $this->assertNotContains($item->cuid, $actionItemCuids, 'Bundled item is in scan action although it is not in the input');
            $this->assertNull($item->bundle, 'Item still bundled');
        }

    }

}
