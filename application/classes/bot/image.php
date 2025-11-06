<?php

class Bot_Image{

    private $low=[
        'bgWidth'=>350,
        'width'=>50,
        'height'=>50,
        'offsetY'=>23,
        'fontSize'=>14,
        'gamblewidth'=>90,
        'history'=>[
            'width'=>30
        ]
    ];

    private $high=[
        'bgWidth'=>350*2,
        'width'=>50*2,
        'height'=>50*2,
        'offsetY'=>23*2,
        'fontSize'=>14*2,
        'gamblewidth'=>140,
        'history'=>[
            'width'=>30*2
        ]
    ];


    public function __construct($size='low') {

        Image::$default_driver='imagick';

        $this->sizeChoosed=$size;
        $this->size=$this->$size;

    }


    public function dir($name){
        return DOCROOT.'games'.DIRECTORY_SEPARATOR.'agt'.DIRECTORY_SEPARATOR.'tgbot'.DIRECTORY_SEPARATOR.$this->sizeChoosed.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
    }


    public function gameDir($game){
        return DOCROOT.'games'.DIRECTORY_SEPARATOR.'agt'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'games'.DIRECTORY_SEPARATOR.$game.DIRECTORY_SEPARATOR;
    }


    public function image2($game,$history,$win=-1,$card=-1,$balance=-1){

        $tgCommon=$this->dir('common');
        $tgDir=$this->dir($game);
        $back=Image::factory($tgDir.'back.jpg');

        if ($card==-1){
            $cardBack=Image::factory($tgDir.'gamble_card_back.png');
        }
        elseif($win>=0) {
            $cardBack=Image::factory($tgCommon."card{$card}big.png");
        }
        else{
            $cardBack=Image::factory($tgCommon."card{$card}.png");
        }

        $offsetBGX=$this->size['bgWidth']/2-90/2;
        $offsetTextX=$offsetBGX+5;
        $offsetY=35;

        $back->watermark($cardBack,$offsetBGX,$offsetY*$this->size['fontSize']/14);


        $suits=[];
        $num=0;

        foreach ($history as $h) {
            if (!(isset($suits[$h]))) {
                $suits[$h]=Image::factory($tgCommon."card{$h}.png");
            }
            $back->watermark($suits[$h],$offsetBGX+90*$this->size['fontSize']/14+$num*$this->size['history']['width']*1.17,5);
            $num++;
            if ($num>=3){
                break;
            }
        }


        if($win<0) {
            $offsetTextX=5;
            $txt='Guess the color of the next card and double your winnings';
        }
        elseif($win==0) {
            $offsetTextX+=20;
            $txt='You lose';
        }
        else {
            $txt='You won '.th::number_format($win);
        }

        $back->text($txt,$offsetTextX,190*$this->size['fontSize']/14,$this->size['fontSize']*12/14);

        if($balance>=0) {
            $back->text('balance: '.th::number_format($balance),20,20*$this->size['fontSize']/14,$this->size['fontSize']);
        }

        return $back;

    }


    public function imageFG($data){
        $back = $this->image($data);
        $back->text('FG: '.$data['bonus'].'/'.$data['bonus_all'],100*$this->size['fontSize']/14,20*$this->size['fontSize']/14,$this->size['fontSize']);
        return $back;
    }

    public function get_win_symbols($lm,$game) {

        $lm=array_filter($lm,function($v) {
            return $v>0;
        });

        $lines=Kohana::$config->load('agt/'.$game)['lines'];

        $res_syms=[];

        foreach($lm as $l=>$m) {

            if($l<=0) {
                //todo with scatters
                continue;
            }

            $last=strrpos(decbin($m),'1');
            foreach($lines[$l] as $height=>$reel) {
                if(!isset($res_syms[$height])) {
                    $res_syms[$height]=[];
                }

                foreach($reel as $length=>$bar) {
                    if($bar!=1) {
                        continue;
                    }
                    if($length<=$last && !in_array($length,$res_syms[$height])) {
                        $res_syms[$height][]=$length;
                    }
                }
            }
        }

        return $res_syms;
    }

    protected function correctSize($game){
        if(in_array($game,['bluestar100','dreamcatcher100'])) {
            $this->size['height']=$this->size['height']*1;
        }
    }

