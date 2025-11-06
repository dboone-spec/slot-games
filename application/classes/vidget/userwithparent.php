<?php

class Vidget_Userwithparent extends Vidget_Related
{
    function _search($vars){
        return form::input($this->name, $vars[$this->name]);
    }

    function _item($model){
        $class=get_class($model->__get($this->param['related']));
        $class=explode('_',$class);
        $m=ORM::factory($class[1]);
        $m->where('id', '=', $model->__get($this->name))->find();

        $user_id = $m->parent_id??$m->id;

        $model = new Model_User($user_id);

        $value = 'id: ' . $model->__get('id') . '<br> name: ' . $model->__get($this->param['name']);

        return $value;
    }
    function _list($model){
        $class=get_class($model->__get($this->param['related']));
        $class=explode('_',$class);
        $m=ORM::factory($class[1]);
        $m->where('id', '=', $model->__get($this->name))->find();

        $user_id = $m->parent_id??$m->id;

        $model = new Model_User($user_id);

        $value = $model->__get('id');

        return $value;
    }

    function handler_search($model,$vars){
        if (isset($vars[$this->name]) and $vars[$this->name] != ''){
            $val = intval($vars[$this->name]);
            $this->search_vars[$this->name]=$val;

            $res = DB::query(Database::SELECT, "select id,parent_id from users where id = :id OR parent_id = :id")->param(':id', $val)->execute()->as_array();
            $ids = [0];
            foreach ($res as $user) {
                $ids[] = $user['id'];
                $ids[] = $user['parent_id'];
            }
            return $model->where($this->name,'in', $ids);
        }

        $this->search_vars[$this->name]='';
        return $model;
    }

}