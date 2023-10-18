<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\TargetStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class TargetContainerReachInPeriodController extends Controller
{
    private $statisticsRepository;

    public function __construct(TargetStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request)
    {

        if ($request->has('period')) {
            $period = $request->query('period');
        } else {
            $period = null;
        }
        $res = $this->statisticsRepository->getTargetContainerReachPerScan($period);
        return Resource::make($res['chart_data'])
            ->additional([
                'meta' => $res['meta']
            ]);
    }
}
