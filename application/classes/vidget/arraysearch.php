<?php

class vidget_arraysearch extends Vidget_Input {

    function _search($vars) {
        return form::input($this->name, implode(',',$vars[$this->name]));
    }

    function handler_search($model, $vars) {
        if (isset($vars[$this->name]) and ! empty($vars[$this->name])) {
            $val = explode(',',$vars[$this->name]);
            array_map('trim', $val);
            $this->search_vars[$this->name] = $val;
            if ($this->name == 'id_list' AND $model instanceof Model_User) {
                    return $model->where($this->m_name . '.id', 'in', $val)
                            ->or_where('users.id', 'in', $val);
            }
            return $model->where($this->m_name . '.' . $this->name, 'in', $val);
        }
        $this->search_vars[$this->name] = [];
        return $model;
    }

}
