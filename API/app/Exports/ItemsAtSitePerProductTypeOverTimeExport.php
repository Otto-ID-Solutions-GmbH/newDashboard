<?php

namespace Cintas\Exports;

use Cintas\Facades\Statistics;
use Cintas\Models\Items\Product;
use Cintas\Models\Items\ProductType;
use Cintas\Models\Statistics\NoItemsPerProductTypePerLocationStatistic;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ItemsAtSitePerProductTypeOverTimeExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison
{

    private $locationId;
    private $start;
    private $end;

    private $types;

    /**
     * ItemsAtSitePerProductTypeOverTimeExport constructor.
     * @param $locationId
     */
    public function __construct($locationId = null, $period = null)
    {
        $this->locationId = $locationId;

        $formatting = Statistics::getPeriodFormatting($period);
        $this->start = $formatting['start_date'];
        $this->end = $formatting['end_date'];

        $query = ProductType::query()
            ->whereHas('products.items');
        $this->types = $query->get()->keyBy('cuid');
    }


    public function collection()
    {
        $query = NoItemsPerProductTypePerLocationStatistic::query();

        if ($this->locationId) {
            $query = $query->where('location_id', '=', $this->locationId);
        }

        if ($this->start) {
            $query = $query->whereDate('date', '>=', $this->start);
        }
        if ($this->end) {
            $query = $query->whereDate('date', '<=', $this->end);
        }

        $data = $query->orderBy('date')
            ->get();

        $dates = $data->unique('date')->pluck('date');
        $collection = collect();

        foreach ($dates as $date) {
            $item = new \stdClass();
            $dateStr = $date->timezone('UTC');
            $item->date = $dateStr->toDateString();
            foreach ($this->types as $type) {
                $prodId = $type->cuid;
                $count = $data->first(function ($d) use ($type, $date) {
                    return $d->date == $date && $d->product_type_id == $type->cuid;
                })->no_items_at_location;

                $item->$prodId = $count;
            }
            $collection->push($item);
        }

        return $collection;

    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headers = [
            'Date'
        ];

        foreach ($this->types as $type) {
            array_push($headers, '#' . $type->name);
        }

        return $headers;
    }

    /**
     * @param Product $row
     *
     * @return array
     */
    public function map($row): array
    {

        $data = [
            $row->date
        ];

        foreach ($this->types as $type) {
            $id = $type->cuid;
            $val = $row->$id;
            array_push($data, $val);
        }

        return $data;
    }
}
