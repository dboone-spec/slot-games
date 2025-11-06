<?php
class Vidget_Personoffice extends Vidget_List{


    function _search($vars){

        $list=array('alldata'=>__('All'))+$this->param['list'];
        return form::select($this->name,$list,$vars[$this->name],['class' => 'select2']);

    }



function _item($model){
    $can_edit=arr::get($this->param,'can_edit',true);

    $params=[];
    if(!$can_edit) {
        $params=['disabled'=>'disabled'];
    }

    $form=[];

    $ofs = $model->officesName(null,true);

    if($model->loaded()) {
        if(in_array($model->role,['client','gameman','report'])){

            foreach($this->param['list'] as $id=>$o) {
                $form[$id]='<label class="col-sm-6" style="border: 1px solid;font-weight: 500;padding: 2px;">';
                $form[$id].=$o.'&nbsp;&nbsp;&nbsp;';
                $form[$id].=form::checkbox($this->name($model).'[]',$id, isset($ofs[$id]));
                $form[$id].='</label>';
            }

            ksort($form);

            return implode('',$form);
        }
        return form::select($this->name($model),$this->param['list'],$model->__get($this->name),$params);
    }
    elseif(Person::$role=='client') {
        return form::select($this->name($model),$this->param['list'],$model->__get($this->name),$params);
    }

    return 'Save person first';


}

public function handler_save($data,$old_data,$model)
{

    if(!isset($data['office_id'])) {
        return $model;
    }

    if($data['role']=='cashier' && is_array($data['office_id'])) {
        return $model;
    }

    if($data['role']=='cashier' && !empty($model->office_id)) {
        return $model;
    }

    if($model->loaded() && is_array($data['office_id'])) {

        database::instance()->begin();

        db::query(Database::DELETE,'delete from person_offices where person_id=:person_id')
                ->param(':person_id', $model->id )
                ->execute();

        foreach($data['office_id'] as $o_id) {
            $sql='insert into person_offices (person_id,office_id)
                            values (:person_id,:office_id)';

            db::query(Database::INSERT,$sql)->param(':person_id', $model->id )
                                            ->param(':office_id', $o_id )
                                            ->execute();
        }

        database::instance()->commit();
        return $model;
    }


    return parent::handler_save($data,$old_data,$model);
}

}
