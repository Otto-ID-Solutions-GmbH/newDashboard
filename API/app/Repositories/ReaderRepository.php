<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 16.10.2018
 * Time: 14:53
 */

namespace Cintas\Repositories;


use Carbon\Carbon;
use Cintas\Http\MessageTypes\ReadDataInput;
use Cintas\Http\MessageTypes\StocktakingDataInput;
use Cintas\Models\Actions\ScanAction;
use Cintas\Models\Facility\LaundryCustomer;
use Illuminate\Support\Collection;

interface ReaderRepository
{

    /**
     * Registers items that are read at a dryer reading point.
     * @param ReadDataInput[] $input a list of reads and the EPCs identified during the read
     * @return Collection<ScanAction> a collection of scan actions created
     */
    public function registerDryerIncomingItems($input);

    /**
     * @param ReadDataInput[] $input a list of reads and the EPCs identified during the read
     * @return Collection<ScanAction> a collection of scan actions created
     */
    public function registerCleanIncomingItems($input);

    /**
     * @param ReadDataInput[] $input a list of reads and the EPCs identified during the read
     * @param string | LaundryCustomer | null $target the target customer
     * @return Collection<ScanAction> a collection of scan actions created
     */
    public function registerOutgoingItems($input, $target = null);

    /**
     * @param ReadDataInput[] $input a list of reads and the EPCs identified during the read
     * @return Collection<ScanAction> a collection of scan actions created
     */
    public function registerBundlingOfItems($input);

    /**
     * Retrieves a list of scan actions within a given time
     * @param $start
     * @param $end
     * @param  $type
     * @return mixed
     */
    public function getScanActionsInTime($start = null, $end = null, $type = null, $sortBy = null, $sortDir = 'asc');

    public function getScanActionsInTimePaginated($start = null, $end = null, $type = null, $sortBy = null, $sortDir = 'asc');

    /**
     * @param null|Carbon $start
     * @param null|Carbon $end
     * @param null|string $type
     * @param null|string $sortBy
     * @param string $sortDir
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getScanActionsInTimeQuery($start = null, $end = null, $type = null, $sortBy = null, $sortDir = 'asc');

    /**
     * @param StocktakingDataInput $input
     * @return mixed
     */
    public function registerStocktaking($input);

}