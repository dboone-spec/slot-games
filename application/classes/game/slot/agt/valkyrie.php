<?php

class game_slot_agt_valkyrie extends game_slot_agt
{
    public function buyfg($li = 0, $amount = 0, $didx = 0) {
		
		$freeCountAll = $this->_calc->getTotalFreeCount();
        $freeCountCurrent = game::data('freeCountCurrent', 0);

        if($freeCountAll>0 && $freeCountCurrent != $freeCountAll)
        {
            throw new Exception('spin is disabled. freerun mode is active '.$this->_game.' '.auth::$user_id.'; freeCountCurrent: '.$freeCountCurrent.'; freeCountAll: '.$freeCountAll);
        }

        $checkGame=$this->_calc->game_id;

        $fs = auth::user()->getFreespins(auth::$user_id,false,true,$checkGame);

        if($fs && $fs->loaded()) {
            throw new Exception('not allow buy');
        }
		
        $this->_calc->is_buy=true;
        $amount=$amount*$this->_config['bonus_buy_price'];
        return $this->spin($li,$amount,$didx);
    }

    public function bonus_game()
    {
        $ans=parent::bonus_game();

        if($ans['bonus']==0) {
            $this->_calc->isFreerun=false;
            $this->_calc->spin();
            $ans['comb_after_fg']=array_values($this->_calc->sym());
            //для restore
            $this->_session['comb_before_fg']=$ans['comb_after_fg'];
            $this->save();
        }

        return $ans;
    }

    public function restore()
    {
        $ans = parent::restore();
        $ans['comb_before_fg'] = $this->_session['comb_before_fg'] ?? $this->_session['comb'];
        return $ans;
    }
}
