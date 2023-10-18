<?php

namespace Cintas\Exports;

use Cintas\Repositories\ReaderRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ScanActionsListDetailsExport implements FromCollection, WithStrictNullComparison, WithMapping, WithHeadings
{
    private $readerRepository;
    private $scanActions;

    public function __construct(ReaderRepository $readerRepository, $scanActions = null)
    {
        $this->readerRepository = $readerRepository;
        $this->scanActions = $scanActions;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if (!$this->scanActions) {
            $collection = $this->readerRepository->getScanActionsInTime();
        } else {
            $collection = $this->scanActions;
        }

        $collection->load(['out_scan_action.location', 'items.rfid_tags', 'skipped_items', 'unknown_rfid_tags']);

        return $collection;
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        $items = $row->items->pluck('rfid_tags.0.epc')->sort();
        $skippedItems = $row->skipped_items->pluck('rfid_tags.0.epc')->sort();
        $unknownEPCs = $row->unknown_rfid_tags->pluck('epc')->sort();

        $noProducts = $row->items
            ->groupBy('product.name')
            ->map(function ($group, $productName) {
                return $productName . ': ' . $group->count();
            });

        $noItemsFindType = $row->items
            ->groupBy('pivot.find_type')
            ->map(function ($group, $findType) {
                return $findType . ': ' . $group->count();
            })->sortKeys();

        return [
            $row->cuid,
            $row->created_at->toIso8601String(),
            $row->type->label,
            $row->reader->label,
            $row->out_scan_action->location->label ?? null,
            $row->items_count,
            $row->skipped_items_count,
            $row->unknown_tags_count,

            implode(', ', $noProducts->all()),
            implode(', ', $noItemsFindType->all()),

            implode(', ', $items->all()),
            implode(', ', $skippedItems->all()),
            implode(', ', $unknownEPCs->all()),
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Timestamp',
            'Action Type',
            'Reader',
            'Destination',
            'No. Items Identified',
            'No. Items Skipped',
            'No. Unknown EPCs',

            'Product Count',
            'Find Type Count',

            'Identified EPCs',
            'Skipped EPCs',
            'Unknown EPCs',
        ];
    }
}
