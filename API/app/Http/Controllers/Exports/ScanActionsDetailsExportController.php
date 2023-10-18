<?php

namespace Cintas\Http\Controllers\Exports;

use Cintas\Exports\ScanActionsListDetailsExport;
use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ReaderRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ScanActionsDetailsExportController extends Controller
{

    private $readerRepository;

    /**
     * GetScanActionsController constructor.
     * @param $readerRepository
     */
    public function __construct(ReaderRepository $readerRepository)
    {
        $this->readerRepository = $readerRepository;
    }

    public function __invoke(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        $type = $request->get('type');
        $sortBy = $request->get('sort_by');
        $sortDir = $request->get('sort_dir');

        if ($request->has('page')) {
            $result = $this->readerRepository->getScanActionsInTimePaginated($start, $end, $type, $sortBy, $sortDir);
        } else {
            $result = $this->readerRepository->getScanActionsInTime($start, $end, $type, $sortBy, $sortDir);
        }

        $readerRepo = app(ReaderRepository::class);
        return Excel::download(new ScanActionsListDetailsExport($readerRepo, $result), 'cintas-rfid-reads-detail-list.xlsx');
    }
}
