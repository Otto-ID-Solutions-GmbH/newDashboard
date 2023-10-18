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
use Cintas\Models\Facility\Facility;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ItemStatusType;
use Cintas\Models\Items\Product;
use Cintas\Models\Items\ProductType;
use Cintas\Models\Statistics\NoItemsPerProductTypePerLocationStatistic;
use Illuminate\Database\Eloquent\Collection;

class EloquentItemStatisticsRepository implements ItemStatisticsRepository
{

    public function getItemsAtLocationStatistics(bool $includeNewItems = false, bool $includeUnknownItems = true, $filterLocationType = null)
    {
        $locationTypes = Item::query()
            ->join('item_statuses', 'last_status_id', '=', 'item_statuses.cuid')
            ->whereNotNull('item_statuses.location_id');

        if ($filterLocationType) {
            $locationTypes = $locationTypes
                ->where('location_type', '=', $filterLocationType);
        }

        $locationTypes = $locationTypes
            ->selectRaw('distinct item_statuses.location_id, item_statuses.location_type')
            ->pluck('location_type', 'location_id');

        $locations = $locationTypes->map(function ($locType, $locCuid) {
            $model = Statistics::getMorphedModel($locType);
            return $model::find($locCuid);
        });

        $locations = $locations->filter(function ($val, $key) {
            return $val !== null;
        });

        $result = collect();

        foreach ($locations as $location) {
            $items = Item::query()
                ->join('item_statuses', 'item_statuses.cuid', '=', 'items.last_status_id')
                ->where('item_statuses.location_id', '=', $location->cuid)
                ->count();

            $result->push([
                'name' => $location->label,
                'cuid' => $location->cuid,
                'value' => $items
            ]);
        }

        if ($includeNewItems) {
            $itemsNew = Item::query()
                ->join('item_statuses', 'item_statuses.cuid', '=', 'items.last_status_id')
                ->where('item_statuses.item_status_type_id', '=', ItemStatusType::findByName('NewStatus')->cuid)
                ->count();

            $result->push([
                'name' => 'New in System',
                'cuid' => null,
                'value' => $itemsNew
            ]);
        } else {
            $itemsNew = 0;
        }

        if ($includeUnknownItems) {
            $itemsUnknown = Item::query()
                ->join('item_statuses', 'item_statuses.cuid', '=', 'items.last_status_id')
                ->whereNull('item_statuses.location_id')
                ->count();

            $result->push([
                'name' => 'Unknown',
                'cuid' => null,
                'value' => $itemsUnknown - $itemsNew
            ]);
        }


        return $result;
    }

    public function getItemsPerFacilityStatistics()
    {
        $facilities = Facility::all();
        $result = collect();

        foreach ($facilities as $facility) {
            $series = $this->getItemsAtFacilityStatistics($facility->cuid);
            $result->push([
                'name' => $facility->name,
                'cuid' => $facility->cuid,
                'series' => $series->toArray()
            ]);
        }

        return $result;
    }

    public function getItemsAtFacilityStatistics($facilityCuid)
    {

        $itemsQuery = Item::query()
            ->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid')
            ->join('item_status_types', 'item_statuses.item_status_type_id', '=', 'item_status_types.cuid')
            ->where('item_statuses.location_id', '=', $facilityCuid)
            ->groupBy('item_status_types.cuid', 'item_status_types.name')
            ->selectRaw('count(distinct items.cuid) as value, item_status_types.name as name, item_status_types.cuid as cuid');

        $itemCount = $itemsQuery
            ->get()
            ->map(function ($r) {
                return [
                    'name' => $this->translateStatusTypeName($r->name),
                    'value' => $r->value,
                    'status_type_cuid' => $r->cuid
                ];
            });

        return $itemCount;

    }

    private function translateStatusTypeName($statusText): string
    {
        switch ($statusText) {
            case 'AtFacilityCleanStatus':
                return 'Clean In';
            case 'AtFacilityDryerStatus':
                return 'Dryers';
            case 'AtFacilityTableStatus':
                return 'Tables';
            default:
                return 'Unknown';
        }
    }

