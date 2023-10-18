<?php

namespace Cintas\Listeners;

use Cintas\Events\OutScanRegistered;
use Cintas\Repositories\FacilityRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class PrintOutscanLabel implements ShouldQueue
{

    private $facilityRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(FacilityRepository $facilityRepository)
    {
        $this->facilityRepository = $facilityRepository;
    }

    /**
     * Handle the event.
     *
     * @param OutScanRegistered $event
     * @return void
     */
    public function handle(OutScanRegistered $event)
    {
        $pdfFile = $this->facilityRepository->generateLabelForOutscan($event->outScanAction);
        $this->facilityRepository->printPdfFile($pdfFile);
    }
}
