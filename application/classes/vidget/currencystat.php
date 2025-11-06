<?php

class Vidget_Currencystat extends Vidget_Input
{
    private function balances($model) {
        $users = orm::factory('user')->where('parent_id', '=', $model->id)->find_all();

        $fields = arr::get($this->param, 'fields', []);
        $labels = $model->labels();

        $html = '';

        if(!count($users)) {
            $users = [$model];
        }

        foreach ($users as $u) {
            if($this->name == 'sum_diff') {
                $diff = th::number_format($u->sum_in - $u->sum_out);
                if($diff==0) {
                    $html = "<span>0</span>";
                    continue;
                }
                $html .= "<span>" . $diff . "&nbsp;{$u->office->currency->code}</span>";
            } else {
                if(is_array($fields)) {
                    $html .= "<span>{$u->office->currency->code} - ";

                    foreach ($fields as $i => $field) {
                        $v = th::number_format($u->$field);
                        $html .= "{$v}";
                        if($i<count($fields)-1) {
                            $html.='/';
                        }
                    }
                    $html .= "</span><br>";
                } else {
                    if($u->$fields<=0) {
                        $html = "<span class=\"calcable\" name=\"{$fields}\">0</span>";
                        continue;
                    }
                    $v = th::number_format($u->$fields);
                    $html .= "<span class=\"calcable\" name=\"{$fields}\">{$v}&nbsp;{$u->office->currency->code}</span>";
                }
            }
        }

        return $html;
    }

    public function _item($model) {
        return $this->balances($model);
    }

    public function _list($model) {
        return $this->balances($model);
    }

    function handler_search($model,$vars){
        return $model;
    }

    function handler_save($data,$old_data,$model){
        return $model;
    }
}