    public function image($data){

        $this->correctSize($data['game']);


        $tgDir=$this->dir($data['game']);
        $back=Image::factory($tgDir.'back.jpg');

        $syms= array_unique($data['sym']);
        $icons=[];

        $win_symbols=null;
        if($data['win']>0) {
            $win_symbols=$this->get_win_symbols($data['lm'],$data['game']);
        }

        foreach ($syms as $sym){
            $icons[$sym]=Image::factory($tgDir."icon{$sym}.png");
            $blackicons[$sym]=Image::factory($tgDir."blackicon{$sym}.png");
        }

        //from vidget
        $reel_height=3;
        $bars=5;

        if(count($data['sym'])==20) {
            $reel_height=4;
        }
        if(count($data['sym'])==9) {
            $bars=3;
            $reel_height=3;
        }

        foreach ($data['sym'] as $num=>$sym){
            $pos=$this->pos($num,$bars,$win_symbols,$reel_height);

            $back->watermark(($pos['is_win'])?$icons[$sym]:$blackicons[$sym], $pos['x'], $pos['y']);

        }

        $back->text('bet: '.th::number_format($data['amount']),20,20*$this->size['fontSize']/14,$this->size['fontSize']);
        $back->text('balance: '.th::number_format($data['balance']),220*$this->size['fontSize']/14,20*$this->size['fontSize']/14,$this->size['fontSize']);



        if (isset($data['bonus_win']) && $data['bonus_win']>0){
            $back->text('FG won: '.$data['bonus_win'],2*$this->size['bgWidth']/3-15*$this->size['fontSize']/14,195*$this->size['fontSize']/14,$this->size['fontSize']+8,[255,215,55]);
            $back->text('won: '.$data['win'],$this->size['bgWidth']/3-50*$this->size['fontSize']/14,195*$this->size['fontSize']/14,$this->size['fontSize']+8,[255,215,55]);
        }
        else if ($data['win']>0){
            $back->text('won: '.$data['win'],$this->size['bgWidth']/2-25*$this->size['fontSize']/14,195*$this->size['fontSize']/14,$this->size['fontSize']+8,[255,215,55]);
        }

        return $back;

    }


    public function pos($num,$barcount=5,$win_symbols=null,$reel_height=3){


        $width=$this->size['width'];
        $height=$this->size['height']*3/$reel_height;

        $offsetX=$this->size['bgWidth']/2-$this->size['width']*$barcount/2;


        $offsetY=$this->size['offsetY'];

        if($barcount==3) {
            $height=$this->size['height']*3/5;
            $offsetY+=$this->size['height']*3/5;
        }

        $x = $num % $barcount;

        $y=floor(($num)/$barcount);

        $is_win=false;

        if(!empty($win_symbols)) {
            $is_win=isset($win_symbols[$y]) && in_array($x,$win_symbols[$y]);
        }
        else {
            $is_win=true;
        }

        $x=$offsetX+($x)*$width;
        $y=$offsetY+$y*$height;

        return ['x'=>$x,'y'=>$y,'is_win'=>$is_win];

    }


    public function info($gameId,$num=1){


        $file=DOCROOT.'games'.DIRECTORY_SEPARATOR.'agt'.DIRECTORY_SEPARATOR.'tgbot'.DIRECTORY_SEPARATOR.'info'.DIRECTORY_SEPARATOR.$gameId.'_'.$num.'.jpg';
        if (!file_exists($file)){
            return false;
        }

        return "games/agt/tgbot/info/{$gameId}_$num.jpg";


    }


    public function initCommon(){

        $tgDir=$this->dir('common');
        th::force_dir($tgDir);
        $gameDir=DOCROOT.'games'.DIRECTORY_SEPARATOR.'agt'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'ui'.DIRECTORY_SEPARATOR;

        for($i=0;$i<4;$i++) {
            $file=$gameDir.'card'.$i.'.png';
            $card=Image::factory($file);
            $card->resize($this->size['gamblewidth']);
            $card->save($tgDir."card{$i}big.png");

            $card->resize($this->size['history']['width']);
            $card->save($tgDir."card$i.png");
        }

    }



    public function initGame($game){

        $gameConf= Kohana::$config->load('agt/'.$game);

        $line=$gameConf['lines'][1];
        if (count($line)!=3 && count($line)!=4) {
            echo "$game is NOT ok (height is not 3)\r\n";
            return null;
        }
        if (count($line[0])!=5 && count($line[0])!=3) {
            echo "$game is NOT ok (width is not 5)\r\n";
            return null;
        }


        $tgDir=$this->dir($game);
        th::force_dir($tgDir);

        $gameDir=$this->gameDir($game);

        $back=$gameDir.'ui'.DIRECTORY_SEPARATOR.'emptyback.jpg';

        if (!file_exists($back)){
            echo "$game is NOT ok ($back)\r\n";
            return null;
        }

        $img = Image::factory($back);
        $img->resize($this->size['bgWidth']);
        $img->save($tgDir.'back.jpg');

        $icons= array_keys($gameConf['pay']);

        foreach ($icons as $icon){
            $iconFile=$gameDir.'icons'.DIRECTORY_SEPARATOR.'icon'.$icon.'.png';
            if (!file_exists($iconFile)){
                echo "$game is NOT ok ($iconFile)\r\n";
                return null;
            }
            $img = Image::factory($iconFile);
            $img->resize($this->size['width']);
            $img->save($tgDir."icon{$icon}.png");

            $imgBlack = Image::factory($tgDir."icon{$icon}.png");
            $imgBlack->black();
            $imgBlack->save($tgDir."blackicon{$icon}.png");
        }


        ///cards for double
        $img=$gameDir.'ui'.DIRECTORY_SEPARATOR.'gamble_card_back.png';
        if (!file_exists($img)){
               echo "$game is NOT ok ($iconFile)\r\n";
               return null;
           }
        $img = Image::factory($img);
        $img->resize($this->size['gamblewidth']);
        $img->save($tgDir."gamble_card_back.png");


        echo "$game is OK\r\n";

    }


}

