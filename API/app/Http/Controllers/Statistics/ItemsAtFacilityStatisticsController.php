<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class ItemsAtFacilityStatisticsController extends Controller
{
    private $statisticsRepository;

    /**
     * ItemsAtLocationController constructor.
     * @param $statisticsRepository
     */
    public function __construct(ItemStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request, $facilityCuid = null)
    {
        if ($facilityCuid) {
            return Resource::make($this->statisticsRepository->getItemsAtFacilityStatistics($facilityCuid));
        } else {
            return Resource::make($this->statisticsRepository->getItemsPerFacilityStatistics());
        }
    }
}
