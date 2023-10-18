<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class NumberOfLostAndExistingItemsController extends Controller
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

        return Resource::make($this->statisticsRepository->getLostAndExistingItemsPerProductType($limit));
    }

}
