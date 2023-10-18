<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 31.10.2018
 * Time: 18:21
 */

namespace Cintas\Repositories;


use Carbon\CarbonPeriod;
use Cintas\Facades\Statistics;
use Cintas\Models\Actions\OutScanAction;
use Cintas\Models\Actions\ScanAction;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\Product;
use Cintas\Models\Statistics\NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic;

class EloquentProductStatisticsRepository implements ProductStatisticsRepository
{

    public function getLifecycleDeltaPerProduct()
    {
        // Retrieve only items that have been sorted out
        $itemsQuery = Item::query()
            ->onlyTrashed()
            ->with(['product']);

        $items = $itemsQuery->get();

        $groupedData = $items
            ->groupBy(function ($item) {
                return $item->product->cuid ?? 'Unknown';
            })->reduce(function ($result, $group) {
                $result = $result ?? collect([]);

                $prod = $group->first()->product;
                $cuid = $prod->cuid ?? null;
                $label = $prod->label ?? 'Unknown';

                $noItemsInGroup = $group->count();
                $sumRemaininLifetime = $group->reduce(function ($result, $item) {
                    return $result + $item->remaining_lifetime;
                }, 0);
                $sumCycleCount = $group->reduce(function ($result, $item) {
                    return $result + $item->cycle_count;
                }, 0);

                $avgRemainingLifetime = $sumRemaininLifetime / $noItemsInGroup;
                $avgCycleCount = $sumCycleCount / $noItemsInGroup;

                return $result->push(
                    collect([
                        'item_cuid' => $cuid,
                        'no_items_in_group' => $noItemsInGroup,
                        'expected_lifetime' => $prod->expected_lifetime,
                        'avg_cycle_count' => $avgCycleCount,
                        'name' => $label,
                        'value' => $avgRemainingLifetime
                    ])
                );

            });

        $result = $groupedData ?? collect();

        return $result->sortByDesc(function ($entry) {
            return $entry['value'];
        });

    }

    public function getAvgTurnaroundTimeByProductType($period, $start = null, $end = null, $customer = null)
    {
        if (!$period) {
            $period = 'since 1 month';
        }
        $formatting = Statistics::getPeriodFormatting($period, $start, $end);
        $format = $formatting['string_format'];
        $start = $formatting['start_date'];
        $end = $formatting['end_date'];
        $interval = $formatting['interval'];

        $actionsQuery = ScanAction::query()
            ->where('created_at', '>=', $start->timezone('UTC'))
            ->where('created_at', '<=', $end->timezone('UTC'))
            ->where('scan_type_id', '<>', 'cjpl2rcis000d90qy3n1y6hd1')
            ->with(['items.product.product_type'])
            ->orderBy('created_at');

        $actions = $actionsQuery->get();

        $itemsActionsMap = collect();

        // Get list of actions for each item
        foreach ($actions as $action) {
            foreach ($action->items as $item) {
                if ($itemsActionsMap->has($item->cuid)) {
                    $actionCollection = $itemsActionsMap->get($item->cuid);
                } else {
                    $actionCollection = [
                        'item' => $item,
                        'actions' => collect()
                    ];
                }
                $actionCollection['actions']->push($action);
                $itemsActionsMap->put($item->cuid, $actionCollection);
            }
        }

        // Compute total turnaround time per item
        $itemsTurnaroundTime = collect();
        foreach ($itemsActionsMap as $cuid => $data) {
            $item = $data['item'];
            $actions = $data['actions'];

            // Search for outgoing scan actions
            for ($o = 0; $o < $actions->count(); $o++) {
                $aO = $actions->get($o);

                // If action is of outgoing type, search for the next incoming type
                if ($aO->type->name == 'OutScan') {

                    // If a customer is set, only process out-scans to that customer. Otherwise, process all out-scan actions
                    if (($customer && $aO->location->cuid == $customer->cuid) || !$customer) {
                        for ($i = $o + 1; $i < $actions->count(); $i++) {
                            $aI = $actions->get($i);

                            // Incoming scan action found; compute turnaround time for the pair
                            if ($aI->type->name == 'CleanInScan' || $aI->type->name == 'DirtyInScan') {
                                $turnaroundTime = $aI->created_at->diffInDays($aO->created_at);

                                if ($itemsTurnaroundTime->has($item->cuid)) {
                                    $data = $itemsTurnaroundTime->get($item->cuid);
                                    $oldTurnaroundTime = $data['total_turnaround'];
                                    $oldNoTurnarounds = $data['no_turnarounds'];
                                    $data['total_turnaround'] = $oldTurnaroundTime + $turnaroundTime;
                                    $data['no_turnarounds'] = $oldNoTurnarounds + 1;
                                } else {
                                    $data = [
                                        'item' => $item,
                                        'total_turnaround' => $turnaroundTime,
                                        'no_turnarounds' => 1
                                    ];
                                }
                                $itemsTurnaroundTime->put($item->cuid, $data);

                            }
                        }
                    }
                }

            }
        }

        // Group item turnaround time by product type
        $groupedData = $itemsTurnaroundTime
            ->groupBy(function ($data) {
                return $data['item']->product->product_type->cuid;
            })->reduce(function ($result, $group) {
                $result = $result ?? collect([]);

                $item = $group->first()['item'];
                $prod = $item->product->product_type;
                $cuid = $prod->cuid;
                $label = $prod->label;

                $sumAvgTurnaroundTime = $group->reduce(function ($result, $data) {
                    return $result + $data['total_turnaround'];
                }, 0);
                $sumNoTurnarounds = $group->reduce(function ($result, $data) {
                    return $result + $data['no_turnarounds'];
                }, 0);

                $avgTurnaroundTime = $sumAvgTurnaroundTime / $sumNoTurnarounds;

                return $result->push(
                    collect([
                        'type_cuid' => $cuid,
                        'avg_no_turnarounds' => $sumNoTurnarounds / $group->count(),
                        'name' => $label,
                        'value' => $avgTurnaroundTime
                    ])
                );

            });

        $result = $groupedData ?? collect();

        return $result->sortByDesc(function ($entry) {
            return $entry['value'];
        });

    }

