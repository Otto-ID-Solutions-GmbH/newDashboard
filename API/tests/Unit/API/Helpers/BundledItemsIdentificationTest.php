<?php

namespace Tests\Unit\API\Helpers;

use Cintas\Models\Facility\Bundle;
use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Item;
use Cintas\Repositories\ItemRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BundledItemsIdentificationTest extends TestCase
{

    use RefreshDatabase;

    private $itemRepository;

    private $bundle;
    private $bundledItems;
    private $unbundledItems;

    protected function setUp()
    {
        parent::setUp();
        $this->itemRepository = $this->app->make(ItemRepository::class);

        Artisan::call('db:seed', [
            '--class' => \CintasCustomerSeeder::class,
        ]);

        $this->bundle = Bundle::create();

        $this->bundledItems = factory(Item::class, 10)
            ->create([
                'deleted_at' => null
            ])
            ->each(function ($i) {
                $i->rfid_tags()->save(factory(RFIDTag::class)->create());
                $i->bundle()->associate($this->bundle);
                $i->save();
            });

        $this->unbundledItems = factory(Item::class, 10)
            ->create()
            ->each(function ($i) {
                $i->rfid_tags()->save(factory(RFIDTag::class)->create());
            });

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItemBundeling()
    {
        $epcsBundled = $this->bundledItems->random(5)->map(function ($i) {
            return $i->rfid_tags->first()->epc;
        });

        $epcsIndirectBundledItems = $this->bundledItems->map(function ($i) {
            return $i->rfid_tags->first()->epc;
        })->diff($epcsBundled);

        $epcsUnbundled = $this->unbundledItems->random(5)->map(function ($i) {
            return $i->rfid_tags->first()->epc;
        });

        $epcs = $epcsBundled->concat($epcsUnbundled);

        // Test if bundle is identified whith 0.5 threshold
        $res = $this->itemRepository->identifyBundledItems($epcs);
        $resEpcs = $res['items']->map(function ($i) {
            return $i->rfid_tags->first()->epc;
        });

        foreach ($epcsBundled as $epc) {
            $this->assertContains($epc, $resEpcs, "Missed bundled items");
        }

        // Test if bundle is skipped with 0.6 threshold when bundle onlys is activated
        $res = $this->itemRepository->identifyBundledItems($epcs, 0.6);
        $resEpcs = $res['items']->map(function ($i) {
            return $i->rfid_tags->first()->epc;
        });
        $skippedEpcs = $res['skipped_items']->map(function ($i) {
            return $i->rfid_tags->first()->epc;
        });

        // Test if direct bundle items not included in the result
        foreach ($epcsBundled as $epc) {
            $this->assertNotContains($epc, $resEpcs, "Missed single bundled items");
        }

        // Test if direct items are included in the skip list
        foreach ($epcsBundled as $epc) {
            $this->assertContains($epc, $skippedEpcs, "Direct item of skipped bundle was not added to skipped item list");
        }

        // Test if indirect bundle items are not included in the result
        foreach ($epcsIndirectBundledItems as $epc) {
            $this->assertNotContains($epc, $resEpcs, 'Bundled items incorrectly included in the result');
        }


        // Test if bundle is skipped with 0.6 threshold when bundles only is NOT activated
        $res = $this->itemRepository->identifyBundledItems($epcs, 0.6, false);
        $resEpcs = $res['items']->map(function ($i) {
            return $i->rfid_tags->first()->epc;
        });
        $skippedEpcs = $res['skipped_items']->map(function ($i) {
            return $i->rfid_tags->first()->epc;
        });

        // Test if direct bundle items are included in the result
        foreach ($epcsBundled as $epc) {
            $this->assertContains($epc, $resEpcs, "Missed single bundled items");
        }

        // Test if direct items are included in the skip list
        foreach ($epcsBundled as $epc) {
            $this->assertNotContains($epc, $skippedEpcs, "Skipped single bundled items although they should have been added");
        }

        // Test if indirect bundle items are not included in the result
        foreach ($epcsIndirectBundledItems as $epc) {
            $this->assertNotContains($epc, $resEpcs, 'Bundled items incorrectly included in the result');
        }


        //TODO: Test unknown EPCs
    }
}
