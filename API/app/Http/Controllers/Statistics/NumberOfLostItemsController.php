<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class NumberOfLostItemsController extends Controller
{

    private $statisticsRepository;

    public function __construct(ItemStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request, $limit = null)
    {

        if (!$limit) {
            $limit = config('cintas.process.outdated_limit');
        }

        if ($request->has('period')) {
            $period = $request->query('period');

            $res = $this->statisticsRepository->getNoLostItemsPerProductTypePerTime($limit, $period);
            return Resource::make($res['data'])
                ->additional([
                    'meta' => $res['meta']
                ]);
        }

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

        $items = $this->statisticsRepository->getNumberOfItems(null, null, $filterLocationType, $includeUnknown);
        $lostItems = $this->statisticsRepository->getNumberOfLostItems($limit, null, $filterLocationType, $includeUnknown);

        return Resource::make([
            'no_items' => $items,
            'no_lost_items' => $lostItems,
            'chart_data' => $this->statisticsRepository->getNoLostItemsPerProductType($limit, $filterLocationType, $includeUnknown)
        ]);
    }

}
