<?php

class Vidget_Selectgames extends Vidget_Input
{

    function _item($model){

        $sql_games = 'select id,visible_name from games where true ';


        //Все вопросы к Дмитрию
        if( !( person::$role =='sa' || person::$user_id == 1023) ){
            $sql_games.='and category not in :nocat and show=1 and branded=0 ';
        }

        if(PROJECT==1) {
            $sql_games.='and brand=\'agt\' ';
        }
        $sql_games.=' order by 2';

        $all_games = db::query(1,$sql_games)
                ->param(':nocat',['coming'])
                ->execute();

        $form = [];

        $enabled_games = db::query(1,'select game_id,enable from office_games where office_id=:oid')
                ->param(':oid',$model->id)
                ->execute()
                ->as_array('game_id');

        $params=[];
        if(!($this->param['can_edit']??true)) {
            $params=['disabled'=>'disabled'];
        }

        foreach($all_games as $g) {

            if(isset($form[$g['visible_name']])) {
                throw new Exception('NOT UNIQUE GAME '.$g['visible_name']);
            }

            $form[$g['visible_name']]='<label class="col-sm-6" style="border: 1px solid;font-weight: 500;padding: 2px;">';
            $form[$g['visible_name']].=$g['visible_name'].'&nbsp;&nbsp;&nbsp;';
            $form[$g['visible_name']].=form::checkbox($this->name($model).'[]',$g['id'],!$model->loaded() || (isset($enabled_games[$g['id']]) && $enabled_games[$g['id']]['enable']==1),$params);
            $form[$g['visible_name']].='</label>';
        }

        ksort($form);

        return implode('',$form);
    }


    function handler_save($data,$old_data,$model){

        if(!arr::get($this->param,'can_edit',false)) {
            return $model;
        }

        if(!$model->loaded()){
            return $model;
        }

        $all_games = db::query(1,'select id,visible_name from games order by 2')->execute()->as_array('id');
        $office_games = db::query(1,'select * from office_games where office_id=:o_id')->param(':o_id',$model->id)->execute()->as_array('game_id');

        $games = arr::get($data,'games',[]);

        $old_games = [];
        foreach($office_games as $k=>$g) {
            if($g['enable']=='1' && isset($all_games[$k])) {
                $old_games[]=$all_games[$k]['visible_name'];
            }
        }
        $new_games = [];
        foreach($games as $g) {
            if(isset($all_games[$g])) {
                $new_games[]=$all_games[$g]['visible_name'];
            }
        }

        sort($old_games);
        sort($new_games);

        db::query(Database::UPDATE,'update office_games set enable=0 where office_id=:o_id')->param(':o_id',$model->id)->execute();

        $sql_i = 'insert into office_games(office_id, game_id, ENABLE) VALUES';

        foreach($games as $i=>$g) {
            $sql_i.='('.$model->id.','.$g.',1)';
            if($i<count($games)-1) {
                $sql_i.=',';
            }
        }

        $sql_i.=' on conflict(office_id,game_id) do update set enable=1';
        db::query(Database::INSERT,$sql_i)->execute('games');


        $action = new Model_Action;
        $action->type = 'update';
        $action->person_id = person::$user_id;
        $action->model_name = 'office';
        $action->model_id = $model->id;
        $action->old_data = json_encode($old_games);
        $action->new_data = json_encode($new_games);
        $action->save();

        return $model;
    }

    function _list($model) {
        $value = $model->__get($this->name);
        return HTML::chars(arr::get($this->param['fields'], $value, $value));
    }

    function _search($vars) {
        return form::select($this->name, $this->param['fields'],$vars[$this->name],array('class'=>"field text medium"));
    }
}
