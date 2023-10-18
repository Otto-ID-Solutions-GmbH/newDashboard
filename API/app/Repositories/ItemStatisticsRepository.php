<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 31.10.2018
 * Time: 18:21
 */

namespace Cintas\Repositories;


use Carbon\Carbon;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Items\ProductType;
use Cintas\Models\Polymorphism\LocationContract;

interface ItemStatisticsRepository
{

    /**
     * Return locations and how many items are currently at that location
     * @param bool $includeNewItems
     * @param bool $includeUnknownItems
     * @param null $filterLocationType
     * @return mixed simple series data of locations and number of items
     */
    public function getItemsAtLocationStatistics(bool $includeNewItems = false, bool $includeUnknownItems = true, $filterLocationType = null);

    /**
     * Retrieve items at facilities and their location within the facility
     * @param string $facilityCuid the CUID of the facility
     * @return mixed
     */
    public function getItemsAtFacilityStatistics($facilityCuid);

    public function getItemsPerFacilityStatistics();

    /**
     * Retrieves the number of items per product per customer
     * @param bool $circulatingOnly
     * @param null $limit
     * @param LaundryCustomer | null $customer
     * @param bool $includeNewItems
     * @param bool $includeUnknownItems
     * @param null $filterLocationType
     * @return mixed
     */
    public function getNoItemsPerProductPerCustomer($circulatingOnly = false, $limit = null, $customer = null, bool $includeNewItems = false, bool $includeUnknownItems = true, $filterLocationType = null);

    /**
     * Get items that have not been processed since a given period of time
     * @param int $limit the number of days that must have passed to items be defined as outdated
     * @param Carbon|string $referenceDate the date at which the number of lost items is computed
     * @param null|string $filterLocationType
     * @param bool $includeUnknownItems
     * @return int the number of lost items
     */
    public function getNumberOfLostItems($limit = null, $referenceDate = null, $filterLocationType = null, $includeUnknownItems = true);

    /**
     * Retrieves the number of items that existed at a given date
     * @param Carbon|string $referenceDate the date at which the number of items is computed. Today will be used, if the parameter is not provided.
     * @param null|ProductType $filterByProductType a product type to filter for
     * @param null|string $filterLocationType
     * @param bool $includeUnknownItems
     * @return int the number of all items
     */
    public function getNumberOfItems($referenceDate = null, $filterByProductType = null, $filterLocationType = null, $includeUnknownItems = true);

    /**
     * Get the number of lost items per product type
     * @param int $limit the number of days that must have passed to items be defined as outdated
     * @param null|string $filterLocationType
     * @param bool $includeUnknownItems
     * @return mixed simple series data of products and counts of lost items
     */
    public function getNoLostItemsPerProductType($limit = null, $filterLocationType = null, $includeUnknownItems = true);

    /**
     * Retrieves the number of lost items per customer where the item was lost and per product type
     * @param int $limit
     * @return mixed
     */
    public function getNoLostItemsPerProductPerCustomer($limit = null);

    /**
     * Retrieves the number of lost items per product for a given customer
     * @param LaundryCustomer|Facility|string|null $location
     * @param int $limit
     * @return mixed
     */
    public function getNoLostItemsPerProductForLocation($location, $limit = null);

    /**
     * Get the number of lost items and number of actual existing items per product type
     * @param int $limit the number of days that must have passed to items be defined as outdated
     * @return mixed multi series data of products and counts of lost items and actual items
     */
    public function getLostAndExistingItemsPerProductType($limit = null);

    /**
     * Retrieves the location with the highest number of lost items
     * @param null $limit
     * @param int $n
     * @param null $filterLocationType
     * @param bool $includeUnknownItems
     * @return mixed
     */
    public function getTopNLocationsWithLostItems($limit = null, $n = null, $filterLocationType = null, $includeUnknownItems = true);

    /**
     * @param int $limit
     * @param string $period
     * @param $start
     * @param $end
     * @return mixed
     */
    public function getNoLostItemsPerProductTypePerTime($limit = null, $period = "This Week");

    /**Retrieve all locations that have lost items
     * @param $limit
     * @param null $filterLocationType
     * @param bool $includeUnknownItems
     * @return mixed
     */
    public function getLocationsWithLostItems($limit, $filterLocationType = null, $includeUnknownItems = true);

    /**
     * @param LocationContract|string $location
     * @param string $period
     * @param string|Carbon|null $start
     * @param string|Carbon|null $end
     * @return mixed
     */
    public function getNoItemsPerProductTypePerTimeAtLocation($location, $period = "This Week", $start = null, $end = null);

}
