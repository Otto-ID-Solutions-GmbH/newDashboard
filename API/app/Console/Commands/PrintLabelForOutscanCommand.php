<?php

namespace Cintas\Console\Commands;

use Cintas\Models\Actions\OutScanAction;
use Cintas\Repositories\FacilityRepository;
use Illuminate\Console\Command;

class PrintLabelForOutscanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'label:print {actionCuid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print a label for a given outscan action';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FacilityRepository $repository)
    {
        //
        $cuid = $this->argument('actionCuid');
        $action = OutScanAction::findOrFail($cuid);

        $pdfFile = $repository->generateLabelForOutscan($action);
        $repository->printPdfFile($pdfFile);
    }
}
