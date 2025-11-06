<?php

//виджет для оффисов. устанавливается время инкасации
class Vidget_HTimestamp extends Vidget_Timestamp {

    function handler_search($model, $vars) {

        $encashment_time = arr::get($this->param, 'encashment_time', 6);
        
        if (!(isset($vars[$this->name . '_start']) and isset($vars[$this->name . '_end']) && !empty($vars[$this->name . '_start']) and ! empty($vars[$this->name . '_end']))) {
            $vars[$this->name . '_start'] = date('Y-m-d', mktime($encashment_time, 0, 0));
            $vars[$this->name . '_end'] = date('Y-m-d', mktime($encashment_time - 1, 59, 59));
        }

        $start = $vars[$this->name . '_start'];
        $end = $vars[$this->name . '_end'];
        $start_time = strtotime($vars[$this->name . '_start']);
        $end_time = strtotime($vars[$this->name . '_end']);

        $start_time = mktime($encashment_time, 0, 0, date('n', $start_time), date('j', $start_time), date('Y', $start_time));
        $end_time = mktime($encashment_time - 1, 59, 59, date('n', $end_time), date('j', $end_time), date('Y', $end_time));

        $this->search_vars[$this->name . '_start'] = $start;
        $this->search_vars[$this->name . '_end'] = $end;
        return $model->where($this->name, '>=', $start_time)->where($this->name, '<=', $end_time);


        return $model;
    }

}
