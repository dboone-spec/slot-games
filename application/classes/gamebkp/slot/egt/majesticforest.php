<?php

//логика игры

class game_slot_egt_majesticforest extends game_slot_egt
{


    public function spin($lidx = -1, $bidx = -1, $didx = -1) {
        $ans = parent::spin($lidx, $bidx, $didx);
        $b = $this->_session['extra_param']??-1;
        $ans['complex']['freeSpinsExpandSymbol']=$b;
        return $ans;
    }

    public function restore()
    {
        $ans = parent::restore();
        return $ans;
    }

    public function bonus_game() {

        $ans = parent::bonus_game();

        $mask = $this->_calc->bonus_win>0?$this->_calc->bonus_mask($this->_session['extra_param'],array_values($this->_calc->bonusdata)):0;

        $ans['bonus_super_symbol_win'] = [];

        $ans['bonus_super_symbol_win']['win']=0;
        $ans['bonus_super_symbol_win']['mask']=0;
        $ans['bonus_super_symbol_win']['linesMask']=array_fill(0,9,0);
        $ans['bonus_super_symbol_win']['linesValue']=array_fill(0,9,0);

        if($this->_calc->bonus_win>0) {
            $ans['bonus_super_symbol_win']['win']=$this->_calc->bonus_win;
            $ans['bonus_super_symbol_win']['mask']=$mask;
            $ans['bonus_super_symbol_win']['linesMask']=array_values($this->_calc->bonus_win_mask);
            $ans['bonus_super_symbol_win']['linesValue']=array_values($this->_calc->bonus_win_line);
            $ans['extra_param']=$this->_session['extra_param'];
        }

        if($this->_session['freeCountAll']>0 && $this->_session['freeCountAll']==$this->_session['freeCountCurrent']) {
            $ans['last_win_sum']=$this->_session['total_win_free'];
        }

        $ans['session_total_win_free'] = $this->_session['total_win_free'];


        return $ans;
    }
}