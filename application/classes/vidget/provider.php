<?php

class Vidget_Provider extends Vidget_Returncoef
{

  
    function _search($vars)
    {        
        return form::select($this->name,['our'=>'Наши','imperium'=>'imperium','all'=>'Все'],$vars[$this->name]??'all');
    }

    function handler_search($model, $vars) {
        if (isset($vars[$this->name]) and ! empty($vars[$this->name])) {
            $this->search_vars[$this->name] = $vars[$this->name];
            
            if($vars[$this->name] == 'all') {
                return $model;
            } else {
                return $model->where($this->name, '=', $vars[$this->name]);
            }
        }
        $this->search_vars[$this->name] = 'our';
        
        return $model->where($this->name, '=', 'our');
    }

}
