<?php

class game_slot_novomatic_deluxe extends game_slot_novomatic {

    protected $_session;


    public function restore()
     {
                
         $ans = parent::restore();
         
         
         if($this->_session['freeCountAll']>0 && $this->_session['freeCountAll']==$this->_session['freeCountCurrent']) {
            $ans['last_win_sum']=$this->_session['total_win_free']*100;
         }
         $ans['bonus_win']=$this->_calc->freerun;
         
         //bad fix when 2 scatters
         if($ans['win']==0 && isset($ans['linesValue'][0]) && $ans['linesValue'][0]>0) {
             $ans['win']=$ans['linesValue'][0];
         }

        //bookofrad
//         $ans['bonus_win']=10;
         
         return $ans;

     }

     public function save_win() {
        $win = $this->_session['win'];
        if($this->_session['freeCountAll']>0 && $this->_session['freeCountAll']==$this->_session['freeCountCurrent']) {
            $win = $this->_session['total_win_free']*100;
        }
        $ans = parent::save_win();
        $ans['last_win_sum']=$win;
        $ans['session_win_free']=$this->_session['total_win_free'];
        return $ans;
     }
}
