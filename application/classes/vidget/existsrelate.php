<?php

class Vidget_Existsrelate extends Vidget_CheckBox
{

    public function _list($model)
    {
        return (int) ($model->__get($this->param['related'])->count_all() > 0);
    }

    function handler_search($model,$vars)
    {
        if(isset($vars[$this->name]) and $vars[$this->name] != '')
        {
            $this->search_vars[$this->name] = $vars[$this->name];

            $p=$vars[$this->name]==0?'=':'>=';


            $model = $model
                    ->join('office_games')->on('office_games.game_id','=','game.id')
//                    ->__get($this->param['related'])
                    ->select_fields(['office_games.office_id'=>'office_games.office_id'])
                    ->group_by('office_games.office_id')
                    ->having_open()
                    ->having(DB::expr('COUNT(office_games.id)'),$p,$vars[$this->name])
                    ->having_close();

            return $model;

            return $model->__get($this->param['related'])->select_fields(['office_id'=>'office_id','game_id'=>'game_id'])->group_by('office_game.office_id')->having_open()->having(DB::expr('COUNT(office_game.id)'),$p,$vars[$this->name])->having_close();
        }
        $this->search_vars[$this->name] = '';
        return $model;
    }

}
