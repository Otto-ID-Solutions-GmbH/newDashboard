<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 14.10.2018
 * Time: 20:24
 */

namespace Cintas\Repositories;


use Carbon\Carbon;
use Cintas\Models\AbstractModel;
use Cintas\Models\Facility\Bundle;
use Cintas\Models\Items\ItemStatusType;
use Illuminate\Database\Eloquent\Collection;

interface ItemRepository
{
    /**
     * Identify one or more items by providing EPCs.
     *
     * @param array | string $epc one EPC or a list of EPCs for which items shall be identified
     * @return mixed
     */
    public function identifyItems($epc);

    /**
     * Identify all items by the provided list of EPCs. In addition, identify additional items via bundle information, if available.
     * @param array | string $epc one EPC or a list of EPCs for which items shall be identified
     * @param float $threshold a threshold value when to identify a bundle. All bundled items are added only when this threshold is met. Otherwise, only the single items are added.
     * @param bool $bundleItemsOnly return only items within a valid bundle and discard single items without bundle
     * @return mixed
     */
    public function identifyBundledItems($epc, $threshold = null, $bundleItemsOnly = false);

    /**
     * Bundles the given items together
     * @param Collection<Item>|Item[] $items a list of items to bundle together
     * @param boolean $unbundleFirst if true, unbundles all items und destroys the bundles before bundeling the together
     * @return Bundle the bundle that was created
     */
    public function bundleItems($items, $unbundleFirst = true);

    /**
     * Unbundles the given items and destroys the bundles that were associated to each item (i.e., all items within a bundle are unbundled)
     * @param Collection<Item>|Item[] $items a list of items to unbundle
     * @return Collection<Item> the list of items, unbundled
     */
    public function unbundleItems($items);

    /**Creates new item statuses for all items and updates the items' last status property
     * @param Collection<Item> &$items a list of items for which the statuses shall be created
     * @param ItemStatusType | string $statusType the item status type or the ID of the type
     * @param AbstractModel | null $location a location assigned to the status
     * @param Carbon | string | null $timestamp a timestamp of the status. The current time will be used, if blank
     * @param bool $refreshItems if set to true, the list of items will be re-hydrated from the database in order to reflect the status changes in last_status
     * @return string[] the CUIDs of the created stati
     */
    public function setItemStatuses(&$items, $statusType, $location = null, $timestamp = null, $refreshItems = false);

    public function getItemStatuses($itemCuid);
}
