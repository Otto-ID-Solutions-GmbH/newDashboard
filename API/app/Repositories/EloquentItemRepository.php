<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 14.10.2018
 * Time: 20:24
 */

namespace Cintas\Repositories;


use Carbon\Carbon;
use Cintas\Facades\Statistics;
use Cintas\Models\Facility\Bundle;
use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ItemStatus;
use Cintas\Models\Items\ItemStatusType;
use EndyJasmi\Cuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class EloquentItemRepository implements ItemRepository
{


    public function identifyItems($epc)
    {

        $epcs = collect($epc)->sort()->unique();

        $epcResult = RFIDTag::whereIn('epc', $epcs)
            ->with(['identifiable', 'identifiable.product', 'identifiable.customer'])
            ->get();

        $identifiedEPCs = collect();
        $items = $epcResult
            ->filter(function ($epc) {
                return $epc->identifiable !== null;
            })
            ->map(function ($epc) use ($identifiedEPCs) {
                $identifiedEPCs->push($epc->epc);
                return $epc->identifiable;
            });

        $missingEPCs = $epcs->diff($identifiedEPCs)->unique()->values()->toArray();

        //TODO: Create unmatched RFID tags for unknown EPCs asynchronously in extra worker
        if ($missingEPCs && sizeof($missingEPCs) > 0) {
            Log::channel('epc')->error("Found unknown EPCs", ['EPCs' => $missingEPCs]);
        }

        return [
            "items" => $items,
            "unknown_epcs" => $missingEPCs
        ];
    }

    public function identifyBundledItems($epc, $threshold = null, $bundleItemsOnly = true)
    {

        if (!$threshold) {
            $threshold = config('cintas.process.bundle_threshold', 0.5);
        }

        $identificationResult = $this->identifyItems($epc);
        $directItems = $identificationResult['items']->keyBy('cuid');
        $directItems->load('bundle', 'bundle.items');

        $allItems = Collection::make();
        $bundleCount = Collection::make();

        foreach ($directItems as $dItem) { // Iterate over all direct items

            $bundle = $dItem->bundle ?? null;

            // Check if bundle exists
            if ($bundle) {
                if ($bundleCount->has($bundle->cuid)) {
                    // Bundle existed, increase amount of items and add item to list
                    $act = $bundleCount->get($bundle->cuid);

                    $act['count'] = $act['count'] + 1;

                    $bundleCount->put($bundle->cuid, $act);
                } else {
                    // First item with that bundle, create an entry with the bundle and initial count
                    $bundleCount->put($bundle->cuid, [
                        'bundle' => $bundle,
                        'count' => 1
                    ]);
                }
            }
        }

        // Add additional items from bundles that meet the given threshold value
        foreach ($bundleCount as $bundleData) {
            $bundle = $bundleData['bundle'];
            $count = $bundleData['count'];

            if ($count > 0 && $count >= ($threshold * $bundle->items()->count())) {
                foreach ($bundle->items ?? [] as $bItem) { // Iterate over all items in the bundle
                    $allItems->put($bItem->cuid, $bItem);
                }
            }
        }

        if (!$bundleItemsOnly) {
            $allItems = $allItems->merge($directItems);
            $skippedItems = collect();
        } else {
            $skippedItems = $directItems->diff($allItems);
        }

        return [
            'items' => $allItems,
            'skipped_items' => $skippedItems,
            'unknown_epcs' => $identificationResult['unknown_epcs']
        ];

    }

    public function bundleItems($items, $unbundleFirst = true)
    {

        if ($unbundleFirst) {
            $this->unbundleItems($items);
        }

        $bundle = new Bundle();
        $bundle->save();

        $bundle->items()->saveMany($items);

        return $bundle;
    }


    public function unbundleItems($items)
    {
        //TODO: Optimize by removing items in the list, that were already unbundled because of unbundeling prior items in the same bundle
        $bundles = collect();
        $items->load('bundle');

        foreach ($items as $item) {
            if ($item->bundle) {
                $bundles->push($item->bundle->cuid);
            }
        }

        Bundle::destroy($bundles->unique());
    }

    /**
     * @inheritdoc
     */
    public function setItemStatuses(&$items, $statusType, $location = null, $timestamp = null, $refreshItems = false)
    {

        $statusType = is_string($statusType) ? ItemStatusType::findByName($statusType) : $statusType ?? ItemStatusType::findByName('AtFacilityStatus');

        $statusData = collect();
        $timestamp = new Carbon($timestamp) ?? Carbon::now();
        $timestamp = $timestamp->setTimezone('UTC');

        $statusCuids = collect();

        if ($location) {
            $locCuid = $location->cuid;
            $locType = Statistics::getMorphAliasFromModel($location);
        } else {
            $locCuid = null;
            $locType = null;
        }

        // Bulk create item status data
        foreach ($items as $item) {
            $cuid = Cuid::make();
            $statusCuids->push($cuid);
            $statusData->put($item->cuid, collect([
                'cuid' => $cuid,
                'created_at' => $timestamp->toDateTimeString(),
                'updated_at' => $timestamp->toDateTimeString(),
                'location_id' => $locCuid,
                'location_type' => $locType,
                'item_status_type_id' => $statusType->cuid,
                'customer_id' => config('cintas.customer_cuid'),
                'item_id' => $item->cuid
            ]));
        }

        // Insert Item Statuses
        ItemStatus::insert($statusData->values()->toArray());

        // Update last status of items
        foreach ($items as $item) {
            $item->last_status_id = $statusData->get($item->cuid)->get('cuid');
            $item->save();
        }

        if ($refreshItems) {
            $items = $items->fresh();
        }

        return $statusCuids->toArray();
    }

    public function getItemStatuses($itemCuid) {
        return Item::findOrFail($itemCuid)->status_history;
    }
}
