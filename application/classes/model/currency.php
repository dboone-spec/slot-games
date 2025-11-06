<?php

class Model_Currency extends ORM {

//    protected $_table_name = '';

    public function sym() {

        if(empty($this->icon)){
            return '';
        }

        $icon = mb_chr(intval($this->icon,16));

        $parts=explode(',',$this->icon);

        if (count($parts) > 1) {
            $icon = '';
            foreach($parts as $c) {
                $icon.= mb_chr(intval($c,16));
            }
        }

        return $icon;
    }

    public function formatBet($x=1,$is_num=false,$no_format=false) {

		if($this->mult==0) {
            return $this->moon_min_bet*$x;
        }

		if($x>10000 && !$is_num) {
            $suffixes = ['', 'K', 'M', 'B', 'T', 'Qa', 'Qi'];
            $index = (int) log(abs($x), 1000);
            $index = max(0, min(count($suffixes) - 1, $index));
            $x=floatval($this->moon_min_bet)>0?$x*$this->moon_min_bet:$x*0.1;
            return th::float_format($x / 1000 ** $index,$this->mult) . $suffixes[$index];
        }

        $float=floatval($this->moon_min_bet)>0?$x*$this->moon_min_bet:$x*0.1;

        if($float<1) {
            $bet = rtrim(sprintf('%.'.$this->mult.'F',$float),'0');
        }
        elseif(!$no_format) {
            $bet = th::float_format(floatval($this->moon_min_bet)>0?$x*$this->moon_min_bet:$x*0.1,$this->mult);
        } else {
            $bet = floatval($this->moon_min_bet)>0?$x*$this->moon_min_bet:$x*0.1;
        }

        $dots = explode('.',"".$bet);

        if(count($dots)>1 && $dots[1]=='00') {
            $bet=$dots[0];

            if(strlen($bet)>=4 && strpos($bet,'000')>=1) {

                if($is_num) {
                    return $bet;
                }

                return str_replace('000','',$bet).'K';
            }
        }


        return $bet;
    }
	
	public function getTimeZoneSeconds() {
        if(empty($this->timezone)) {
            return 0;
        }

        $timestr=explode('UTC',$this->timezone)[1];

        list($hour,$minute)=explode(':',$timestr);
        $hour=trim($hour,'±');

        return $hour*Date::HOUR+$minute*Date::MINUTE;
    }
	
	
	
	    public function labels()
    {
        return [
            'name' => 'Name',
            'val' => 'Val (EUR)',
            'code' => 'Code',
            'iso_4217' => 'ISO 4217',
            'source' => 'Source',
            'updated' => 'Updated',
            'crypto'  => 'Crypto',
			'country_update' =>'Country updated',
        ];
    }
	
	
}