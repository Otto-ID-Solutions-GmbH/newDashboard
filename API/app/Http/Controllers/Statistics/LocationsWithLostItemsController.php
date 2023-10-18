<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Http\Resources\Facility\LocationRessource;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;

class LocationsWithLostItemsController extends Controller
{
    private $statisticsRepository;

    public function __construct(ItemStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request, $n = null)
    {
        $limit = $request->query('limit');

        if ($request->has('filterLocationType')) {
            $filterLocationType = $request->query('filterLocationType');
        } else {
            $filterLocationType = null;
        }

        return LocationRessource::collection($this->statisticsRepository->getLocationsWithLostItems($limit, $filterLocationType));
    }
}