    public function getGroupedAvgCycleCount()
    {
        $groups = [
            [0, 10],
            [11, 20],
            [21, 30],
            [31, 40],
            [41, 50],
            [51, 60],
            [61, 70],
            [71, 80],
            [81, 90],
            [91, 100],
            [101, 'inf']
        ];

        $data = collect();

        foreach ($groups as $groupLimit) {
            $min = $groupLimit[0];
            $max = $groupLimit[1];

            $filteredItemsCountQuery = Item::onlyCirculating()
                ->where('cycle_count', '>=', $min);

            if ($max !== 'inf') {
                $name = "[$min, $max]";
                $filteredItemsCountQuery = $filteredItemsCountQuery
                    ->where('cycle_count', '<=', $max);
            } else {
                $name = "> $min";
            }

            $data->push([
                'name' => $name,
                'value' => $filteredItemsCountQuery->count()
            ]);
        }

        $totalAvgCycleCount = Item::onlyCirculating()->avg('cycle_count');

        return [
            'chart_data' => $data->toArray(),
            'total_avg_cycle_count' => $totalAvgCycleCount
        ];

    }

    public function getAvgCycleCountByProduct()
    {
        $result = Product::query()
            ->has('items')
            ->get()
            ->map(function ($p) {
                return [
                    'name' => $p->name,
                    'value' => $p->avg_cycle_count,
                    'avg_age_in_days' => $p->avg_age_in_days,
                    'no_old_items' => $p->no_old_items
                ];
            });

        return $result;

    }

