<?php defined('SYSPATH') OR die('No direct script access.');

class Image_GD extends Kohana_Image_GD {


    public function black(){
        $this->_load_image();

        $bl = imagecolorallocatealpha($this->_image, 0, 0, 0, 50);

        imagefilledrectangle($this->_image, 0, 0, $this->width, $this->height, $bl);
    }

    public function text($text,$x,$y,$size=14,$color=[255,255,255]){


        $this->_load_image();

        $fontPath = DOCROOT.implode(DIRECTORY_SEPARATOR,['games','agt','tgbot','common','Bebas.ttf']);

        $colorInt = hexdec(dechex($color[0]).dechex($color[1]).dechex($color[2]));

        imagettftext($this->_image, $size, 0, $x, $y, $colorInt, $fontPath, $text);

    }





}
