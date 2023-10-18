<?php

namespace Cintas\Exports;

use Cintas\Facades\Statistics;
use Cintas\Repositories\ReaderRepository;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class ScanActionsListExport implements FromCollection, WithStrictNullComparison, WithMapping, WithHeadings
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
        $query =
            $this->readerRepository->getScanActionsInTimeQuery()
                ->with(['reader', 'out_scan_action.location'])
                ->withCount(['items', 'skipped_items', 'unknown_rfid_tags']);

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
            $row->cuid,
            $row->created_at->timezone(Statistics::getUserTimezone())->toIso8601String(),
            $row->type->label,
            $row->reader->label,
            $row->out_scan_action->location->label ?? null,
            $row->items_count,
            $row->skipped_items_count,
            $row->unknown_rfid_tags_count
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $tz = Statistics::getUserTimezone();
        return [
            'ID',
            "Timestamp ($tz)",
            'Action Type',
            'Reader',
            'Destination',
            '# Items Identified',
            '# Items Skipped',
            '# Unknown EPCs',
        ];
    }
}
