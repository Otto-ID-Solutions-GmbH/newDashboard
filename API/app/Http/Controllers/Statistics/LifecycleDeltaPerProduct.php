<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ProductStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class LifecycleDeltaPerProduct extends Controller
{
    private $statisticsRepository;

    public function __construct(ProductStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request)
    {
        return Resource::make($this->statisticsRepository->getLifecycleDeltaPerProduct());
    }
}
