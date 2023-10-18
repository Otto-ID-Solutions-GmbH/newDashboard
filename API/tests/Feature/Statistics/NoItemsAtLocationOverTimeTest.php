<?php

namespace Tests\Feature\Statistics;

use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Repositories\ItemStatisticsRepository;
use Tests\TestCase;

class NoItemsAtLocationOverTimeTest extends TestCase
{

    private $statisticsRepository;


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStatistic()
    {
        $this->statisticsRepository = app(ItemStatisticsRepository::class);
        $location = LaundryCustomer::find('cjpl2rcjx000e90qyy8yf51m2');
        $data = $this->statisticsRepository->getNoItemsPerProductTypePerTimeAtLocation($location, 'Last Month');
    }
}
