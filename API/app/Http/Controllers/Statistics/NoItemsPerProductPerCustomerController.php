<?php

namespace Cintas\Http\Controllers\Statistics;

use Cintas\Http\Controllers\Controller;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Repositories\ItemStatisticsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\Resource;

class NoItemsPerProductPerCustomerController extends Controller
{
    private $statisticsRepository;

    public function __construct(ItemStatisticsRepository $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    public function __invoke(Request $request)
    {
        $limit = $request->query('limit');
        $circulatingOnly = $limit ? true : false;

        if ($request->has('customerId')) {
            $customer = LaundryCustomer::find($request->query('customerId'));
        } else {
            $customer = null;
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

        return Resource::make($this->statisticsRepository->getNoItemsPerProductPerCustomer($circulatingOnly, $limit, $customer, false, $includeUnknown, $filterLocationType));
    }
}