    public function getDeliveredProductsByCustomerTimeline($period, $start = null, $end = null)
    {

        if (!$period) {
            $period = 'since 2 weeks';
        }
        $formatting = Statistics::getPeriodFormatting($period, $start, $end);
        $format = $formatting['string_format'];
        $start = $formatting['start_date'];
        $end = $formatting['end_date'];
        $interval = $formatting['interval'];
        $periodRange = new CarbonPeriod($start, $interval, $end);

        $result = collect();
        $prev = null;

        foreach ($periodRange as $key => $date) {

            if ($prev) {

                $series = collect();
                $query = OutScanAction::query()
                    ->with(['scan_action', 'location'])
                    ->where('created_at', '>=', $prev)
                    ->where('created_at', '<', $date);

                $actions = $query->get();

                foreach ($actions as $action) {
                    $customer = $action->location;
                    $customerId = $customer->cuid ?? 'Unknown';
                    $noItems = $action->scan_action->items()->count();

                    $noPerGroup = Item::query()
                        ->join('item_scan_action', 'items.cuid', '=', 'item_scan_action.item_id')
                        ->join('scan_actions', 'scan_actions.cuid', '=', 'item_scan_action.scan_action_id')
                        ->join('out_scan_actions', 'out_scan_actions.scan_action_id', '=', 'scan_actions.cuid')
                        ->join('products', 'product_id', '=', 'products.cuid')
                        ->join('product_types', 'product_type_id', '=', 'product_types.cuid')
                        ->where('scan_actions.cuid', '=', $action->scan_action->cuid)
                        ->where('out_scan_actions.location_id', '=', $customerId)
                        ->groupBy('product_types.cuid', 'product_types.name')
                        ->selectRaw('count(distinct items.cuid) as count, product_types.cuid as cuid, product_types.name as name')
                        ->get();

                    if ($series->has($customerId)) {
                        $oldCount = $series->get($customerId)['value'];
                        $oldPerProduct = $series->get($customerId)['per_product'];

                        foreach ($noPerGroup as $data) {
                            if ($oldPerProduct->has($data->cuid)) {
                                $oldCount = $oldPerProduct->get($data->cuid)['value'];
                                $oldPerProduct->put($data->cuid, [
                                    'name' => $data->name,
                                    'value' => $oldCount + $data->count
                                ]);
                            } else {
                                $oldPerProduct->put($data->cuid, [
                                    'name' => $data->name,
                                    'value' => +$data->count
                                ]);
                            }
                        }

                        $series->put($customerId, [
                            'name' => $customer->label ?? 'Unknown',
                            'value' => $oldCount + $noItems,
                            'per_product' => $oldPerProduct
                        ]);

                    } else {

                        $series->put($customerId, [
                            'name' => $customer->label ?? 'Unknown',
                            'value' => +$noItems,
                            'per_product' => $noPerGroup->mapWithKeys(function ($r) {
                                return [$r->cuid => [
                                    'name' => $r->name,
                                    'value' => $r->count
                                ]];
                            })
                        ]);
                    }

                }

                $result->push([
                    'name' => $prev->format($format),
                    'series' => $series
                        ->values()
                        ->map(function ($s) {
                            return [
                                'name' => $s['name'],
                                'value' => $s['value'],
                                'per_product' => $s['per_product']->values()
                            ];
                        })
                ]);


            }
            $prev = $date;
        }

        return $result->all();

    }

