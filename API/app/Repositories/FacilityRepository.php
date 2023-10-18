<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 14.10.2018
 * Time: 20:24
 */

namespace Cintas\Repositories;


use Cintas\Models\Actions\OutScanAction;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ItemStatusType;
use Illuminate\Support\Collection;

interface FacilityRepository
{


    public function getLocations(string $facilityCuid = null);

    /**
     * Returns the list of customers served by the facility
     * @param string $facilityCuid the CUID of the facility
     * @return mixed
     */
    public function getCustomers(string $facilityCuid = null);

    /**
     * Return a customer by its CUID
     * @param string $cuid the CUID of the customer
     * @return mixed
     */
    public function getCustomer(string $cuid);

    /**
     * @param string[] | Item[] | Collection $items
     * @param string | Facility $facility
     * @param string|null $timestamp the timestamp of the income
     * @param bool $unbundleItems if true, unbundle incoming items
     * @param ItemStatusType | string | null $statusType the status type
     * @return mixed A list of statuses created for the incoming items
     */
    public function registerIncomingItems($items, $facility, $timestamp = null, $unbundleItems = false, $statusType = null);

    /**
     * @param $items string[] | Item[] | Collection<Item> $items
     * @param LaundryCustomer | null $target
     * @param string | null $timestamp
     * @return mixed
     */
    public function registerOutgoingItems($items, $target, $timestamp = null);

    /**
     * Generate a PDF label for outgoing items
     * @param OutScanAction $outScanAction
     * @return mixed
     */
    public function generateLabelForOutscan($outScanAction);

    public function printPdfFile($pdfFileName);


}
