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

class PortalInReadTest extends TestCase
{

    use RefreshDatabase;

    private $jsonInput;

    private $items;
    private $gate;

    private $bundle;


    protected function setUp()
    {
        parent::setUp();

        Artisan::call('db:seed', [
            '--class' => \CintasCustomerSeeder::class,
        ]);

        $this->bundle = Bundle::create();

        $this->items = factory(Item::class, 3)
            ->create([
                'deleted_at' => null
            ])
            ->each(function ($i) {
                $i->rfid_tags()->save(factory(RFIDTag::class)->create());
                $i->bundle()->associate($this->bundle);
                $i->save();
            });
        try {
            $this->gate = factory(Reader::class)
                ->create([
                    'id' => 'Gate',
                    'facility_id' => config('cintas.facility_cuid')
                ]);
        } catch (\Exception $e) {

        }


        $this->jsonInput =
            <<<EOT
{
  "data": [
    {
      "IP": "169.254.1.1",
      "Name": "Gate",
      "Optional": "Workwear",
      "Lightbarrier": "Incoming",
      "Tags": [
        {
          "EPC": "A30A20130206000000000001",
          "Timestamp": "2018-10-24T16:00:00+02:00",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000002",
          "Timestamp": "2018-10-24T16:00:01+02:00",
          "Antenna": "2"
        },
        {
          "EPC": "A30A20130206000000000003",
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
    public function testExample()
    {
        $response = $this->post('api/reads/portal-in-read', json_decode($this->jsonInput, true));
        $response->assertStatus(200);

        $action = ScanAction::find($response->json('data.0.cuid') ?? null);
        $this->assertNotNull($action, 'Scan action missing');
        $this->assertEquals('CleanInScan', $action->type->name, 'Wrong scan action');

        $facility = Facility::all()->first();
        $items = Item::query()->whereHas('rfid_tags', function ($query) {
            return $query->whereIn('epc', ['A30A20130206000000000001', 'A30A20130206000000000002', 'A30A20130206000000000003']);
        })->get();

        // Test item statuses
        foreach ($items as $item) {
            $item->refresh();

            $this->assertNotNull($item->location, 'Location missing');
            $this->assertEquals($facility->cuid, $item->location->cuid, 'Wrong location after register dryer read');
            $this->assertEquals($this->bundle->cuid, $item->bundle->cuid, 'Item was unbundled although it should not!');

            $this->assertEquals('AtFacilityCleanStatus', $item->last_status->status_type->name, 'Wrong last status type!');
        }

        // Test scan actions
        $scanActions = ScanAction::all();
        $this->assertEquals(1, $scanActions->count(), 'Wrong number of scan actions');

        $action = $scanActions->get(0);
        $reader = Reader::findById('Gate');
        $this->assertEquals($reader->cuid, $action->reader->cuid, 'Wrong reader for action');
        $this->assertEquals($items->take(2)->only('cuid'), $action->items->only('cuid'), 'Wrong items in scan action');
        $this->assertEquals('CleanInScan', $action->type->name, 'Wrong scan action type');
    }
}