    public function getNoItemsPerProductPerCustomer($circulatingOnly = false, $limit = null, $customer = null, bool $includeNewItems = false, bool $includeUnknownItems = true, $filterLocationType = null)
    {

        if ($customer) {
            $locations = collect();
            $locations->push($customer);
        } else {
            if ($circulatingOnly) {
                $locations = $this->getLocationsWithCirculatingItems($limit, $filterLocationType);
            } else {
                $locations = $this->getLocationsWithItems($filterLocationType);
            }
        }

        $result = collect();

        foreach ($locations as $location) {

            $itemsQueryKnown = Item::query()
                ->join('products', 'product_id', '=', 'products.cuid');

            $itemsQueryKnown = $itemsQueryKnown
                ->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid')
                ->where('item_statuses.location_id', '=', $location->cuid)
                ->groupBy('product_id', 'products.name')
                ->selectRaw('count(distinct items.cuid) as count, product_id, products.name');

            if ($circulatingOnly) {
                $itemsQueryKnown = $itemsQueryKnown->onlyCirculating($limit);
            }

            $items = $itemsQueryKnown->get();

            $result->push([
                'name' => $location->label,
                'cuid' => $location->cuid,
                'series' => $items->map(function ($r) {
                    return [
                        'name' => $r->name,
                        'cuid' => $r->product_id,
                        'value' => $r->count
                    ];
                })
            ]);
        }

        if ($customer) {
            return $result;
        }

        if ($includeUnknownItems) {

            $itemsUnknownQuery = Item::query()
                ->join('products', 'product_id', '=', 'products.cuid');

            $itemsUnknownQuery = $itemsUnknownQuery
                ->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid')
                ->where(function ($query) {
                    return $query
                        ->whereNull('item_statuses.location_id')
                        ->where('item_statuses.item_status_type_id', '<>', ItemStatusType::findByName('NewStatus')->cuid);
                })
                ->groupBy('product_id', 'products.name')
                ->selectRaw('count(distinct items.cuid) as count, product_id, products.name as name');

            if ($circulatingOnly) {
                $itemsUnknownQuery = $itemsUnknownQuery->onlyCirculating($limit);
            };

            $itemsUnknown = $itemsUnknownQuery->get()
                ->map(function ($r) {
                    return [
                        'name' => $r->name,
                        'value' => $r->count,
                        'cuid' => $r->product_id
                    ];
                });

            if ($itemsUnknown->count() > 0) {
                $result->push([
                    'name' => 'Unknown',
                    'cuid' => null,
                    'series' => $itemsUnknown
                ]);
            }
        }

        if ($includeNewItems) {
            $itemsNew = Item::query()
                ->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid')
                ->where('item_statuses.item_status_type_id', '=', ItemStatusType::findByName('NewStatus')->cuid)
                ->join('products', 'product_id', '=', 'products.cuid')
                ->groupBy('product_id', 'products.name')
                ->selectRaw('count(distinct items.cuid) as count, product_id, products.name as name')
                ->get()
                ->map(function ($r) {
                    return [
                        'name' => $r->name,
                        'value' => $r->count,
                        'cuid' => $r->product_id
                    ];
                });

            if ($itemsNew->count() > 0) {
                $result->push([
                    'name' => 'New in System',
                    'cuid' => null,
                    'series' => $itemsNew
                ]);
            }

        }

        return $result;

    }

    public function getNumberOfLostItems($limit = null, $referenceDate = null, $filterLocationType = null, $includeUnknownItems = true)
    {
        $query = Item::query()
            ->onlyOutdated($limit, $referenceDate);

        if ($filterLocationType) {
            $query = $query->onlyAtLocationType($filterLocationType, $includeUnknownItems);
        }

        return $query->count();
    }

    public function getNumberOfItems($referenceDate = null, $filterByProductType = null, $filterLocationType = null, $includeUnknownItems = true)
    {
        $query = Item::query();

        if ($referenceDate) {
            $query = $query
                ->where('created_at', '<=', Carbon::make($referenceDate)->timezone('UTC'));
        }

        if ($filterByProductType) {
            $query = $query->whereHas('product.product_type', function ($query) use ($filterByProductType) {
                return $query->where('cuid', '=', $filterByProductType->cuid);
            });
        }

        if ($filterLocationType) {
            $query = $query->onlyAtLocationType($filterLocationType, $includeUnknownItems);
        }

        return $query->count();
    }

