<?php

class Vidget_Json extends Vidget_Echo
{

    private function print_json($a = [])
    {
        $s = '';

        if(is_array($a)) {

            foreach($a as $k => $v)
            {
                if(!is_array($v))
                {
                    $s .= $k . ': ' . $v . '<br />';
                }
                else
                {
                    $s .= $this->print_json($v);
                }
            }
        }
        return $s;
    }

    function _item($model)
    {

        $json = json_decode($model->__get($this->name),1);
        return $this->print_json($json);
    }

}
