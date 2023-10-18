<?php

namespace Cintas\Console\Commands\Statistics;

use Cintas\Jobs\Statistics\ComputeDeliveryReturnPerProductTypePerLocationStatisticsJob;
use Illuminate\Console\Command;

class ComputeNoDeliveredReturnedItemsPerProductTypePerLocationStatisticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:no-delivered-returned-items-per-product-type-per-location {startDate?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Computes the statistics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startDateString = $this->argument('startDate');
        ComputeDeliveryReturnPerProductTypePerLocationStatisticsJob::dispatchNow($startDateString);
    }

}
