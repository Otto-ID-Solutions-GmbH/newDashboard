<?php

namespace Cintas\Exports;

use Cintas\Models\Items\Item;
use Cintas\Repositories\ReaderRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ItemsExport implements FromCollection, WithStrictNullComparison, WithMapping, WithHeadings
{
    private $readerRepository;

    public function __construct(ReaderRepository $readerRepository)
    {
        $this->readerRepository = $readerRepository;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Item::query()
            ->with(['rfid_tags', 'product.product_type', 'bundle', 'last_status.status_type', 'last_status.location',]);

        return $query->get();
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->rfid_tags->first()->epc,
            $row->product->product_number,
            $row->product->name,
            $row->product->product_type->name,
            $row->product->product_type->expected_lifetime,
            $row->product->product_type->bundle_target,

            null,
            $row->last_status->location->label ?? null,

            $row->cycle_count,
            $row->bundle->cuid ?? null,
            $this->getStatus($row->last_status->status_type->name ?? null),
            $row->last_status->updated_at->toIso8601String() ?? null
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'EPC',
            'Product Code',
            'Product Description',
            'Product Group',
            'Expected lifetime',
            'Bundle target',

            'Customer Code',
            'Customer Name',

            'Cycle count',
            'Bundle ID',
            'Last Scan Point',
            'Last Scan Date',
        ];
    }

    public function getStatus($text): string
    {
        switch ($text) {
            case 'AtFacilityTableStatus':
                return 'Bundled';
            case 'AtFacilityCleanStatus':
                return 'Clean return';
            case 'AtFacilityDryerStatus':
                return 'Soil return';
            case 'AtCustomerStatus':
                return 'Shipped to customer';
            case 'SortedOutStatus':
                return 'Sorted out';
            case 'AtFacilityStatus':
                return 'At Columbus (unspecified)';
            default:
                return $text;
        }
    }
}
