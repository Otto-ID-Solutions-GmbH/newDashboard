<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Facades\Statistics;
use Cintas\Repositories\ProductStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Routing\Controller;

class NoDeliveredAndReturnedItemsPerProductTypePerLocationStatisticController extends Controller
{

    private $statisticsRepository;

    /**
     * ItemsAtLocationController constructor.
     * @param $statisticsRepository
     */
    public function __construct(ProductStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request, $locationType, $locationCuid, $productTypeCuid)
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

        return Resource::make($this->statisticsRepository->getDeliverAndReturnTimeline($location->cuid, $productTypeCuid, $period));
    }

}
