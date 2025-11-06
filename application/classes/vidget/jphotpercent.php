<?php

class vidget_jphotpercent extends Vidget_Input {


    function handler_save($data,$old_data,$model){
        $redis = dbredis::instance();
        $redis->select(1);

        $redis->set('jpHotPercent-'.$model->office_id.'-'.($model->type-1),(float) $data[$this->name]);

        return parent::handler_save($data,$old_data,$model);
    }

}
