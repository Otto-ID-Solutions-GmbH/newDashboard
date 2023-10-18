<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 31.10.2018
 * Time: 18:21
 */

namespace Cintas\Repositories;


interface ProductStatisticsRepository
{

    /**
     * Retrieve per product the average delta of cycles against the expected lifetime of items that have been sorted out.
     * @return mixed
     */
    public function getLifecycleDeltaPerProduct();

    public function getAvgTurnaroundTimeByProductType($period, $start = null, $end = null, $customer = null);

    public function getGroupedAvgCycleCount();

    public function getAvgCycleCountByProduct();

    //public function getAvgTurnaroundTimeByProduct($period, $start = null, $end = null);

    public function getDeliveredProductsByCustomerTimeline($period, $start = null, $end = null);

    public function getReturnedProductsByCustomerTimeline($period, $start = null, $end = null);

    public function getIncomingAndOutgoingProductsTimeline($period, $start = null, $end = null);

    public function getDeliverAndReturnTimeline($locationId, $productId, $period, $start = null, $end = null);

}
