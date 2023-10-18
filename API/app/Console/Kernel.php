<?php

namespace Cintas\Console;

use Cintas\Jobs\Statistics\ComputeDeliveryReturnPerProductTypePerLocationStatisticsJob;
use Cintas\Jobs\Statistics\ComputeNoItemsPerProductTypePerLocationStatisticsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $timezone = config('cintas.time.timezone');

        $schedule
            ->job(new ComputeNoItemsPerProductTypePerLocationStatisticsJob)
            ->dailyAt('22:00:00')
            ->withoutOverlapping();

        $schedule
            ->job(new ComputeDeliveryReturnPerProductTypePerLocationStatisticsJob)
            ->dailyAt('22:00:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
