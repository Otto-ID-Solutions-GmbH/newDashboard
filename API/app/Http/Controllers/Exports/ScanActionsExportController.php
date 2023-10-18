<?php

namespace Cintas\Http\Controllers\Exports;

use Cintas\Exports\ScanActionsListExport;
use Cintas\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ScanActionsExportController extends Controller
{
    //

    public function __invoke(ScanActionsListExport $export)
    {
        return Excel::download($export, 'cintas-rfid-reads-list.xlsx');
    }
}
