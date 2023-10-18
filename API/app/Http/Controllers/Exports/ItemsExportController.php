<?php

namespace Cintas\Http\Controllers\Exports;

use Cintas\Exports\ItemsExport;
use Cintas\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ItemsExportController extends Controller
{
    //

    public function __invoke(ItemsExport $export)
    {
        return Excel::download($export, 'cintas-items.xlsx');
    }
}
