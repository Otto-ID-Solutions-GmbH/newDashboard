<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class ItemsAtLocationController extends Controller
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

    public function __invoke(Request $request)
    {
        //

        if ($request->has('includeUnknown')) {
            $includeUnknown = $request->query('includeUnknown');
        } else {
            $includeUnknown = true;
        }

        if ($request->has('filterLocationType')) {
            $filterLocationType = $request->query('filterLocationType');
        } else {
            $filterLocationType = null;
        }

        return Resource::make($this->statisticsRepository->getItemsAtLocationStatistics(false, $includeUnknown, $filterLocationType));
    }

}
