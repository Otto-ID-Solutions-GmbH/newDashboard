<?php

namespace Cintas\Exports;

use Cintas\Models\Items\Product;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class AgeSummaryExport implements FromQuery, WithMapping, WithHeadings, WithStrictNullComparison
{

    /**
     * @return Builder
     */
    public function query()
    {
        return Product::query()->has('items');
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Product Description',
            'Avg Age in Days',
            'Avg Cycle Count',
            'No Items older 2yrs'
        ];
    }

    /**
     * @param Product $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->name,
            $row->avg_age_in_days,
            $row->avg_cycle_count,
            $row->no_old_items
        ];
    }
}
