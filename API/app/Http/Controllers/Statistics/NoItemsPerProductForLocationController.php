<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Facades\Statistics;
use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class NoItemsPerProductForLocationController extends Controller
{
    private $statisticsRepository;

    public function __construct(ItemStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

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
            $period = 'This year';
        }

        return Resource::make($this->statisticsRepository->getNoItemsPerProductTypePerTimeAtLocation($location, $period));
    }
}
