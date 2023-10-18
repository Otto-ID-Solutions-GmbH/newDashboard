<?php

namespace Cintas\Jobs\Statistics;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Cintas\Models\Statistics\NoItemsPerProductTypePerLocationStatistic;
use EndyJasmi\Cuid;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ComputeNoItemsPerProductTypePerLocationStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startDateString;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($startDateString = null)
    {
        if ($startDateString) {
            $this->startDateString = $startDateString;
        } /*else {
            $lastDate = Carbon::make(NoItemsPerProductTypePerLocationStatistic::query()
                ->max('date'));
            $this->startDateString = $lastDate->addDay()->toIso8601String();
        }*/
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $startDateString = $this->startDateString;

        $prev = null;

        $format = 'Y-m-d';
        $timezone = 'UTC';
        $start = Carbon::parse($startDateString)->timezone($timezone)->startOfWeek();
        $end = Carbon::now($timezone);
        $interval = config('statistics.items_at_location.interval', '1 day');
        $periodRange = new CarbonPeriod($start, $interval, $end);

        $insertData = [];

        Log::info("Started computing NoItemsPerProductTypePerLocation statistics from $start to $end (interval $interval)...");

        foreach ($periodRange as $key => $date) {
            $exists = $exists = NoItemsPerProductTypePerLocationStatistic::where('date', '=', $date)->first();
            if ($exists) {
                Log::warning("Data for date $date already existed, skipping date...");
                continue;
            } else {
                $data = $this->getData($date);
            }

            foreach ($data as $datum) {
                $exists = NoItemsPerProductTypePerLocationStatistic::where('date', '=', $date)
                    ->where('location_id', '=', $datum->location_id)
                    ->where('product_type_id', '=', $datum->product_type_id)
                    ->first();

                if (!$exists) {
                    $res = [
                        'cuid' => Cuid::make(),
                        'created_at' => Carbon::now('UTC')->toDateTimeString(),
                        'updated_at' => Carbon::now('UTC')->toDateTimeString(),
                        'date' => $date,
                        'location_id' => $datum->location_id,
                        'location_type' => $datum->location_type ?? 'Unknown',
                        'product_type_id' => $datum->product_type_id,
                        'no_items_at_location' => $datum->item_count,
                    ];
                    array_push($insertData, $res);
                } else {
                    Log::warning('Updated existing statistics for entry #' . $exists->cuid);
                    $exists->no_items_at_location = $datum->item_count;
                    $exists->save();
                }
            }

            NoItemsPerProductTypePerLocationStatistic::insert($insertData);
            $insertData = [];

        }

        Log::info("Completed staNoItemsPerProductTypePerLocation statistics from $start to $end (interval $interval)...");

    }

    private function getData($timestamp)
    {

        return DB::select('
        select ist.location_id, ist.location_type, p.product_type_id, count(i.cuid) as item_count
        from items as i
                 inner join products as p on p.cuid = i.product_id
                 inner join item_statuses as ist on i.cuid = ist.item_id
        where i.deleted_at is null
          and ist.cuid = (
            SELECT ist2.cuid
            FROM item_statuses as ist2
            WHERE ist2.item_id = i.cuid
              and ist2.created_at <= :dat
            ORDER BY ist2.created_at DESC
            LIMIT 1
        )
        group by ist.location_id, ist.location_type, p.product_type_id;
        ', ['dat' => $timestamp]);

    }
}
