<?php

namespace Cintas\Exports;

use Cintas\Facades\Statistics;
use Cintas\Models\Items\Product;
use Cintas\Models\Items\ProductType;
use Cintas\Models\Statistics\NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class IncomingOutgoingItemsOverTimeExport implements FromCollection, WithMapping, WithHeadings, WithStrictNullComparison
{

    private $locationId;
    private $productTypeId;

    private $start;
    private $end;

    private $types;

    /**
     * ItemsAtSitePerProductTypeOverTimeExport constructor.
     * @param $locationId
     */
    public function __construct($locationId = null, $productTypeId = null, $period = null)
    {
        $this->locationId = $locationId;
        $this->productTypeId = $productTypeId;

        $formatting = Statistics::getPeriodFormatting($period);
        $this->start = $formatting['start_date'];
        $this->end = $formatting['end_date'];

        $query = ProductType::query()
            ->whereHas('products.items');
        $this->types = $query->get()->keyBy('cuid');
    }


    public function collection()
    {
        $query = NoDeliveredAndReturnedItemsPerProductTypePerLocationStatistic::query();

        if ($this->locationId) {
            $query = $query->where('location_id', '=', $this->locationId);
        }

        if ($this->productTypeId) {
            $query = $query->where('product_type_id', '=', $this->productTypeId);
        }

        if ($this->start) {
            $query = $query->whereDate('date', '>=', $this->start);
        }
        if ($this->end) {
            $query = $query->whereDate('date', '<=', $this->end);
        }

        $data = $query->orderBy('date')
            ->with(['location', 'product_type'])
            ->get();

        return $data;

    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $headers = [
            'Date',
            'Location',
            'Product Type',
            'Clean In',
            'Soil In',
            'Out'
        ];

        return $headers;
    }

    /**
     * @param Product $row
     *
     * @return array
     */
    public function map($row): array
    {
        $dateStr = $row->date->timezone('UTC');
        $date = $dateStr->toDateString();
        $data = [
            $date,
            $row->location->name,
            $row->product_type->name,
            $row->no_items_clean_in,
            ($row->no_items_soil_in ?? 0) + ($row->no_items_unknown_in ?? 0),
            $row->no_items_out
        ];

        return $data;
    }
}
