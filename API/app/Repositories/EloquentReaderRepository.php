<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 16.10.2018
 * Time: 14:54
 */

namespace Cintas\Repositories;


use Carbon\Carbon;
use Cintas\Events\OutScanRegistered;
use Cintas\Facades\Statistics;
use Cintas\Http\MessageTypes\ReadDataInput;
use Cintas\Http\MessageTypes\TagDataInput;
use Cintas\Models\Actions\OutScanAction;
use Cintas\Models\Actions\ScanAction;
use Cintas\Models\Actions\ScanActionType;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Facility\Reader;
use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ItemStatusType;
use Cintas\Models\Polymorphism\LocationContract;
use EndyJasmi\Cuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;


class EloquentReaderRepository implements ReaderRepository
{

    private $itemRepository;
    private $facilityRepository;

    public function __construct(ItemRepository $itemRepository, FacilityRepository $facilityRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->facilityRepository = $facilityRepository;
    }

    /**
     * @param TagDataInput[] $tagReads
     * @param Reader|string $reader the reader or the CUID or named ID of the reader
     * @param string $scanType the type of incoming scan
     * @param bool $unbundleItems if true, incoming items are unbundled
     * @param ItemStatusType | string | null $statusType the status type
     * @return mixed the scan actions that were created
     */
    protected function registerIncomingRead($tagReads, $reader, $scanType, $statusType, $unbundleItems = false)
    {
        $result = new Collection();

        if (!$reader) {
            throw new \BadMethodCallException('No reader was provided!');
        }

        if (is_string($reader)) {
            $reader = Reader::where('cuid', '=', $reader)
                ->orWhere('id', '=', $reader)
                ->first();
        }

        $facility = $reader->facility;

        // Create scan action
        $scanAction = new ScanAction();
        $scanAction->reader()->associate($reader);
        $scanAction->type()->associate(ScanActionType::findByName($scanType));
        $scanAction->save();

        // Get items from tag read data mapped by their EPC
        $res = $this->getEpcToItemMapForRead($tagReads);
        $items = $res['items'];
        $itemCuids = $items->pluck('cuid');
        $unknownEPCs = $res['unknown_epcs'];

        // Map tag data by the EPCs
        $tagDataMap = collect($tagReads)->keyBy(function ($tD) {
            return $tD->epc;
        });

        // Increase cycle count only when item has been washed
        if ($scanType === 'DirtyInScan') {
            Item::whereIn('cuid', $itemCuids)
                ->increment('cycle_count');
        }

        // Attach items to scan action
        $scanActionAttachInfo = $this->getItemToScanActionAttachTableInfo($items, $tagDataMap, $scanAction)->toArray();
        DB::table('item_scan_action')
            ->insert($scanActionAttachInfo);

        // Register and attach unknown EPCs
        $unknwonTags = $this->registerUnknownEPCs($unknownEPCs);
        $scanAction->unknown_rfid_tags()->saveMany($unknwonTags);

        // Create new statuses for items
        $this->facilityRepository->registerIncomingItems($items, $facility, null, $unbundleItems, $statusType);

        //TODO: Register new statuses of items with the item-scanaction information

        // push the scan action to the results
        $result->push($scanAction);

        return $result;
    }

    /**
     * @param $tagReads
     * @param $reader
     * @return ScanAction
     * @throws \Exception
     */
    protected function registerTableRead($tagReads, $reader)
    {

        if (!$reader) {
            throw new \BadMethodCallException('No reader was provided!');
        }

        if (is_string($reader)) {
            $reader = Reader::where('cuid', '=', $reader)
                ->orWhere('id', '=', $reader)
                ->first();
        }

        $facility = $reader->facility;

        if (!$facility) {
            throw new \Exception('No facility is associated with the reader!');
        }

        $scanAction = new ScanAction();
        $scanAction->reader()->associate($reader);
        $scanAction->type()->associate(ScanActionType::findByName('TableScan'));
        $scanAction->save();

        // Get items from tag read data mapped by their EPC
        // Get items from tag read data mapped by their EPC
        $res = $this->getEpcToItemMapForRead($tagReads);
        $items = $res['items'];
        $unknownEPCs = $res['unknown_epcs'];

        // Map tag data by the EPCs
        $tagDataMap = collect($tagReads)->keyBy(function ($tD) {
            return $tD->epc;
        });

        // Unbundle and bundle all items
        $bundle = $this->itemRepository->bundleItems($items, true);

        // Attach items to scan action
        $scanActionAttachInfo = $this->getItemToScanActionAttachTableInfo($items, $tagDataMap, $scanAction)->toArray();
        DB::table('item_scan_action')
            ->insert($scanActionAttachInfo);

        // Register items with the facility as incoming items
        $this->facilityRepository->registerIncomingItems($items->values(), $facility, null, false, 'AtFacilityTableStatus');

        // Register and attach unknown EPCs
        $unknwonTags = $this->registerUnknownEPCs($unknownEPCs);
        $scanAction->unknown_rfid_tags()->saveMany($unknwonTags);

        return $scanAction;
    }

