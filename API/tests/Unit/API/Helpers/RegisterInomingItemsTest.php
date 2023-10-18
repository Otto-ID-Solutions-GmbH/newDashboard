<?php

namespace Tests\Unit\API\Helpers;

use Cintas\Models\Facility\Facility;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ItemStatusType;
use Cintas\Repositories\FacilityRepository;
use Tests\TestCase;

class RegisterInomingItemsTest extends TestCase
{

    private $facilityRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->facilityRepository = $this->app->make(FacilityRepository::class);
    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIncomingItems()
    {
        $items = Item::take(10)->get();
        $facility = Facility::all()->first();

        $historyCountBefore = $items->mapWithKeys(function ($i) {
            return [$i->cuid => $i->status_history()->count()];
        });

        $statuses = $this->facilityRepository->registerIncomingItems($items, $facility);

        $items = Item::take(10)->with(['last_status.location'])->get();

        $statusType = ItemStatusType::findByName('AtFacilityStatus');

        foreach ($items as $index => $item) {
            $this->assertEquals($historyCountBefore->get($item->cuid) + 1, $item->status_history()->count(), 'Failed to add new item status into history.');
            $this->assertSame($item->last_status->cuid, $statuses->get($index)->cuid, 'Failed to associate the correct last status to an item.');
            $this->assertSame($facility->cuid, $item->location->cuid, 'Failed to associate the correct location to the item status.');
            $this->assertSame($statusType->cuid, $item->last_status->status_type->cuid, 'Failed to associate the correct status type to the item status.');
        }


    }
}
