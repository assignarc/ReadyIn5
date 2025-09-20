<?php
namespace RI5\Services\Traits;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\Font;

trait QrCodeTrait{
    
    private string $LOGO_URL = "images/ri5/logo.png";
    private int $LOGO_RESIZETOWIDTH = 60;
    private string $ENCODING = "UTF-8";
    private int $IMAGE_SIZE = 300;
    private int $IMAGE_MARGIN = 0;
    private $LABEL_SIZE = 20;
    /**
     * Summary of gnerateQrCode
     * @param string $url
     * @param string $labelText
     * @param bool $includeLogo
     * @return string
     */
    function gnerateQrCode(string $url, string $labelText, bool $includeLogo=true) : string {
        $writer = new \Endroid\QrCode\Writer\SvgWriter();
       $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            size: 300,
            margin: 10,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );
        $label =null;
        $logo = null;
       
        if($labelText && $labelText!=""){
            $label = new Label($labelText, font: new Font($this->LABEL_SIZE));
        }
        if($includeLogo)
            $logo = new \Endroid\QrCode\Logo\Logo($this->LOGO_URL, resizeToWidth: $this->LOGO_RESIZETOWIDTH);
      
        $result =  $writer->write($qrCode,$logo,$label)->getDataUri();

        return $result;

    }
}
