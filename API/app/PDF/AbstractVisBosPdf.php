<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 24.07.2016
 * Time: 17:21
 */

namespace Cintas\PDF;

use TCPDF;
use VisBOS_API\Model\Identity\Barcode;

abstract class AbstractVisBosPdf extends TCPDF
{

    // Define barcode style
    protected $bcStyle = array(
        'position' => '',
        'align' => 'C',
        'stretch' => false,
        'fitwidth' => true,
        'cellfitalign' => '',
        'border' => false,
        'padding' => 0,
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, //array(255,255,255),
        'text' => true,
        'font' => 'Ingra',
        'fontsize' => 8,
        'stretchtext' => 0
    );

    const PIXEL_TO_POINT_FACTOR = 0.75;

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        // Set page size
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);

        // set document information
        $this->SetCreator('VisBOS by witcotech');


        // set image scale factor
        //$this->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set JPEG quality
        $this->setJPEGQuality(90);
        $this->setRasterizeVectorImages(false);

        // set default font subsetting mode
        $this->setFontSubsetting(false);

    }


    public abstract function printDocument($models = null);

    protected function imagettfbboxextended($size, $angle, $fontfile, $text)
    {
        /*this function extends imagettfbbox and includes within the returned array
        the actual text width and height as well as the x and y coordinates the
        text should be drawn from to render correctly.  This currently only works
        for an angle of zero and corrects the issue of hanging letters e.g. jpqg*/
        $bbox = imagettfbbox($size, $angle, $fontfile, $text);

        //calculate x baseline
        if ($bbox[0] >= -1) {
            $bbox['x'] = abs($bbox[0] + 1) * -1;
        } else {
            //$bbox['x'] = 0;
            $bbox['x'] = abs($bbox[0] + 2);
        }

        //calculate actual text width
        $bbox['width'] = abs($bbox[2] - $bbox[0]);
        if ($bbox[0] < -1) {
            $bbox['width'] = abs($bbox[2]) + abs($bbox[0]) - 1;
        }

        //calculate y baseline
        $bbox['y'] = abs($bbox[5] + 1);

        //calculate actual text height
        $bbox['height'] = abs($bbox[7]) - abs($bbox[1]);
        if ($bbox[3] > 0) {
            $bbox['height'] = abs($bbox[7] - $bbox[1]) - 1;
        }

        return $bbox;
    }

    protected function computeLabelDimensions($label, $labelFontSize, $font)
    {
        $comp = $this->imagettfbboxextended($labelFontSize, 0, $font, $label);
        $dummyLabel = $this->imagettfbboxextended($labelFontSize, 0, $font, 'qwertzuiopüasdfghjkjlöäyxcvbnm123456789QWERTZUIOPÜASDFGHJKLÖÄYXCVBNM,.?!/()|');

        $width = $comp['width'] * 0.264583; //$this->pixelsToUnits($comp['width']);
        $height = $dummyLabel['height'] * 0.264583;//$this->pixelsToUnits($dummyLabel['height']);
        $x = $comp['x'] * 0.264583;//$this->pixelsToUnits($comp['x']);
        $y = $comp['y'] * 0.264583;//$this->pixelsToUnits($comp['y']);

        return array('width' => $width, 'height' => $height, 'x' => $x, 'y' => $y);
    }

    protected function translateBarcodeType(Barcode $bc)
    {
        switch ($bc->ean_type) {
            case 'EAN_8':
                return 'EAN8';
            case 'EAN_13':
                return 'EAN13';
            default:
                return 'EAN8';
        }
    }

}