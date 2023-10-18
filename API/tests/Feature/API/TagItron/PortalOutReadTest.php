<?php

namespace Tests\Feature\API\TagItron;

use Cintas\Models\Actions\ScanAction;
use Cintas\Models\Facility\Bundle;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Facility\Reader;
use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PortalOutReadTest extends TestCase
{

    use RefreshDatabase;

    private $jsonInput;

    private $bundle;
    private $bundledItems;

    private $bundle2;
    private $bundledItems2;

    private $unbundledItems;

    private $gate;
    private $target;


    protected function setUp()
    {
        parent::setUp();

        Artisan::call('db:seed', [
            '--class' => \CintasCustomerSeeder::class,
        ]);

        $facility = Facility::find(config('cintas.facility_cuid'));

        $this->bundle = Bundle::create();
        $this->bundle2 = Bundle::create();

        $this->bundledItems = factory(Item::class, 10)
            ->create([
                'deleted_at' => null
            ])
            ->each(function ($i) {
                $i->rfid_tags()->save(factory(RFIDTag::class)->create());
                $i->bundle()->associate($this->bundle);
                $i->save();
            });

        $this->bundledItems2 = factory(Item::class, 10)
            ->create([
                'deleted_at' => null
            ])
            ->each(function ($i) {
                $i->rfid_tags()->save(factory(RFIDTag::class)->create());
                $i->bundle()->associate($this->bundle2);
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
                'id' => 'Gate',
                'facility_id' => config('cintas.facility_cuid')
            ]);

        $this->target = factory(LaundryCustomer::class)->create();
        $this->target->served_by_facilities()->attach($facility);

        $this->jsonInput =
            <<<EOT
{
  "data": [
    {
      "IP": "169.254.1.1",
      "Name": "Gate",
      "Optional": "Workwear",
      "Lightbarrier": "Outgoing",
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
          "EPC": "A30A20130206000000000006",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "1"
        },
        
        {
          "EPC": "A30A20130206000000000011",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "2"
        },
        {
          "EPC": "A30A20130206000000000012",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "2"
        },
        
        {
          "EPC": "7461676974726F2000000000",
          "Timestamp": "2018-10-24T16:00:02+02:00",
          "Antenna": "1"
        }
      ]
    }
  ]
}
EOT;
    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCustomerOutReadWithDirectItems()
    {
        $customerId = $this->target->cuid;
        $response = $this->post("api/reads/portal-out-read?customer=$customerId", json_decode($this->jsonInput, true));
        $response->assertStatus(200);

        $action = ScanAction::find($response->json('data.0.cuid') ?? null);
        $this->assertNotNull($action, 'Scan action missing');
        $this->assertEquals('OutScan', $action->type->name, 'Wrong scan action');

        $items = $action->items->pluck('cuid');

        // Refresh items and item statuses
        $this->bundledItems->fresh();
        $this->bundledItems2->fresh();
        $this->unbundledItems->fresh();

        // Test if all bundled items of bundle one are contained
        foreach ($this->bundledItems as $bundledItem) {
            $bundledItem->refresh();
            $this->assertContains($bundledItem->cuid, $items, 'Bundled item missing in scan action');
            $this->assertEquals($customerId, $bundledItem->location->cuid ?? null, 'Wrong location in item status');
        }

        // Test if the direct items of bundle 2 are NOT contained in the result
        $directBundle2Items = Item::whereHas('rfid_tags', function ($query) {
            return $query->whereIn('epc', ['A30A20130206000000000011', 'A30A20130206000000000012']);
        })->get();

        foreach ($directBundle2Items as $directItem) {
            $directItem->refresh();
            $response->assertJsonMissing(['cuid' => $directItem->cuid]);
            $this->assertNotContains($directItem->cuid, $items, 'Direct items of bundle 2 are missing in scan action');
            $this->assertNotEquals($customerId, $directItem->location->cuid ?? null, 'Wrong location in item status');
        }

        // Test if indirect items of bundle 2 are not contained in the result
        $indirectBundle2ItemCuids = $this->bundledItems2->diff($directBundle2Items);

        foreach ($indirectBundle2ItemCuids as $indirectItemCuid) {
            $this->assertNotContains($indirectItemCuid, $items, 'Indirect items of bundle 2 are contained in scan action although the bundle threshold was not reached!');
        }

        // Test the target is set correctly
        $this->assertEquals($action->out_scan_action->location->cuid, $this->target->cuid, 'Wrong target location for scan action!');

        // Test the unknown tag is identified correctly
        $unknownTags = $action->unknown_rfid_tags->map(function ($i) {
            return $i->cuid;
        });

        $this->assertContains(RFIDTag::findByEpc('7461676974726F2000000000')->cuid, $unknownTags, 'Unknown EPC not listed in result');

        //TODO: Add tests for the item statuses, for calling the service with no customer, ...
    }

}
