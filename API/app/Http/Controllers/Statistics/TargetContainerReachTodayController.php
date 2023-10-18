<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\TargetStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class TargetContainerReachTodayController extends Controller
{
    private $statisticsRepository;

    public function __construct(TargetStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request)
    {

        if ($request->has('date')) {
            $date = $request->query('date');
        } else {
            $date = null;
        }

        return Resource::make($this->statisticsRepository->getDailyTargetContainerReachPerProductType($date));
    }
}
