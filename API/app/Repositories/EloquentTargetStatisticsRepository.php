<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 31.10.2018
 * Time: 18:21
 */

namespace Cintas\Repositories;


use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Cintas\Facades\Statistics;
use Cintas\Models\Actions\ScanAction;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ProductType;

class EloquentTargetStatisticsRepository implements TargetStatisticsRepository
{

    public function getTargetContainerReachPerScan($period = null)
    {

        $formatting = Statistics::getPeriodFormatting($period);
        $start = $formatting['start_date'];
        $end = $formatting['end_date'];
        $interval = '1 Scan';

        $scanActionsQuery = ScanAction::query()
            ->onlyOfType('OutScan')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $end)
            ->with(['items.product.product_type.target_containers.laundry_customer', 'out_scan_action.location']);

        $scanActions = $scanActionsQuery->get();
        $dataReached = collect();
        $dataMissed = collect();

        foreach ($scanActions as $scanAction) { // Process each scan action
            $r = $this->getCountPerProductType($scanAction);
            $totalReached = $r->reduce(function ($result, $prodCounts) {
                return $result + $prodCounts->get('count');
            }, 0);
            $totalTarget = $r->reduce(function ($result, $prodCounts) {
                return $result + $prodCounts->get('container_target');
            }, 0);
            $totalMissed = $totalTarget - $totalReached;

            $dataReached->push(collect([
                'scan_cuid' => $scanAction->cuid,
                'name' => $scanAction->created_at->toIso8601String(),
                'value' => $totalReached
            ]));
            $dataMissed->push(collect([
                'scan_cuid' => $scanAction->cuid,
                'name' => $scanAction->created_at->toIso8601String(),
                'value' => $totalMissed
            ]));
        }

