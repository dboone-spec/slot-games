<?php

class Vidget_Betcome extends Vidget_Echo
{

    protected function _roulettecome($model)
    {
        $r = json_decode($model->__get($this->name),1);
        if(!$r)
        {
            return '';
        }

        $conf = (array) kohana::$config->load($model->__get('game_type').'/'.$model->__get('game').'.betnum');

        $s = '<div style="width: 300px; overflow-x: scroll; overflow-y: hidden;">';

        if(!is_array($r)) {
            return $model->__get($this->name);
        }

        foreach($r as $c => $b)
        {
            $a = $conf[$c];
            if(!is_array($a)) {
                $a=[$a];
            }
            foreach($a as $n) {
                $color='green';
                $textcolor='#fff';
                if(in_array($n,$conf[149])) {
                    $textcolor='#000';
                    $color='red';
                }
                else if(in_array($n,$conf[150])) {
                    $color='black';
                }
                $s.='<span style="padding: 1%; background: '.$color.'; color: '.$textcolor.'">'.$n.'</span>';
            }
            $s.=':&nbsp;';
            $s.=th::number_format($b).'&nbsp;&nbsp;';
        }
        $s.='</div>';
        return $s;
    }

    function _list($model)
    {
        if($model->__get('game') == 'virtualroulette')
        {
            return $this->_roulettecome($model);
        }

        if($model->__get('type')=='double') {
            if(is_numeric($model->__get('come'))) {
                $suits = [0=>'♥',1=>'♦',2=>'♠',3=>'♣'];
                return $suits[$model->__get('come')];
            }
        }

        if(in_array($model->__get('game'),['roshambo','spinners'])) {
            return ((int) $model->__get('come')+1).'&nbsp;hand';
        }

        if($model->__get('game')=='sapper') {
            return ($model->__get('come')+1).'&nbsp;btn';
        }

        return parent::_list($model);
    }

    function _item($model)
    {
        if($model->__get('game') == 'virtualroulette')
        {
            return $this->_roulettecome($model);
        }
        return parent::_item($model);
    }

}
