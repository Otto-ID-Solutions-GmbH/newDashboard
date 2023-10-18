<?php

namespace Cintas\Jobs\Statistics;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ProductType;
use Cintas\Models\Statistics\NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic;
use EndyJasmi\Cuid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ComputeDeliveryReturnPerProductTypePerLocationStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startDateString;

    private $typeIds;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($startDateString = null)
    {
        if ($startDateString) {
            $this->startDateString = $startDateString;
        } /*else {
            $lastDate = Carbon::make(NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic::query()
                ->max('date'));
            if (!$lastDate) {
                $lastDate = Carbon::now('UTC');
                $this->startDateString = $lastDate->toIso8601String();
            } else {
                $this->startDateString = $lastDate->addDay()->toIso8601String();
            }

        }

        $this->typeIds = ProductType::has('products.items')->pluck('cuid');
        */
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $startDateString = $this->startDateString;

        $prev = null;

        $format = 'Y-m-d';
        $timezone = 'UTC';
        $start = Carbon::parse($startDateString, 'UTC')->timezone($timezone)->startOfDay();
        $end = Carbon::now($timezone);
        $interval = config('statistics.incoming_outgoing_items.interval', '1 day');
        $periodRange = new CarbonPeriod($start, $interval, $end);

        $locations = LaundryCustomer::all();
        $insertData = [];

        Log::info("Started computing NoDeliveredAndReturnedItemsPerProductTypePerLocation statistics from $start to $end (interval $interval)...");

        foreach ($periodRange as $key => $date) {
            foreach ($locations as $location) {
                $locationId = $location->cuid;
                $exists = $exists = NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic::query()
                    ->where('date', '=', $date)
                    ->where('location_id', '=', $locationId)
                    ->first();
                if ($exists) {
                    Log::warning("Data for date $date and location $location->name already existed, skipping...");
                } else {
                    $this->addEmptyData($date, $this->typeIds);
                    $cleanIncome = $this->getItemCountForIncomingItems($date, 'cjpl2rcis000b90qyl8su9szl', $locationId)->get();
                    $soilIncome = $this->getItemCountForIncomingItems($date, 'cjpl2rcis000a90qye7pr97kc', $locationId)->get();
                    $unknownIncome = $this->getItemCountForIncomingItems($date, 'cjpl2rcis000d90qy3n1y6hd1', $locationId)->get();
                    $outgoing = $this->getItemCountForOutgoingItems($date, 'cjpl2rcis000c90qygi05ed8g', $locationId)->get();

                    $this->processResult($date, $cleanIncome, 'no_items_clean_in');
                    $this->processResult($date, $soilIncome, 'no_items_soil_in');
                    $this->processResult($date, $unknownIncome, 'no_items_unknown_in');
                    $this->processResult($date, $outgoing, 'no_items_out');
                }
            }

        }

        Log::info("Completed computing statistics.");

    }

    private function processResult($date, $data, $column)
    {
        $insertData = [];
        foreach ($data as $datum) {
            $exists = NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic::whereDate('date', '=', $date)
                ->where('location_id', '=', $datum->location_id)
                ->where('product_type_id', '=', $datum->product_type_id)
                ->first();

            if (!$exists) {
                $res = [
                    'cuid' => Cuid::make(),
                    'created_at' => Carbon::now('UTC')->toDateTimeString(),
                    'updated_at' => Carbon::now('UTC')->toDateTimeString(),
                    'date' => $date,
                    'location_id' => $datum->location_id,
                    'location_type' => $datum->location_type ?? 'Unknown',
                    'product_type_id' => $datum->product_type_id,
                    $column => $datum->item_count ?? 0,
                ];
                array_push($insertData, $res);
            } else {
                $exists->$column = $datum->item_count;
                $exists->updated_at = Carbon::now('UTC')->toDateTimeString();
                $exists->save();
            }
        }

        NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic::insert($insertData);

    }

    private function addEmptyData($date, $typeIds)
    {
        $insertData = [];
        foreach ($typeIds as $typeId) {
            $res = [
                'cuid' => Cuid::make(),
                'created_at' => Carbon::now('UTC')->toDateTimeString(),
                'updated_at' => Carbon::now('UTC')->toDateTimeString(),
                'date' => $date,
                'location_id' => 'cjpl2rcjx000e90qyy8yf51m2',
                'location_type' => 'LaundryCustomer',
                'product_type_id' => $typeId,

            ];
            array_push($insertData, $res);
        }
        NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic::insert($insertData);
    }

    private function getItemCountForOutgoingItems($timestamp, $scanTypeId, $fromLocationId)
    {
        return Item::query()
            ->join('item_scan_action', 'items.cuid', '=', 'item_scan_action.item_id')
            ->join('scan_actions', 'item_scan_action.scan_action_id', '=', 'scan_actions.cuid')
            ->join('products', 'product_id', '=', 'products.cuid')
            ->join('item_statuses', 'item_scan_action.old_status_id', '=', 'item_statuses.cuid')
            ->where('scan_actions.scan_type_id', '=', $scanTypeId)
            ->whereDate('scan_actions.created_at', $timestamp)
            ->where('item_statuses.location_id', '=', $fromLocationId)
            ->groupBy('products.product_type_id')
            ->selectRaw('count(distinct items.cuid) as item_count, products.product_type_id as product_type_id, \'cjpl2rcjx000e90qyy8yf51m2\' as location_id, \'LaundryCustomer\' as location_type');
    }

    private function getItemCountForIncomingItems($timestamp, $scanTypeId, $toLocationId)
    {
        return Item::query()
            ->join('item_scan_action', 'items.cuid', '=', 'item_scan_action.item_id')
            ->join('scan_actions', 'item_scan_action.scan_action_id', '=', 'scan_actions.cuid')
            ->join('products', 'items.product_id', '=', 'products.cuid')
            ->join('item_statuses', 'item_scan_action.old_status_id', '=', 'item_statuses.cuid')
            ->where('scan_actions.scan_type_id', '=', $scanTypeId)
            ->whereDate('scan_actions.created_at', $timestamp)
            ->where('item_statuses.location_id', '<>', $toLocationId)
            ->groupBy('products.product_type_id')
            ->selectRaw('count(distinct items.cuid) as item_count, products.product_type_id as product_type_id, \'cjpl2rcjx000e90qyy8yf51m2\' as location_id, \'LaundryCustomer\' as location_type');
    }

}
