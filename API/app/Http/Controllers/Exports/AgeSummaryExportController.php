<?php

namespace Cintas\Http\Controllers\Exports;

use Cintas\Exports\AgeSummaryExport;
use Cintas\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class AgeSummaryExportController extends Controller
{
    //

    public function __invoke(AgeSummaryExport $export)
    {
        return Excel::download($export, 'cintas-age-summary.xlsx');
    }
}
