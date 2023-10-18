<?php

namespace Tests\Unit\API\Helpers;

use Cintas\Models\Items\Item;
use Cintas\Repositories\ItemRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ItemBundelingTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @var ItemRepository
     */
    private $itemRepository;

    private $items;

    protected function setUp()
    {
        parent::setUp();

        $this->itemRepository = $this->app->make(ItemRepository::class);

        Artisan::call('db:seed', [
            '--class' => \CintasCustomerSeeder::class,
        ]);

        $this->items = factory(Item::class, 20)->create();
    }

    public function testBundeling()
    {
        // Test bundeling when no bundles exist
        $itemsToBundle = $this->items->random(10);
        $otherItems = $this->items->diff($itemsToBundle);

        $bundle = $this->itemRepository->bundleItems($itemsToBundle);
        $bundleCuid = $bundle->cuid;

        $this->assertNotNull($bundle, 'Bundle missing');
        $this->assertEquals($itemsToBundle->only('cuid'), $bundle->items->only('cuid'), 'Wrong items in bundle');

        // Test bundeling of items without and with existing bundle
        $newBundleItems = $itemsToBundle->random(5)->concat($otherItems->random(5));
        $oldBundledItems = $itemsToBundle->diff($newBundleItems);

        // Create additional bundle that shall not be unbundeled
        $newBundle2Items = $otherItems->diff($newBundleItems)->diff($oldBundledItems);
        $newBundle2 = $this->itemRepository->bundleItems($newBundle2Items);

        $newBundle = $this->itemRepository->bundleItems($newBundleItems);

        // Assert new bundle exists
        $this->assertEquals($newBundleItems->only('cuid'), $newBundle->items->only('cuid'));
        $this->assertDatabaseMissing('bundles', ['cuid' => $bundleCuid]);

        // Assert items of old bundle were unbundled
        foreach ($oldBundledItems as $obi) {
            $this->assertNull($obi->bundle);
        }

        // Assert items from second bundle are still bundled
        $this->assertEquals($newBundle2Items->only('cuid'), $newBundle2->items->only('cuid'));

    }
}