    public function getReturnedProductsByCustomerTimeline($period, $start = null, $end = null)
    {

        if (!$period) {
            $period = 'since 2 weeks';
        }
        $formatting = Statistics::getPeriodFormatting($period, $start, $end);
        $format = $formatting['string_format'];
        $start = $formatting['start_date'];
        $end = $formatting['end_date'];
        $interval = $formatting['interval'];
        $periodRange = new CarbonPeriod($start, $interval, $end);

        $result = collect();
        $prev = null;

        foreach ($periodRange as $key => $date) {

            if ($prev) {

                $series = collect();
                $query = ScanAction::query()
                    ->join('scan_action_types', 'scan_action_types.cuid', '=', 'scan_actions.scan_type_id')
                    ->where(function ($query) {
                        return $query->where('scan_action_types.name', '=', 'CleanInScan')
                            ->orWhere('scan_action_types.name', '=', 'DirtyInScan');
                    })
                    ->where('scan_actions.created_at', '>=', $prev)
                    ->where('scan_actions.created_at', '<', $date)
                    ->select('scan_actions.*')
                    ->with(['type']);

                $actions = $query->get();

                foreach ($actions as $action) {

                    $noPerGroup = Item::query()
                        ->join('item_scan_action', 'items.cuid', '=', 'item_scan_action.item_id')
                        ->join('scan_actions', 'scan_actions.cuid', '=', 'item_scan_action.scan_action_id')
                        ->join('item_statuses', 'item_scan_action.old_status_id', '=', 'item_statuses.cuid')
                        ->where('scan_actions.cuid', '=', $action->cuid)
                        ->groupBy('item_statuses.location_id', 'item_statuses.location_type')
                        ->selectRaw('count(distinct items.cuid) as count, item_statuses.location_id as cuid, item_statuses.location_type as type')
                        ->get();

                    foreach ($noPerGroup as $data) {
                        $id = $data->cuid ?? 'Unknown';
                        $noClean = $action->type->name == 'CleanInScan' ? +$data->count : 0;
                        $noSoil = $action->type->name == 'DirtyInScan' ? +$data->count : 0;
                        if ($series->has($id)) {
                            $oldCount = $series->get($id)['value'];
                            $oldCleanCount = $series->get($id)['clean_count'];
                            $oldSoilCount = $series->get($id)['soil_count'];
                            $series->put($id, [
                                'cuid' => $id,
                                'type' => $data->type ?? 'Unknown',
                                'value' => $oldCount + $data->count,
                                'clean_count' => $oldCleanCount + $noClean,
                                'soil_count' => $oldSoilCount + $noSoil
                            ]);
                        } else {
                            $series->put($id, [
                                'cuid' => $id ?? 'Unknown',
                                'type' => $data->type ?? 'Unknown',
                                'value' => +$data->count,
                                'clean_count' => +$noClean,
                                'soil_count' => +$noSoil
                            ]);
                        }
                    }

                }

                $result->push([
                    'name' => $prev->format($format),
                    'series' => $series
                        ->values()
                        ->map(function ($s) {
                            if ($s['cuid'] == 'Unknown') {
                                $location = 'Unknown';
                            } else {
                                $type = Statistics::getMorphedModel($s['type']);
                                $location = $type::find($s['cuid'])->name;
                            }
                            return [
                                'name' => $location,
                                'cuid' => $s['cuid'],
                                'type' => $s['type'],
                                'value' => $s['value'],
                                'clean_count' => $s['clean_count'],
                                'soil_count' => $s['soil_count']
                            ];
                        })
                ]);


            }
            $prev = $date;
        }

        return $result->all();

    }