    public function getNoLostItemsPerProductType($limit = null, $filterLocationType = null, $includeUnknownItems = true)
    {

        if ($limit === null) {
            $limit = config('cintas.process.outdated_limit');
        }

        $query = Item::query()
            ->with(['last_status', 'product.product_type'])
            ->onlyOutdated($limit);

        if ($filterLocationType) {
            $query = $query->onlyAtLocationType($filterLocationType, $includeUnknownItems);
        }

        $data = $query->get();

        $groupedData = $data
            ->groupBy(function ($item) {
                return $item->product->product_type->cuid ?? 'Unknown';
            })->reduce(function ($result, $group) {
                $result = $result ?? collect([]);

                $cuid = $group->first()->product->product_type->cuid ?? null;
                $label = $group->first()->product->product_type->label ?? 'Unknown';

                $totalCount = Item::whereHas('product.product_type', function ($query) use ($cuid) {
                    return $query->where('cuid', '=', $cuid);
                })->count();

                return $result->push(
                    collect([
                        'item_cuid' => $cuid,
                        'name' => $label,
                        'value' => $group->count(),
                        'percent_lost' => $group->count() / $totalCount,
                        'total_in_group' => $totalCount
                    ])
                );

            });

        return $groupedData ?? [];

    }

    public function getLostAndExistingItemsPerProductType($limit = null)
    {

        if ($limit === null) {
            $limit = config('cintas.process.outdated_limit');
        }

        $query = Item::query()
            ->with(['last_status', 'product.product_type'])
            ->onlyOutdated($limit);
        $data = $query->get();

        $groupedData = $data->groupBy(function ($item) {
            return $item->product->product_type->cuid ?? 'Unknown';
        });

        $result = collect();

        foreach ($groupedData as $prodGroup) {
            $cuid = $prodGroup->first()->product->product_type->cuid ?? null;
            $label = $prodGroup->first()->product->product_type->label ?? 'Unknown';

            $lostCount = $prodGroup->count();
            $totalCount = Item::whereHas('product.product_type', function ($query) use ($cuid) {
                return $query->where('cuid', '=', $cuid);
            })->count();

            $result->push([
                'name' => $label,
                'percent_lost' => $totalCount > 0 ? ($lostCount / $totalCount) : 0.0,
                'percent_lost_label' => 'Percent Lost',
                'total_count' => $totalCount,
                'total_count_label' => 'Total Items Count',
                'series' => [
                    ['name' => 'Lost',
                        'label' => 'Items Lost Count',
                        'value' => $lostCount],
                    ['name' => 'In Circulation',
                        'label' => 'Items in Circulation Count',
                        'value' => $totalCount - $lostCount]
                ]
            ]);
        }

        return $result;

    }

    public function getNoLostItemsPerProductPerCustomer($limit = null)
    {

        $locations = $this->getLocationsWithLostItems($limit);

        $result = collect();

        foreach ($locations as $location) {
            $items = Item::query()
                ->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid')
                ->where('item_statuses.location_id', '=', $location->cuid)
                ->join('products', 'product_id', '=', 'products.cuid')
                ->onlyOutdated($limit)
                ->groupBy('product_id', 'products.name')
                ->selectRaw('count(distinct items.cuid) as count, product_id, products.name')
                ->get();

            $result->push([
                'name' => $location->label,
                'cuid' => $location->cuid,
                'series' => $items->map(function ($r) {
                    return [
                        'name' => $r->name,
                        'cuid' => $r->product_id,
                        'value' => $r->count
                    ];
                })
            ]);
        }

        $itemsUnknown = Item::query()
            ->join('item_statuses', 'items.last_status_id', '=', 'item_statuses.cuid')
            ->whereNull('item_statuses.location_id')
            ->onlyOutdated($limit)
            ->join('products', 'product_id', '=', 'products.cuid')
            ->groupBy('product_id', 'products.name')
            ->selectRaw('count(distinct items.cuid) as count, product_id, products.name as name')
            ->get()
            ->map(function ($r) {
                return [
                    'name' => $r->name,
                    'value' => $r->count,
                    'cuid' => $r->product_id
                ];
            });

        if ($itemsUnknown->count() > 0) {
            $result->push([
                'name' => 'Unknown',
                'cuid' => null,
                'series' => $itemsUnknown
            ]);
        }

        return $result;
    }

    public function getNoLostItemsPerProductForLocation($location, $limit = null)
    {

        if ($limit === null) {
            $limit = config('cintas.process.outdated_limit');
        }

        $query = Product::query()
            ->whereHas('items');
        $products = $query->get();

        $data = collect();

        foreach ($products as $product) {
            $noLostItems = $product->getNoLostItemsAttribute($limit, null, $location);
            $noLostItems30 = $product->getNoLostItemsAttribute(30, null, $location, 59);
            $noLostItems60 = $product->getNoLostItemsAttribute(60, null, $location, 89);
            $noLostItems90 = $product->getNoLostItemsAttribute(90, null, $location);

            $data->push([
                'name' => $product->label,
                'value' => $noLostItems,
                'no_lost_items_30' => $noLostItems30,
                'no_lost_items_60' => $noLostItems60,
                'no_lost_items_90' => $noLostItems90
            ]);
        }

        return $data;

    }

