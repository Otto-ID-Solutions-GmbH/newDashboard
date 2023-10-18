<?php

namespace Cintas\Console\Commands\Statistics;

use Cintas\Jobs\Statistics\ComputeNoItemsPerProductTypePerLocationStatisticsJob;
use Illuminate\Console\Command;

class ComputeNoItemsPerProductTypePerLocationStatisticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics:no-items-per-product-type-per-location {startDate?}';

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
        ComputeNoItemsPerProductTypePerLocationStatisticsJob::dispatchNow($startDateString);
    }

}