    public function getIncomingAndOutgoingProductsTimeline($period, $start = null, $end = null)
    {

        if (!$period) {
            $period = 'since 1 week';
        }

        $formatting = Statistics::getPeriodFormatting($period, $start, $end);
        $format = $formatting['string_format'];
        $start = $formatting['start_date'];
        $end = $formatting['end_date'];
        $interval = $formatting['interval'];
        $periodRange = new CarbonPeriod($start, $interval, $end);

        $result = collect();
        $prev = null;

        foreach ($periodRange as $key => $date) {

            if ($prev) {

                $cleanSeries = collect();
                $totalClean = 0;

                $soilSeries = collect();
                $totalSoil = 0;

                $outSeries = collect();
                $totalOut = 0;

                $noPerProductClean = $this->getItemsByProductCountQuery($prev, $date, 'CleanInScan')->get();

                foreach ($noPerProductClean as $data) {
                    $totalClean += $data->count;
                    if ($cleanSeries->has($data->cuid)) {
                        $oldCount = $cleanSeries->get($data->cuid)['value'];
                        $cleanSeries->put($data->cuid, [
                            'name' => $data->name,
                            'value' => $oldCount + $data->count
                        ]);
                    } else {
                        $cleanSeries->put($data->cuid, [
                            'name' => $data->name,
                            'value' => +$data->count
                        ]);
                    }
                }

                $noPerProductSoil = $this->getItemsByProductCountQuery($prev, $date, 'DirtyInScan')->get();

                foreach ($noPerProductSoil as $data) {
                    $totalSoil += $data->count;
                    if ($soilSeries->has($data->cuid)) {
                        $oldCount = $soilSeries->get($data->cuid)['value'];
                        $soilSeries->put($data->cuid, [
                            'name' => $data->name,
                            'value' => $oldCount + $data->count
                        ]);
                    } else {
                        $soilSeries->put($data->cuid, [
                            'name' => $data->name,
                            'value' => +$data->count
                        ]);
                    }
                }


                $noPerProductOut = $this->getItemsByProductCountQuery($prev, $date, 'OutScan')->get();

                foreach ($noPerProductOut as $data) {
                    $totalOut += $data->count;
                    if ($outSeries->has($data->cuid)) {
                        $oldCount = $outSeries->get($data->cuid)['value'];
                        $outSeries->put($data->cuid, [
                            'name' => $data->name,
                            'value' => $oldCount + $data->count
                        ]);
                    } else {
                        $outSeries->put($data->cuid, [
                            'name' => $data->name,
                            'value' => +$data->count
                        ]);
                    }
                }


                $result->push([
                    'name' => $prev->format($format),
                    'series' => [[
                        'name' => 'Clean return',
                        'value' => $totalClean,
                        'per_product' => $cleanSeries->values()
                    ],
                        [
                            'name' => 'Soil return',
                            'value' => $totalSoil,
                            'per_product' => $soilSeries->values()
                        ],
                        [
                            'name' => 'Delivered',
                            'value' => $totalOut,
                            'per_product' => $outSeries->values()
                        ]]
                ]);

            }
            $prev = $date;
        }

        return $result->all();

    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    private function getItemsByProductCountQuery($start, $end, $scanActionType)
    {
        return Item::query()
            ->join('item_scan_action', 'items.cuid', '=', 'item_scan_action.item_id')
            ->join('scan_actions', 'item_scan_action.scan_action_id', '=', 'scan_actions.cuid')
            ->join('scan_action_types', 'scan_actions.scan_type_id', '=', 'scan_action_types.cuid')
            ->join('products', 'product_id', '=', 'products.cuid')
            ->join('product_types', 'product_type_id', '=', 'product_types.cuid')
            ->where('scan_action_types.name', '=', $scanActionType)
            ->where('scan_actions.created_at', '>=', $start)
            ->where('scan_actions.created_at', '<', $end)
            ->groupBy('product_types.cuid', 'product_types.name')
            ->selectRaw('count(distinct items.cuid) as count, product_types.cuid as cuid, product_types.name as name');
    }

    public function getDeliverAndReturnTimeline($locationId, $productId, $period, $start = null, $end = null)
    {

        if (!$period) {
            $period = 'since 1 month';
        }

        $formatting = Statistics::getPeriodFormatting($period, $start, $end);
        $format = $formatting['string_format'];
        $start = $formatting['start_date'];
        $end = $formatting['end_date'];
        $interval = $formatting['interval'];
        $periodRange = new CarbonPeriod($start, $interval, $end);

        $cleanIn = collect();
        $soilIn = collect();
        $unknownIn = collect();
        $out = collect();

        $data = NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic::query()
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->where('product_type_id', '=', $productId)
            ->where('location_id', '=', $locationId)
            ->get();
        $result = collect();

        foreach ($data as $datum) {
            $name = $datum->date->timezone('UTC')->format('Y-m-d');
            $result->push([
                'name' => $name,
                'series' => [
                    ['name' => 'Clean In', 'value' => $datum->no_items_clean_in ?? 0],
                    ['name' => 'Soil In', 'value' => ($datum->no_items_soil_in ?? 0) + ($datum->no_items_unknown_in ?? 0)],
                    //['name' => 'Unknown In', 'value' => $datum->no_items_unknown_in ?? 0],
                    ['name' => 'Out', 'value' => -($datum->no_items_out ?? 0)],
                ]
            ]);
            /*$cleanIn->push([
                'name' => $name,
                'value' => $datum->no_items_clean_in ?? 0
            ]);
            $soilIn->push([
                'name' => $name,
                'value' => $datum->no_items_soil_in ?? 0
            ]);
            $unknownIn->push([
                'name' => $name,
                'value' => $datum->no_items_unknown_in ?? 0
            ]);
            $out->push([
                'name' => $name,
                'value' => -($datum->no_items_out ?? 0)
            ]);*/
        }

        /*return [
            ['name' => 'Clean In', 'series' => $cleanIn->toArray()],
            ['name' => 'Soil In', 'series' => $soilIn->toArray()],
            ['name' => 'Unknown In', 'series' => $unknownIn->toArray()],
            ['name' => 'Out', 'series' => $out->toArray()],
        ];*/

        return $result->toArray();

    }

}