    public function getTopNLocationsWithLostItems($limit = null, $n = null, $filterLocationType = null, $includeUnknownItems = true)
    {

        if ($limit === null) {
            $limit = config('cintas.process.outdated_limit');
        }

        if ($n === null) {
            $n = config('cintas.view.top_n');
        }

        //TODO: Test
        $locationTypes = Item::query()
            ->onlyOutdated($limit)
            ->groupBy('item_statuses.location_id', 'item_statuses.location_type');

        if ($filterLocationType) {
            $locationTypes = $locationTypes
                ->where('location_type', '=', $filterLocationType);
        }

        $locationTypes = $locationTypes
            ->selectRaw('count(distinct items.cuid) as count, item_statuses.location_id, item_statuses.location_type')
            ->orderBy('count', 'desc')
            ->take($n)
            ->get();

        return $locationTypes->map(function ($res) {
            if ($res->location_type) {
                $model = Statistics::getMorphedModel($res->location_type);
                $loc = $model::find($res->location_id);
            } else {
                $loc = null;
            }

            return [
                'name' => $loc ? $loc->label : 'Unknown',
                'cuid' => $loc ? $loc->cuid : null,
                'value' => $res->count
            ];
        });


    }

    public function getNoLostItemsPerProductTypePerTime($limit = null, $period = "This Week", $start = null, $end = null)
    {

        if ($limit === null) {
            $limit = config('cintas.process.outdated_limit');
        }

        $formatting = Statistics::getPeriodFormatting($period, $start, $end);
        $format = $formatting['string_format'];
        $start = $formatting['start_date'];
        $end = $formatting['end_date'];
        $interval = $formatting['interval'];

        $periodRange = new CarbonPeriod($start, $interval, $end);

        $result = collect();

        $query = ProductType::query()
            ->whereHas('products.items')
            ->with(['products', 'products.items.last_status']);
        $productTypes = $query->get();

        foreach ($productTypes as $type) {
            $name = $type->label;
            $items = $type->products->flatMap(function ($product) {
                return $product->items;
            }); //TODO: Compute how many items existed within the period of time via created_at

            $series = collect();
            $totalCount = 0;
            $lostCount = 0;
            $count = 0;

            foreach ($periodRange as $date) {

                if ($date > Carbon::now()) {
                    // If date is in the future, copy the values from the last valid date
                    $noLostItemsAtDate = $series->last()['value'];
                    $itemsCount = $series->last()['item_count'];

                    $series->push([
                        'name' => $date->format($format),
                        'value' => +$noLostItemsAtDate,
                        'item_count' => $itemsCount
                    ]);

                } else {
                    // If the date is real, compute the parameters correctly
                    $query = Item::query()
                        ->with(['last_status'])
                        ->onlyOutdated($limit, $date)
                        ->whereHas('product.product_type', function ($query) use ($type) {
                            return $query->where('cuid', '=', $type->cuid);
                        });

                    $noLostItemsAtDate = $query->count();

                    $itemsCount = $this->getNumberOfItems($date, $type);
                    $totalCount += $itemsCount;
                    $lostCount += $noLostItemsAtDate;
                    $count++;

                    $series->push([
                        'name' => $date->format($format),
                        'value' => +$noLostItemsAtDate,
                        'item_count' => $itemsCount
                    ]);
                }


            }

            $result->push([
                'name' => $name,
                'avg_total_count' => ($totalCount + $lostCount) / $count,
                'avg_total_count_label' => 'Avg. No. Items In Period',
                'avg_lost_count' => $lostCount / $count,
                'avg_lost_count_label' => 'Avg. No. Items Lost in Period',
                'series' => $series,

            ]);
        }

        return [
            'data' => $result,
            'meta' => [
                'start_date' => $start->toIso8601String(),
                'end_date' => $end->toIso8601String(),
                'interval' => $interval
            ]
        ];


    }

