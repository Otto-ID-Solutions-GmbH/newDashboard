<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Facades\Statistics;
use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class NoLostItemsPerProductForLocationController extends Controller
{
    private $statisticsRepository;

    public function __construct(ItemStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request, $locationType, $locationCuid)
    {
        $limit = $request->query('limit');

        if ($locationType && $locationCuid && $locationType !== 'Unknown' && $locationCuid !== 'Unknown') {
            $model = Statistics::getMorphedModel($locationType);
            $location = $model::find($locationCuid);
        } else {
            $location = 'Unknown';
        }


        return Resource::make($this->statisticsRepository->getNoLostItemsPerProductForLocation($location, $limit));
    }
}