    protected function registerOutgoingRead($tagReads, $reader, $target)
    {

        if (!$reader) {
            throw new \BadMethodCallException('No reader was provided!');
        }

        if (is_string($reader)) {
            $reader = Reader::where('cuid', '=', $reader)
                ->orWhere('id', '=', $reader)
                ->first();
        }

        $scanAction = new ScanAction();
        $scanAction->reader()->associate($reader);
        $scanAction->type()->associate(ScanActionType::findByName('OutScan'));
        $scanAction->save();
        $outScanAction = new OutScanAction();
        $scanAction->out_scan_action()->save($outScanAction);

        // Set target location of scan action
        if ($target) {
            $outScanAction->location()->associate($target);
            $outScanAction->save();
        }

        // Get direct and indirect items via bundle information
        $res = $this->getEpcToItemMapForRead($tagReads, true);
        $items = $res['items'];
        $unknownEPCs = $res['unknown_epcs'];
        $skippedItems = $res['skipped_items'];

        // Map tag data by the EPCs
        $tagDataMap = collect($tagReads)->keyBy(function ($tD) {
            return $tD->epc;
        });

        // Attach items to scan action
        $scanActionAttachInfo = $this->getItemToScanActionAttachTableInfo($items, $tagDataMap, $scanAction)->toArray();
        DB::table('item_scan_action')
            ->insert($scanActionAttachInfo);

        // Register items with the facility as incoming items
        $this->facilityRepository->registerOutgoingItems($items->values(), $target, null);

        // Attach skipped items to scan action
        $skippedItemsAcanActionAttachInfo = $this->getItemToScanActionAttachTableInfo($skippedItems, $tagDataMap, $scanAction, true)->toArray();
        DB::table('skipped_item_scan_action')
            ->insert($skippedItemsAcanActionAttachInfo);

        // Register and attach unknown EPCs
        $unknwonTags = $this->registerUnknownEPCs($unknownEPCs);
        $scanAction->unknown_rfid_tags()->saveMany($unknwonTags);

        // Dispatch event, that outgoing scan action was registered
        event(new OutScanRegistered($outScanAction));

        return $scanAction;
    }

    public function registerDryerIncomingItems($input)
    {
        $result = new Collection();


        foreach ($input as $read) {
            $reader = Reader::findById($read->name);
            $result = $result->concat($this->registerIncomingRead($read->tags, $reader, 'DirtyInScan', 'AtFacilityDryerStatus', true));
        }

        return $result;
    }


    public function registerCleanIncomingItems($input)
    {
        $result = new Collection();

        foreach ($input as $read) {
            $reader = Reader::where('id', '=', $read->name)->first();
            //TODO: Change return type of function to single read instead of collection like for outgoing reads
            $result = $result->concat($this->registerIncomingRead($read->tags, $reader, 'CleanInScan', 'AtFacilityCleanStatus', false));
        }

        return $result;

    }

    /**
     * @param ReadDataInput[] $input a list of reads and the EPCs identified during the read
     * @param string | LaundryCustomer | null $target the target customer
     * @return \Illuminate\Support\Collection<ScanAction> a collection of scan actions created
     */
    public function registerOutgoingItems($input, $target = null)
    {

        $result = new Collection();

        foreach ($input as $read) {
            $reader = Reader::where('id', '=', $read->name)->first();
            $result = $result->push($this->registerOutgoingRead($read->tags, $reader, $target));
        }

        return $result;

    }

    public function registerBundlingOfItems($input)
    {

        $result = new Collection();

        foreach ($input as $read) {
            $reader = Reader::where('id', '=', $read->name)->first();
            try {
                $result = $result->push($this->registerTableRead($read->tags, $reader));
            } catch (\Exception $e) {
                Log::error("Error processing item bundling: " . $e->getMessage());
            }
        }

        return $result;

    }

    /**
     * @param TagDataInput[] | Collection<TagDataInput> $tagReads
     * @param bool $identifyBundleItemsOnly
     * @return mixed
     */
    private function getEpcToItemMapForRead($tagReads, $identifyBundleItemsOnly = false)
    {

        if (!($tagReads instanceof BaseCollection)) {
            $tagReads = collect($tagReads);
        }

        $epcs = $tagReads->map(function ($t) {
            return $t->epc;
        });

        if ($identifyBundleItemsOnly) {
            $res = $this->itemRepository->identifyBundledItems($epcs->toArray(), config('cintas.process.bundle_threshold'), true);
        } else {
            $res = $this->itemRepository->identifyItems($epcs->toArray());
            $res['skipped_items'] = collect();
        }

        $items = $res['items']->keyBy(function ($item) {
            return $item->rfid_tags->first()->epc;
        });

        $skippedItems = $res['skipped_items']->keyBy(function ($item) {
            return $item->rfid_tags->first()->epc;
        });

        return [
            'items' => $items,
            'skipped_items' => $skippedItems,
            'unknown_epcs' => $res['unknown_epcs']
        ];
    }

