<?php
namespace RI5\Services\Traits;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Color\ColorInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\NotoSans;

trait QrCodeTrait{
    
    private string $LOGO_URL = "images/ri5/logo.png";
    private int $LOGO_RESIZETOWIDTH = 60;
    private string $ENCODING = "UTF-8";
    private int $IMAGE_SIZE = 300;
    private int $IMAGE_MARGIN = 0;
    private $LABEL_SIZE = 20;
    
    function gnerateQrCode(string $url, string $labelText=null, bool $includeLogo=true) : string {
        $writer = new \Endroid\QrCode\Writer\SvgWriter();
        $qrCode = QrCode::create($url)
                            ->setEncoding(new Encoding($this->ENCODING))
                            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                            ->setSize($this->IMAGE_SIZE)
                            ->setMargin($this->IMAGE_MARGIN)
                            ->setForegroundColor(new Color(0, 0, 0))
                            ->setBackgroundColor(new Color(255, 255, 255));

        $label =null;
        $logo = null;
       
        if($labelText && $labelText!=""){
            $label = Label::create($labelText)->setFont(new NotoSans($this->LABEL_SIZE));
        }
        if($includeLogo)
            $logo = \Endroid\QrCode\Logo\Logo::create($this->LOGO_URL)->setResizeToWidth($this->LOGO_RESIZETOWIDTH);
      
        $result =  $writer->write($qrCode,$logo,$label)->getDataUri();

        return $result;

    }
}
