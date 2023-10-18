<?php

namespace Tests\Unit\Models;

use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ItemStatus;
use Cintas\Models\Items\ItemStatusType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ItemTest extends TestCase
{

    use RefreshDatabase;

    private $item;
    private $tag;

    private $locationFacility;
    private $locationCustomer;

    protected function setUp()
    {
        parent::setUp();

        Artisan::call('db:seed', [
            '--class' => \CintasCustomerSeeder::class,
        ]);

        $this->locationFacility = Facility::first();

        $this->locationCustomer = factory(LaundryCustomer::class)->create();
        $this->locationCustomer->served_by_facilities()->attach($this->locationFacility);

        $item = factory(Item::class)->create();
        $this->tag = factory(RFIDTag::class)->create();
        $item->rfid_tags()->save($this->tag);

        $this->item = $item->refresh();
        $this->locationFacility->refresh();
        $this->locationCustomer->refresh();

    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItemCreation()
    {
        $status = $this->item->last_status;
        $statusHistory = $this->item->status_history;

        $this->assertNotNull($status, "Status missing");

        $this->assertEquals($status, $statusHistory->last(), "Wrong last status");
        $this->assertEquals('NewStatus', $status->status_type->name);

    }

    public function testLocationChange()
    {

        $stat = $this->item->last_status->location;
        $this->assertNull($stat);

        // Test location change to facility
        $this->item->location = $this->locationFacility;
        $loc = $this->item->location;
        $lastStatus = $this->item->last_status;

        $this->assertEquals($this->locationFacility->cuid, $loc->cuid, 'Wrong item location');
        $this->assertEquals('AtFacilityStatus', $lastStatus->status_type->name, 'Wrong item status');

        // Test location change to customer
        $this->item->location = $this->locationCustomer;
        $loc = $this->item->location;
        $lastStatus = $this->item->last_status;

        $this->assertEquals($this->locationCustomer->cuid, $loc->cuid, 'Wrong item location');
        $this->assertEquals('AtCustomerStatus', $lastStatus->status_type->name, 'Wrong item status');

    }

    public function testStatuses()
    {

        $oldStatusHistory = clone $this->item->status_history;

        // Test location change by new status
        $status = new ItemStatus();
        $status->status_type()->associate(ItemStatusType::findByName('AtFacilityStatus'));
        $status->location()->associate($this->locationFacility);
        $this->item->pushStatus($status);

        $oldStatusHistory->push($status);

        $loc = $this->item->location;
        $lastStatus = $this->item->last_status;

        $newStatusHistory = $this->item->status_history;

        $this->assertEquals($this->locationFacility->cuid, $loc->cuid, 'Wrong item location');
        $this->assertEquals($status->cuid, $lastStatus->cuid, 'Status is not shown as last status');
        $this->assertEquals($status->cuid, $this->item->status_history()->latest('cuid')->first()->cuid, 'Status not added to history');
        $this->assertEquals($oldStatusHistory->count(), $newStatusHistory->count(), 'Status missing in status history');
        $this->assertEquals('AtFacilityStatus', $lastStatus->status_type->name, 'Wrong item status');
    }

    //TODO: Add item tests: remaining lifetime
}
