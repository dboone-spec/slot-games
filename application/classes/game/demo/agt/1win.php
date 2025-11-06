<?php

class game_demo_agt_1win extends game_demo_agt
{

    public function buyfg($li = 0, $defamount = 0, $didx = 0) {

        $freeCountAll = $this->_calc->getTotalFreeCount();
        $freeCountCurrent = game::data('freeCountCurrent', 0);

        if($freeCountAll>0 && $freeCountCurrent != $freeCountAll)
        {
            throw new Exception('spin is disabled. freerun mode is active '.$this->_game.' '.auth::$user_id.'; freeCountCurrent: '.$freeCountCurrent.'; freeCountAll: '.$freeCountAll);
        }

        $this->_calc->is_buy=true;
        $amount=$defamount*$this->_config['bonus_buy_price'];
        $ans=$this->spin($li,$amount,$didx);

        $this->_session['amount'] = $defamount;
        $this->save();

        return $ans;
    }

    public function bonus_game()
    {
        $ans=parent::bonus_game();

        return $ans;
    }

    public function restore()
    {
        $ans = parent::restore();
        return $ans;
    }
}
