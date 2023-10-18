<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\TargetStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class BundleRatioInOutscansController extends Controller
{
    private $statisticsRepository;

    public function __construct(TargetStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request)
    {
        $period = $request->query('period');
        return Resource::make($this->statisticsRepository->getContainerBundleRatio($period));
    }
}