    /**
     * @param $limit
     * @param null $filterLocationType
     * @param bool $includeUnknownItems
     * @return mixed
     */
    public function getLocationsWithLostItems($limit, $filterLocationType = null, $includeUnknownItems = true)
    {
        if ($limit === null) {
            $limit = config('cintas.process.outdated_limit');
        }

        $locationTypes = Item::query()
            ->onlyOutdated($limit)
            ->whereNotNull('item_statuses.location_id');

        if ($filterLocationType) {
            $locationTypes = $locationTypes
                ->where('location_type', '=', $filterLocationType);
        }

        $locationTypes = $locationTypes
            ->selectRaw('distinct item_statuses.location_id, item_statuses.location_type')
            ->pluck('location_type', 'location_id');

        $result = new Collection();

        foreach ($locationTypes as $locCuid => $locType) {
            $model = Statistics::getMorphedModel($locType);
            $obj = $model::find($locCuid);
            $result->push($obj);
        }

        return $result;
    }

    public function getLocationsWithCirculatingItems($limit = null, $filterLocationType = null)
    {
        if ($limit === null) {
            $limit = config('cintas.process.outdated_limit');
        }

        $locationTypes = Item::query()
            ->join('item_statuses', 'last_status_id', '=', 'item_statuses.cuid')
            ->onlyCirculating($limit)
            ->whereNotNull('item_statuses.location_id');

        if ($filterLocationType) {
            $locationTypes = $locationTypes
                ->where('location_type', '=', $filterLocationType);
        }

        $locationTypes = $locationTypes
            ->selectRaw('distinct item_statuses.location_id, item_statuses.location_type')
            ->pluck('location_type', 'location_id');

        $result = new Collection();
        foreach ($locationTypes as $locCuid => $locType) {
            $model = Statistics::getMorphedModel($locType);
            $obj = $model::find($locCuid);
            if ($obj) {
                $result->push($obj);
            }
        }

        return $result;
    }

    public function getLocationsWithItems($filterLocationType = null)
    {

        $locationTypes = Item::query()
            ->rightJoin('item_statuses', 'last_status_id', '=', 'item_statuses.cuid')
            ->whereNotNull('item_statuses.location_id');

        if ($filterLocationType) {
            $locationTypes = $locationTypes
                ->where('location_type', '=', $filterLocationType);
        }

        $locationTypes = $locationTypes
            ->selectRaw('distinct item_statuses.location_id, item_statuses.location_type')
            ->pluck('location_type', 'location_id');

        $result = new Collection();
        foreach ($locationTypes as $locCuid => $locType) {
            $model = Statistics::getMorphedModel($locType);
            $obj = $model::find($locCuid);
            if ($obj) {
                $result->push($obj);
            }
        }

        return $result;
    }

    public function getNoItemsPerProductTypePerTimeAtLocation($location, $period = "Since 3 Month", $start = null, $end = null)
    {
        $formatting = Statistics::getPeriodFormatting($period, $start, $end);
        $format = 'Y/m/d';
        $start = $formatting['start_date']->startOfWeek();
        $end = $formatting['end_date']->endOfWeek();
        $interval = '1 Day';

        $periodRange = new CarbonPeriod($start, $interval, $end);

        $result = collect();

        $query = ProductType::query()
            ->whereHas('products.items');
        $productTypes = $query->get();

        foreach ($productTypes as $type) {
            $name = $type->label;
            $series = collect();
            $totalCount = 0;
            $count = 0;

            foreach ($periodRange as $date) {

                if ($date > Carbon::now()) {
                    // If date is in the future, copy the values from the last valid date
                    $noItemsAtDate = $series->last()['value'];

                    $series->push([
                        'name' => $date->format($format),
                        'value' => +$noItemsAtDate
                    ]);

                } else {
                    // If the date is real, compute the parameters correctly
                    $data = NoItemsPerProductTypePerLocationStatistic::query()
                        ->where('product_type_id', '=', $type->cuid)
                        ->where('location_id', '=', $location->cuid)
                        ->whereDate('date', $date)
                        ->first();

                    $noItemsAtDate = $data->no_items_at_location ?? null;
                    if ($noItemsAtDate !== null) {
                        $totalCount += $noItemsAtDate;
                        $count++;
                    }

                    $series->push([
                        'name' => $date->format($format),
                        'value' => $noItemsAtDate
                    ]);
                }


            }

            $result->push([
                'name' => $name,
                'avg_total_count' => $count > 0 ? $totalCount / $count : 0,
                'avg_total_count_label' => 'Avg. #items in total period',
                'series' => $series,

            ]);
        }

        return [
            'data' => $result,
            'meta' => [
                'start_date' => $start->toIso8601String(),
                'end_date' => $end->toIso8601String(),
                'interval' => $interval
            ]
        ];
    }

}
