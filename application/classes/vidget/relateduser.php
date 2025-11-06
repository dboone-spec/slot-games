<?php

class Vidget_Relateduser extends Vidget_Related
{
    function _search($vars){
        return form::input($this->name, $vars[$this->name]);
    }

    function _item($model){
        $class=get_class($model->__get($this->param['related']));
        $class=explode('_',$class);
        $m=ORM::factory($class[1]);
        $m->where('id', '=', $model->user_id)->find();
        
        $user_id = $m->parent_id??$m->id;

        $model = new Model_User($user_id);

        $value = 'id: ' . $model->__get('id') . '<br> name: ' . $model->__get($this->param['name']);
        
        return $value;
    }
    
    function handler_search($model,$vars){
        if (isset($vars[$this->name]) and $vars[$this->name] != ''){
            $val = trim($vars[$this->name]);
            $this->search_vars[$this->name]=$val;

            $res = DB::query(Database::SELECT, "select id from users where lower(name) like '%'||LOWER('{$val}')||'%'")->execute()->as_array();
            $ids = [0];
            foreach ($res as $user) {
                $ids[] = $user['id'];
            }
            return $model->where($model->object_name().'.user_id','in', $ids);
        }

        $this->search_vars[$this->name]='';
        return $model;
    }

}