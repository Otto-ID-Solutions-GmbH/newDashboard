<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class TopNLocationswithLostItemsController extends Controller
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

        return Resource::make($this->statisticsRepository->getTopNLocationsWithLostItems($limit, $n, $filterLocationType));
    }
}
