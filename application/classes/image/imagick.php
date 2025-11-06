<?php defined('SYSPATH') OR die('No direct script access.');

class Image_Imagick extends Kohana_Image_Imagick {


    public function black(){

        $draw = new ImagickDraw();
        $draw->setFillColor('rgba( 0, 0, 0 , 0.5 )');
        $draw->rectangle(0, 0, $this->im->getImageWidth(), $this->im->getImageHeight());

        $this->im->drawImage($draw);
    }

    public function text($text,$x,$y,$size=14,$color=[255,255,255]){

        $fontPath = DOCROOT.implode(DIRECTORY_SEPARATOR,['games','agt','tgbot','common','Bebas.ttf']);

        $draw = new ImagickDraw();
        $draw->setFont($fontPath);
        $draw->setFontSize($size*1.5);
        $draw->setFillColor('rgb('.implode(',',$color).')');
        $this->im->annotateImage($draw, $x, $y, 0, $text);

    }





}
