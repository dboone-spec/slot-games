<?php

class demoCurrency {

    public $code='FUN';
    public $icon='#';
    public $mult=2;

}

class demoOffice {
    public $lang = 'en';
    public $currency="FUN";
    public $default_dentab=1;
    public $default_bet;
    public $enable_jp=0;
    public $enable_bia=0;
    public $apienable=0;
    public $owner=0;
    public $apitype = -1;

    public function activeJackpots() {
        return [];
    }

    public function get_k_list() {
        return [1, 2, 5, 10, 0.1, 0.2, 0.5];
    }

    public function __construct(){
        $this->currency=new demoCurrency;
    }


}

class demoFreespins {

    public function loaded(){

        return false;
    }


}



class Model_DemoUser //extends Model_User
{
    public $bets_arr;
    public $last_game=null;

    public function __construct($id = NULL)
    {

        //$this->_initialize();

        $this->office_id = OFFICE;
        $this->office = new demoOffice();

        $this->id=$id;
        $this->amount = 2000;

        $force_amount=dbredis::instance()->get('demoForceAmount'.$this->id);

        if($force_amount) {
            $this->amount=$force_amount;
        }

        $this->_loaded=true;
		$this->promo_started=null;
		$this->api=0;
        $this->_fs=new demoFreespins;

        $this->lang=dbredis::instance()->get('demoLang'.$this->id);

    }


    public function save(Validation $validation = NULL)
        {
                return $this;
        }


    public function getFreespins($user_id,$count_update=false) {
        return new demoFreespins;
    }



}
