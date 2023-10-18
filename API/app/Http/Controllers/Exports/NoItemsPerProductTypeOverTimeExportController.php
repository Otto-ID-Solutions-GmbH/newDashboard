<?php

namespace Cintas\Http\Controllers\Exports;

use Cintas\Exports\ItemsAtSitePerProductTypeOverTimeExport;
use Cintas\Facades\Statistics;
use Cintas\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NoItemsPerProductTypeOverTimeExportController extends Controller
{
    //

    public function __invoke(Request $request, $locationType, $locationCuid)
    {

        if ($locationType && $locationCuid && $locationType !== 'Unknown' && $locationCuid !== 'Unknown') {
            $model = Statistics::getMorphedModel($locationType);
            $location = $model::find($locationCuid);
        } else {
            $location = 'Unknown';
        }

        if ($request->has('period')) {
            $period = $request->query('period');
        } else {
            $period = null;
        }

        $export = new ItemsAtSitePerProductTypeOverTimeExport($location->cuid, $period);
        return Excel::download($export, 'cintas-no-items-per-product-type-over-time.xlsx');
    }
}
