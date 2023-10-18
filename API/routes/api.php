<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API Logger
Route::post('log/{severity}', 'Shared\ApiLoggerController');

// Items
Route::post('bundled-items', 'Items\ItemController@getBundledItemsByEPC');
Route::post('items-by-epc', 'Items\ItemController@findByEPC');
Route::resource('items', 'Items\ItemController');
Route::apiResource('items/details', 'Items\ItemDetailsController');
Route::apiResource('items/{itemId?}/status', 'Items\ItemStatusController');

// Products and Types
Route::apiResource('products/types', 'Items\ProductTypeController');

// Identifiers
Route::apiResource('identifiers', 'Identifiers\IdentifiersController');
Route::get('identifiables/{type}/{id}', 'Identifiers\IdentifiableController@show');
Route::get('identifiables', 'Identifiers\IdentifiableController@index');

// Customers
Route::apiResource('facilities/{facilityId}/customers', 'Facility\CustomerController');
Route::apiResource('facilities/{facilityId}/locations', 'Facility\LocationController');
Route::apiResource('locations', 'Facility\LocationController');

// Reads
Route::apiResource('reads/dryer-read', 'Read\DryerReadController');
Route::apiResource('reads/portal-read', 'Read\PortalController');
Route::apiResource('reads/portal-in-read', 'Read\PortalInController');
Route::apiResource('reads/portal-out-read', 'Read\PortalOutController');
Route::apiResource('reads/table-read', 'Read\TableController');

Route::apiResource('stocktakings', 'Stocktaking\StocktakingController');

// Statistics
Route::get('statistics/items-at-location', 'Statistics\ItemsAtLocationController');
Route::get('statistics/items-at-facility/{facilityCuid?}', 'Statistics\ItemsAtFacilityStatisticsController');
Route::get('statistics/items-per-location-per-product', 'Statistics\NoItemsPerProductPerCustomerController');

Route::get('statistics/locations-with-lost-items', 'Statistics\LocationsWithLostItemsController');
Route::get('statistics/no-lost-items/{limit?}', 'Statistics\NumberOfLostItemsController');
Route::get('statistics/no-lost-and-existing-items/{limit?}', 'Statistics\NumberOfLostAndExistingItemsController');
Route::get('statistics/no-lost-items-for-location/{locationType?}/{locationCuid?}', 'Statistics\NoLostItemsPerProductForLocationController');
Route::get('statistics/no-lost-items-per-customer', 'Statistics\NoLostItemsPerProductPerCustomerController');
Route::get('statistics/top-locations-with-lost-items/{n?}', 'Statistics\TopNLocationswithLostItemsController');

Route::get('statistics/no-items-over-time/{locationType?}/{locationCuid?}', 'Statistics\NoItemsPerProductForLocationController');


Route::get('statistics/incoming-outgoing-products', 'Statistics\IncomingAndOutgoingProductsInTimeController');
Route::get('statistics/delivered-products-per-customer', 'Statistics\DeliveredProductsInTimeController');
Route::get('statistics/returned-products', 'Statistics\ReturnedProductsInTimeController');

Route::get('statistics/incoming-outgoing-products-over-time/{locationType}/{locationCuid}/{productTypeCuid}', 'Statistics\NoDeliveredAndReturnedItemsPerProductTypePerLocationStatisticController');


Route::get('statistics/lifecycle-delta-per-product', 'Statistics\LifecycleDeltaPerProduct');
Route::get('statistics/avg-turnaround-time-per-product', 'Statistics\AvgTurnaroundTimePerProductController');
Route::get('statistics/grouped-avg-cycle-count', 'Statistics\GroupedAvgCycleCountController');
Route::get('statistics/avg-cycle-count-by-product', 'Statistics\AvgCycleCountByProductController');

Route::get('statistics/target-container-reach-today', 'Statistics\TargetContainerReachTodayController');
Route::get('statistics/target-container-reach-per-scan', 'Statistics\TargetContainerReachInPeriodController');
Route::get('statistics/aggregated-bundle-ratio-in-outscans', 'Statistics\AggregatedBundleRatioInOutscansController');
Route::get('statistics/bundle-ratio-in-outscans', 'Statistics\BundleRatioInOutscansController');

// Data
Route::apiResource('reads/scan-actions', 'Read\ScanActionsController');


// Exports
Route::get('export/scan-actions', 'Exports\ScanActionsExportController');
Route::get('export/scan-action-details', 'Exports\ScanActionsDetailsExportController');

Route::get('export/age-summary', 'Exports\AgeSummaryExportController');
Route::get('export/no-items-per-product-type-over-time/{locationType}/{locationCuid}', 'Exports\NoItemsPerProductTypeOverTimeExportController');
Route::get('export/incoming-outgoing-products-over-time/{locationType}/{locationCuid}/{productTypeCuid}', 'Exports\IncomingOutgoingProductsOverTimeExportController');
Route::get('export/items', 'Exports\ItemsExportController');