    private function getItemToScanActionAttachMap($epcToItemsMap, $tagDataMap)
    {
        $map = collect();

        foreach ($epcToItemsMap as $epc => $item) {
            $tD = $tagDataMap->get($epc);
            if ($tD) {
                $timestamp = new Carbon($tD->timestamp) ?? Carbon::now();
                $timestamp = $timestamp->timezone('UTC')->toDateTimeString();
                $info = [
                    'cuid' => Cuid::make(),
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'read_at' => $timestamp,
                    'antenna' => $tD->antenna ?? null,
                    'find_type' => 'directEpc'
                ];
                $map->put($item->cuid, $info);
            }
        }

        return $map;
    }

    private function getItemToScanActionAttachTableInfo($epcToItemsMap, $tagDataMap, $scanAction, $skippedItems = false)
    {
        $map = collect();

        foreach ($epcToItemsMap as $epc => $item) {
            $tD = $tagDataMap->get($epc);
            $timestamp = new Carbon($tD->timestamp ?? null) ?? Carbon::now();
            $timestamp = $timestamp->timezone('UTC')->toDateTimeString();
            $info = [
                'cuid' => Cuid::make(),
                'item_id' => $item->cuid,
                'scan_action_id' => $scanAction->cuid,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'read_at' => $timestamp,
                'antenna' => $tD->antenna ?? null,
                'old_status_id' => $item->last_status_id // Add last status info before moving the item
            ];

            if (!$skippedItems) {
                $info['find_type'] = $tD ? 'directEpc' : 'bundle';
            }

            $map->push($info);
        }

        return $map;
    }

    /**
     * @param Collection<string> $unknownEPCs
     */
    private function registerUnknownEPCs($unknownEPCs)
    {
        $tags = collect();
        foreach ($unknownEPCs as $unknownEPC) {
            $tag = RFIDTag::findByEpc($unknownEPC);
            if (!$tag) {
                $tag = new RFIDTag();
                $tag->epc = $unknownEPC;
                $tag->save();
            }
            $tags->push($tag);
        }
        return $tags;
    }

    /**
     * Retrieves a list of scan actions within a given time
     * @param Carbon | string $start
     * @param Carbon | string $end
     * @param string $type
     * @return Collection<ScanAction> mixed
     */
    public function getScanActionsInTime($start = null, $end = null, $type = null, $sortBy = null, $sortDir = 'asc')
    {
        return $this->getScanActionsInTimeQuery($start, $end, $type, $sortBy, $sortDir)->get();
    }

    public function getScanActionsInTimePaginated($start = null, $end = null, $type = null, $sortBy = null, $sortDir = 'asc')
    {
        return $this->getScanActionsInTimeQuery($start, $end, $type, $sortBy, $sortDir)->paginate(config('cintas.view.items_per_page'));
    }

    public function getScanActionsInTimeQuery($start = null, $end = null, $type = null, $sortBy = null, $sortDir = 'asc')
    {

        $actionsQuery = ScanAction::query();

        if ($sortBy && !Schema::hasColumn((new ScanAction)->getTable(), $sortBy)) {
            Log::error("Trying to sort by non existing column '$sortBy' in ScanActions");
            $sortBy = 'cuid';
        }

        if ($start) {
            $actionsQuery = $actionsQuery->where('created_at', '>=', Carbon::make($start)->timezone('UTC'));
        }

        if ($end) {
            $actionsQuery = $actionsQuery->where('created_at', '<=', Carbon::make($end)->timezone('UTC'));;
        }

        if ($type) {
            $actionsQuery = $actionsQuery
                ->onlyOfType($type);
        }

        if ($sortBy && $sortDir) {
            $actionsQuery = $actionsQuery
                ->orderBy($sortBy, $sortDir);
        } else {
            $actionsQuery = $actionsQuery
                ->orderBy('created_at', 'desc');
        }

        return $actionsQuery;
    }

    public function registerStocktaking($input)
    {

        $location = Statistics::findPolymorphModel($input->location_type, $input->location_id);

        $entries = collect($input->stocktaking_entries);
        $items = Item::find($entries->flatMap(function ($entry) {
            return $entry->item_ids ?? [];
        }));

        if ($location instanceof Facility) {
            $statusType = ItemStatusType::findByName('AtFacilityStatus');
        } else if ($location instanceof LocationContract) {
            $statusType = ItemStatusType::findByName('AtCustomerStatus');
        } else {
            $statusType = ItemStatusType::findByName('UnknownStatus');
        }

        $timestamp = $input->updated_at ? $input->updated_at : ($input->created_at ? $input->created_at : Carbon::now('UTC'));

        $ids = $this->itemRepository->setItemStatuses($items, $statusType, $location, $timestamp);

        $input->setCuid($input->cuid);
        foreach ($input->stocktaking_entries as $entry) {
            $entry->setCuid($entry->cuid);
        }

        return $input;
    }

}
