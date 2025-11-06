<?php

class Vidget_Integer extends Vidget_Input
{
    function handler_search($model,$vars){
        if (isset($vars[$this->name]) and !empty($vars[$this->name])){
            $this->search_vars[$this->name] = $vars[$this->name];
            if(false && $this->name == 'user_id' OR ($this->name=='id' AND $model instanceof Model_User)) {
                $u = new Model_User($vars[$this->name]);

                if ($u->loaded()) {
                    $ids = [];

                    $sql = <<<SQL
                            Select id
                            From users
                            Where
                                id = :id
                                OR
                                parent_id = :id

SQL;
                    $res=db::query(1, $sql)->param(':id', $u->id)->execute()->as_array();
                    foreach ($res as $v) {
                        $ids[] = $v['id'];
                    }

                    return $model->where($this->m_name . '.' . $this->name, 'in', $ids);
                }
            }
            return $model->where($this->model->object_name().'.'.$this->name, '=', (int)$vars[$this->name]);
        }
        $this->search_vars[$this->name]='';
        return $model;
    }

    function handler_save($data,$old_data,$model){
        if(isset($data[$this->name])) {
            $value = intval($data[$this->name]);
            $model->set($this->name, $value);
        }

        return $model;
    }
}
