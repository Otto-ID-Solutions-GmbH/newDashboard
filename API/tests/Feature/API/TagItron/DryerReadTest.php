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

class DryerReadTest extends TestCase
{

    use RefreshDatabase;

    private $jsonInput;

    private $items;
    private $dryer1;
    private $dryer2;

    protected function setUp()
    {
        parent::setUp();

        Artisan::call('db:seed', [
            '--class' => \CintasCustomerSeeder::class,
        ]);

        $bundle = Bundle::create();

        $this->items = factory(Item::class, 3)
            ->create([
                'cycle_count' => 0,
                'deleted_at' => null
            ])
            ->each(function ($i) use ($bundle) {
                $i->rfid_tags()->save(factory(RFIDTag::class)->create());
                $i->bundle()->associate($bundle);
                $i->save();
            });

        $this->dryer1 = factory(Reader::class)
            ->create([
                'id' => 'Dryer 1',
                'facility_id' => config('cintas.facility_cuid')
            ]);


        $this->dryer2 = factory(Reader::class)
            ->create([
                'id' => 'Dryer 2',
                'facility_id' => config('cintas.facility_cuid')
            ]);


        $this->jsonInput =
            <<<EOT
{
  "data": [
    {
      "IP": "169.254.1.1",
      "Name": "Dryer 1",
      "Optional": "Workwear",
      "Tags": [
        {
          "EPC": "A30A20130206000000000001",
          "Timestamp": "09.10.2018 14:39",
          "Antenna": "1"
        },
        {
          "EPC": "A30A20130206000000000002",
          "Timestamp": "09.10.2018 14:39",
          "Antenna": "2"
        }
      ]
    },
    {
      "IP": "169.254.1.2",
      "Name": "Dryer 2",
      "Optional": "",
      "Tags": [
        {
          "EPC": "A30A20130206000000000003",
          "Timestamp": "09.10.2018 14:40",
          "Antenna": "2"
        },
        {
          "EPC": "7461676974726F2000000000",
          "Timestamp": "09.10.2018 14:41",
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
    public function testTagItronJson()
    {

        $response = $this->post('api/reads/dryer-read', json_decode($this->jsonInput, true));
        $response->assertStatus(200);

        $action = ScanAction::find($response->json('data.0.cuid') ?? null);
        $this->assertNotNull($action, 'Scan action missing');
        $this->assertEquals('DirtyInScan', $action->type->name, 'Wrong scan action');

        $facility = Facility::all()->first();
        $items = Item::query()->whereHas('rfid_tags', function ($query) {
            return $query->whereIn('epc', ['A30A20130206000000000001', 'A30A20130206000000000002', 'A30A20130206000000000003']);
        })->get();

        // Test item statuses
        foreach ($items as $item) {
            $item->refresh();

            $this->assertNotNull($item->location, 'Location missing');
            $this->assertEquals($facility->cuid, $item->location->cuid, 'Wrong location after register dryer read');
            $this->assertEquals(1, $item->cycle_count, 'Wrong cycle count for item');

            $this->assertEquals('AtFacilityDryerStatus', $item->last_status->status_type->name, 'Wrong last status type!');
        }

        //TODO: Test unbundeling of bundled items!

        // Test scan actions
        $scanActions = ScanAction::all();
        $this->assertEquals(2, $scanActions->count(), 'Wrong number of scan actions');

        $action = $scanActions->get(0);
        $action->refresh();
        $reader = Reader::findById('Dryer 1');
        $this->assertEquals($reader->cuid, $action->reader->cuid, 'Wrong reader for first action');
        $this->assertEquals($items->take(2)->only('cuid'), $action->items->only('cuid'), 'Wrong items in first scan action');
        $this->assertEquals('DirtyInScan', $action->type->name, 'Wrong scan action type');

        $action = $scanActions->get(1);
        $action->refresh();
        $reader = Reader::findById('Dryer 2');
        $this->assertEquals($reader->cuid, $action->reader->cuid, 'Wrong reader for second action');
        $this->assertEquals($items->get(2)->cuid, $action->items->first()->cuid, 'Wrong items in second scan action');
        $this->assertEquals('DirtyInScan', $action->type->name, 'Wrong scan action type');

        // Test capturing of unknwon EPCs
        $this->assertNotNull(RFIDTag::findByEpc('7461676974726F2000000000'), 'No tag for unknown EPC was found!');

        $this->assertEquals(RFIDTag::findByEpc('7461676974726F2000000000')->cuid, $action->unknown_rfid_tags->first()->cuid, 'Missing unknown EPC in scan action');

    }

}
