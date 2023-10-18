<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class NoLostItemsPerProductPerCustomerController extends Controller
{
    private $statisticsRepository;

    public function __construct(ItemStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request)
    {
        $limit = $request->query('limit');

        return Resource::make($this->statisticsRepository->getNoLostItemsPerProductPerCustomer($limit));
    }
}
