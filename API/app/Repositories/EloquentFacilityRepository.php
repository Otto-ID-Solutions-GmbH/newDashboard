<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 14.10.2018
 * Time: 20:24
 */

namespace Cintas\Repositories;


use Carbon\Carbon;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ItemStatus;
use Cintas\Models\Items\ItemStatusType;
use Cintas\PDF\Identity\OutscanLabelPdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class EloquentFacilityRepository implements FacilityRepository
{

    private $itemRepository;

    /**
     * EloquentFacilityRepository constructor.
     * @param $itemRepository
     */
    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }


    public function getCustomers(string $facilityCuid = null)
    {
        $customerQuery = LaundryCustomer::query();

        if ($facilityCuid) {
            $customerQuery = $customerQuery->whereHas('served_by_facilities', function ($query) use ($facilityCuid) {
                return $query->where('facilities.cuid', '=', $facilityCuid);
            });
        }

        return $customerQuery->get();
    }

    public function getCustomer(string $cuid = null)
    {
        return LaundryCustomer::findOrFail($cuid);
    }


    public function registerIncomingItems($items, $facility, $timestamp = null, $unbundleItems = false, $statusType = null)
    {

        if (is_array($items) && $items[0] && is_string($items[0])) {
            $items = Item::find($items);
        }

        if (is_string($facility)) {
            $facility = Facility::find($facility);
        }

        if (!$facility) {
            throw new \BadMethodCallException("No valid facility was provided for registering incoming items!");
        }

        $statusType = is_string($statusType) ? ItemStatusType::findByName($statusType) : $statusType ?? ItemStatusType::findByName('AtFacilityStatus');
        $timestamp = new Carbon($timestamp) ?? Carbon::now();
        $timestamp = $timestamp->setTimezone('UTC');

        // Bulk create item status data
        $statusCuids = $this->itemRepository->setItemStatuses($items, $statusType, $facility, $timestamp, false);

        // Unbundle items if necessary
        if ($unbundleItems) {
            $this->itemRepository->unbundleItems($items);
        }

        return $items;

    }

    /**
     * @param $items string[] | Item[] | Collection<Item> $items
     * @param LaundryCustomer | null $target
     * @param string | null $timestamp
     * @return mixed
     */
    public function registerOutgoingItems($items, $target, $timestamp = null)
    {
        if (is_array($items) && $items[0] && is_string($items[0])) {
            $items = Item::find($items);
        }

        if ($target instanceof LaundryCustomer) {
            $statusType = ItemStatusType::findByName('AtCustomerStatus');
        } else if ($target instanceof Facility) {
            $statusType = ItemStatusType::findByName('AtFacilityStatus');
        } else {
            $statusType = ItemStatusType::findByName('AtUnknownCustomerStatus');
        }

        // Bulk create item status data
        $statusCuids = $this->itemRepository->setItemStatuses($items, $statusType, $target, $timestamp, false);

        return ItemStatus::find($statusCuids);
    }

    public function getLocations(string $facilityCuid = null)
    {
        $facilities = Facility::query();
        if ($facilityCuid) {
            $facilities = $facilities->where('cuid', '=', $facilityCuid);
        }
        $facilities = $facilities->get();
        $customerLocations = $this->getCustomers($facilityCuid);

        $locations = $customerLocations->concat($facilities);

        return $locations;
    }

    public function generateLabelForOutscan($outScanAction)
    {
        Log::debug("Generating label PDF for out-scan action #" . $outScanAction->cuid);
        $pdf = new OutscanLabelPdf();
        $pdf->printDocument($outScanAction);

        $filename = $pdf->generateFileName($outScanAction);
        $pdf->Output(base_path('.') . '/storage/app/public/' . $filename . '.pdf', 'F');

        return $filename;
    }

    public function printPdfFile($pdfFileName)
    {

        $pdfBoxPath = resource_path('lib/pdfbox-app-2.0.15.jar');
        $pdfFilePath = storage_path('app/public/' . $pdfFileName . '.pdf');
        $labelWriterName = config('cintas.label_printer.name');
        Log::debug("Printing PDF file " . $pdfFileName . "to printer " . $labelWriterName);

        $printCmd = "java -jar \"$pdfBoxPath\" PrintPDF -silentPrint -printerName \"$labelWriterName\" \"$pdfFilePath\"";

        $process = new Process($printCmd);
        $process->run();

        if (!$process->isSuccessful()) {
            $error = $process->getErrorOutput();
            Log::error("Error during label printing: " . $error);
        } else {
            Log::info("Sucessfully printed label");
        }

    }
}
