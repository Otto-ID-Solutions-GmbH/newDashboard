<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 24.07.2016
 * Time: 17:21
 */

namespace Cintas\PDF\Identity;

use Cintas\PDF\AbstractVisBosPdf;
use EndyJasmi\Cuid;

const STANDARD_FONT_SIZE = 10;

class OutscanLabelPdf extends AbstractVisBosPdf
{

    protected $bcStyle = array(
        'position' => '',
        'align' => 'C',
        'stretch' => false,
        'fitwidth' => true,
        'cellfitalign' => 'C',
        'border' => false,
        'padding' => 0,
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, //array(255,255,255),
        'text' => true,
        //'font' => 'Ingra',
        //'fontsize' => 8,
        //'fontstyle' => '',
        'stretchtext' => 0
    );

    public function __construct()
    {

        parent::__construct("L", "mm", [28.575, 88.9]);

        //$this->SetFont('Ingra', "B", 10, base_path('/resources/assets/fonts/tcpdf/ingra'));

        $this->SetTitle('Cintas container label');
        $this->SetKeywords('Cintas, RFID, Trucount');

        $this->SetMargins(5, 2, -1);

        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);

        // set auto page breaks
        $this->SetAutoPageBreak(false);

        //$this->FontStyle = "B";

    }

    //Page header
    public function Header()
    {

    }

    // Page footer
    public function Footer()
    {

    }

    public function printDocument($models = null)
    {

        if (!$models || empty($models)) {
            throw new \Exception("No barcode models provided!");
        }

        if (!is_array($models)) {
            $models = [$models];
        }

        foreach ($models as $model) {
            $this->printBarcodePage($model);
        }

        // reset pointer to the last page
        $this->lastPage();
    }

    private function printBarcodePage($model)
    {

        $cuid = $model->cuid;
        $date = $model->created_at->timezone('EST')->format('Y-m-d, H:i:s e');

        $source = $model->scan_action->reader->facility;
        $sourceLabel = $source->label;
        $targetLabel = ($model->location->label) ?? 'Unknown';

        $this->AddPage();

        $content = $model->scan_action->items
            ->groupBy('product.label')
            ->map(function ($group, $label) {
                return $group->count() . " x " . $label;
            });

        $contentString = $content->implode(', ');

        $titleText = "$sourceLabel > $targetLabel\n$date\n$contentString";

        $this->SetFontSize(10);
        $this->FontStyle = "B";
        $this->MultiCell(0, 15, $titleText, 0, "L", false, 1, '', '', true, 0, false, true, 15, 'T', true);

        $this->SetFontSize(8);
        $this->write1DBarcode($cuid, "C128", 10, 18, 70, 5, 1, $this->bcStyle, 'N');

    }

    public function generateFileName($model): string
    {
        $string = "outscan-label-" . ($model ? $model->cuid : Cuid::make());
        return $string;
    }

}
