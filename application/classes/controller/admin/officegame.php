<?php

class Controller_Admin_Officegame extends Super
{

    public $mark       = 'Игры (ППС)'; //имя
    public $model_name = 'officegame'; //имя модели
    public $sh         = 'admin/officegame'; //шаблон
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function action_input()
    {
        parent::action_input();

        $id           = arr::get($_POST,'og_id');
        $model        = ORM::factory($this->model_name,$id);
        $ans['error'] = 2;

        if($model->loaded())
        {
            $office_id = $model->office_id;
            $game      = $model->game->name;

            $sql = 'insert into counters_history ("date","in","out",bonus,game,bettype,provider,double_in,double_out,office_id,"free","type")
        SELECT extract(\'epoch\' from CURRENT_TIMESTAMP) as "date","in","out",bonus,game,bettype,provider,double_in,double_out,office_id,"free","type" from counters_games
        where office_id = :office_id and game=:game';

            db::query(Database::INSERT,$sql)
                    ->param(':game',$game)
                    ->param(':office_id',$office_id)
                    ->execute();

            $sql_c  = <<<SQL
                delete from counters where game=:game and office_id=:office_id
SQL;
            db::query(4,$sql_c)->parameters([
                    ':office_id' => $office_id,
                    ':game'      => $game,
            ])->execute();
            $sql_cg = <<<SQL
                delete from counters_games where game=:game and office_id=:office_id
SQL;
            db::query(4,$sql_cg)->parameters([
                    ':office_id' => $office_id,
                    ':game'      => $game,
            ])->execute();

            $ans['error'] = 0;
        }
        $this->response->body(json_encode($ans));
    }

    public function configure()
    {
        $this->search = [
            'provider',
            'office_id',
            'game_id',
        ];

        $this->list = [
            'id',
            'office_id',
            'enable',
            'game_id',
            'brand',
            'z',
        ];

        $this->show = [
            'office_id',
            'enable',
            'game_id',
            'brand',
        ];

        $this->vidgets['enable'] = new Vidget_CheckBox('enable',$this->model);
        $this->vidgets['z']      = new Vidget_Returncoef('z',$this->model);
        $this->vidgets['provider']      = new Vidget_Provider('provider',$this->model);

        $br = new Vidget_Related('game_id',$this->model);
		$br->param('related','game');
		$br->param('name','brand');

        $this->vidgets['brand']      = $br;

        $this->vidgets['office_id']      = new Vidget_Select('office_id',$this->model);
        $offices = $this->offices();
        ksort($offices);
        $this->vidgets['office_id']->param('fields',$offices);


        $game                     = new Vidget_Related('game_id',$this->model);
        $game->param('related','game');
        $game->param('name','visible_name');
        $this->vidgets['game_id'] = $game;

        if(person::$role=='gameman') {
            unset($this->search[array_search('provider',$this->search)]);
            unset($this->list[array_search('provider',$this->list)]);
            unset($this->vidgets['provider']);
        }
    }

    public function handler_search($vars){
        $model = parent::handler_search($vars);

        $model->join('games')->on('games.id', '=', 'officegame.game_id');

        return $model->where('office_id','in', $this->offices());
	}
}