        return [
            'chart_data' => [['name' => 'reached', 'series' => $dataReached->toArray()],
                ['name' => 'missed', 'series' => $dataMissed->toArray()]],
            'meta' => [
                'start_date' => $start->toIso8601String(),
                'end_date' => $end->toIso8601String(),
                'interval' => $interval
            ]
        ];

    }

    public function getDailyTargetContainerReachPerProductType($date = null)
    {

        if (!$date) {
            $date = Carbon::now();
        }

        $scanActionsQuery = ScanAction::query()
            ->onlyOfType('OutScan')
            ->whereDay('created_at', $date)
            ->with(['items.product.product_type.target_containers.laundry_customer', 'out_scan_action.location']);

        $scanActions = $scanActionsQuery->get();
        $result = collect();

        foreach ($scanActions as $scanAction) { // Process each scan action
            $r = $this->getCountPerProductType($scanAction);
            foreach ($r as $prodCuid => $productResult) { // Aggregate scan action into overall result
                if ($result->has($prodCuid)) { // Product already exists in the result => Aggregate
                    $data = $result->get($prodCuid);

                    // Aggregate the count and container target of the current scan into existing scan
                    $newCount = $data->get('count') + $productResult->get('count');
                    $newContainerTarget = $data->get('container_target') + $productResult->get('container_target');

                    $data->put('count', $newCount);
                    $data->put('container_target', $newContainerTarget);

                    $result->put($prodCuid, $data);
                } else { // Product is new in the result
                    $result->put($prodCuid, $productResult);
                }
            }
        }

        $result = $result->map(function ($productRes, $prodCuid) {

            $product = $productRes->get('product');
            $targetReach = $productRes->get('container_target') > 0 ? $productRes->get('count') / $productRes->get('container_target') : null;

            return collect([
                'cuid' => $product->cuid,
                'name' => $product->label,
                'value' => $targetReach
            ]);
        });

        $total = $result->reduce(function ($result, $perProduct) {
            return $result + $perProduct->get('value');
        }, 0.0);

        //TODO: Associate target container with scan action to allow changes in the target container?...

        return [
            'chart_data' => $result->values(),
            'total_avg_reach' => $result->count() > 0 ? $total / $result->count() : null
        ];
    }

    private function getCountPerProductType(ScanAction $scanAction)
    {

        $laundryCustomer = $scanAction->out_scan_action->location;

        $res = $scanAction->items
            ->groupBy(function ($item) {
                return $item->product->product_type->cuid;
            })->reduce(function ($result, $group) use ($laundryCustomer) {
                $result = $result ?? collect([]);
                $p = $group->first()->product->product_type;
                return $result->put(
                    $p->cuid,
                    collect([
                        'product' => $p,
                        'count' => $group->count(),
                        'container_target' => $this->getTargetContainerContent($p, $laundryCustomer)  //TODO: How to handle missing target data?
                    ])
                );

            });

        return $res ?? collect();
    }

    private function getTargetContainerContent(ProductType $productType, $laundryCustomer = null): int
    {
        if (!$laundryCustomer) {
            return 0;
        }

        $tC = $productType->target_containers->first(function ($t) use ($laundryCustomer) {
            return $t->laundry_customer->cuid === $laundryCustomer->cuid;
        });

        $count = $tC->pivot->target_container_content;

        return $count;
    }

    private function computeAvgCotainerTargetReachInPeriod($start, $end, $scanActions = null)
    {
        if (!$scanActions) {
            $scanActionsQuery = ScanAction::query()
                ->onlyOfType('OutScan')
                ->where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->with(['items.product.product_type.target_containers.laundry_customer', 'out_scan_action.location']);
            $scanActions = $scanActionsQuery->get();
        } else {
            $scanActions = $scanActions->filter(function ($action) use ($start, $end) {
                return $action->created_at >= $start && $action->created_at <= $end;
            });
        }

        if (!$scanActions || $scanActions->count() == 0) {
            return null;
        }

        $total = $scanActions->reduce(function ($result, $action) {
            return $result + $action->out_scan_action->avg_container_reach;
        }, 0.0);

        return $scanActions->count() > 0 ? $total / $scanActions->count() : null;
    }

    public function getAggregatedContainerBundleRatio($period = null, $start = null, $end = null)
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

                $ratioSeries = collect();
                $totalBundled = 0;
                $totalUnBundled = 0;

                $noItemsBundled = $this->getItemsByProductCountQuery($prev, $date, 'OutScan', 'item_scan_action')->get();

                foreach ($noItemsBundled as $data) {
                    $totalBundled += $data->count;
                    if ($ratioSeries->has($data->cuid)) {
                        $oldCount = $ratioSeries->get($data->cuid)['bundled'] ?? 0;
                        $ratioSeries->put($data->cuid, [
                            'name' => $data->name,
                            'bundled' => $oldCount + $data->count
                        ]);
                    } else {
                        $ratioSeries->put($data->cuid, [
                            'name' => $data->name,
                            'bundled' => +$data->count,
                            'unbundled' => 0,
                            'value' => 100.0
                        ]);
                    }
                }

                $noItemsUnBundled = $this->getItemsByProductCountQuery($prev, $date, 'OutScan', 'skipped_item_scan_action')->get();

                foreach ($noItemsUnBundled as $data) {
                    $totalUnBundled += $data->count;
                    $oldBundled = $ratioSeries->get($data->cuid)['bundled'] ?? 0;
                    if ($ratioSeries->has($data->cuid)) {
                        $oldCount = $ratioSeries->get($data->cuid)['unbundled'] ?? 0;
                        $newCount = $oldCount + $data->count;
                        $ratioSeries->put($data->cuid, [
                            'name' => $data->name,
                            'bundled' => $oldBundled,
                            'unbundled' => $newCount,
                            'value' => ($oldBundled / ($oldBundled + $newCount) * 100) ?? 0.0
                        ]);
                    } else {
                        $ratioSeries->put($data->cuid, [
                            'name' => $data->name,
                            'bundled' => $oldBundled,
                            'unbundled' => +$data->count,
                            'value' => ($oldBundled / ($oldBundled + $data->count) * 100) ?? 0.0
                        ]);
                    }
                }

                $totalRatio = ($totalBundled + $totalUnBundled) > 0 ? ($totalBundled / ($totalBundled + $totalUnBundled) * 100) : 0.0;

                $result->push([
                    'name' => $prev->format($format),
                    'series' => $ratioSeries->values(),
                    'total_bundled' => $totalBundled,
                    'total_unbundled' => $totalUnBundled,
                    'total_ratio_percent' => $totalRatio
                ]);

            }
            $prev = $date;
        }

        return $result->all();

    }

    public function getContainerBundleRatio($period = null, $start = null, $end = null)
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

        $productTypes = ProductType::all();

        foreach ($productTypes as $productType) {
            $series = collect();

            $actions = ScanAction::query()
                ->where(function ($query) use ($start, $end) {
                    $query
                        ->whereHas('type', function ($query) {
                            return $query->where('scan_action_types.name', '=', 'OutScan');
                        })
                        ->where('scan_actions.created_at', '>=', $start)
                        ->where('scan_actions.created_at', '<', $end);
                })
                ->withCount([
                    'items' => function ($query) use ($productType) {
                        $query->whereHas('product.product_type', function ($query) use ($productType) {
                            $query->where('product_types.cuid', '=', $productType->cuid);
                        });
                    },
                    'skipped_items' => function ($query) use ($productType) {
                        $query->whereHas('product.product_type', function ($query) use ($productType) {
                            $query->where('product_types.cuid', '=', $productType->cuid);
                        });
                    }
                ])
                ->get();

            foreach ($actions as $action) {

                $noBundledItems = $action->items_count;

                $noUnbundledItems = $action->skipped_items_count;

                $ratio = ($noBundledItems + $noUnbundledItems) > 0 ? $noBundledItems / ($noBundledItems + $noUnbundledItems) * 100 : 0.0;

                if ($noBundledItems > 0) {
                    $series->push([
                        'name' => $action->created_at->toIso8601String(),
                        'value' => $ratio,
                        'bundled_items' => $noBundledItems,
                        'unbundled_items' => $noUnbundledItems
                    ]);
                }

            }

            $result->push([
                'name' => $productType->name,
                'cuid' => $productType->cuid,
                'series' => $series->toArray()
            ]);

        }

        return $result->all();

    }

    private function getItemsByProductCountQuery($start, $end, $scanActionType, $itemScanActionRelation = 'item_scan_action')
    {
        return Item::query()
            ->join($itemScanActionRelation, 'items.cuid', '=', $itemScanActionRelation . '.item_id')
            ->join('scan_actions', $itemScanActionRelation . '.scan_action_id', '=', 'scan_actions.cuid')
            ->join('scan_action_types', 'scan_actions.scan_type_id', '=', 'scan_action_types.cuid')
            ->join('products', 'product_id', '=', 'products.cuid')
            ->join('product_types', 'product_type_id', '=', 'product_types.cuid')
            ->where('scan_action_types.name', '=', $scanActionType)
            ->where('scan_actions.created_at', '>=', $start)
            ->where('scan_actions.created_at', '<', $end)
            ->groupBy('product_types.cuid', 'product_types.name')
            ->selectRaw('count(distinct items.cuid) as count, product_types.cuid as cuid, product_types.name as name');
    }

}