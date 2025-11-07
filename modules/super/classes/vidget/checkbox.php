<?php

class Vidget_CheckBox extends Vidget{


function _list($model){

    if($model instanceof Model_Status AND $model->id != 'autopay') {
        return ' - ';
    }

    $s=false;
    if ($model->__get($this->name)>0){
            $s=true;
    }

    if($model instanceof Model_Jackpot) {
        $key = $model->primary_key();
        $url = str_replace('_', '', $model->object_name());
        $url = "/enter/$url/checkbox/{$model->$key}";
        $id = $this->name . '_' . $model->id;

        $js = <<<JS
            <script>
                $('#$id').click(function(event){
                    event.preventDefault();
                    event.stopPropagation();

                    var check_box = $(this);

                    var checked = check_box.prop('checked');

                    checked = checked==true?1:0;

                    $.ajax({
                        method: 'post',
                        url: '$url',
                        data: {
                            field: '{$this->name}',
                            checked: checked,
                        },
                        success: function(response) {
                            if(response.error==1) {
                                alert('Ошибка при сохранении');
                            } else {
                                check_box.prop('checked', checked);
                                alert('Изменение успешно сохранено');
                            }
                        }
                    });
                });
            </script>
JS;
        return form::checkbox($this->name($model),null,$s, ['id'=>$id]).$js;
    }


    return form::checkbox($this->name($model),null,$s,['disabled'=>'disabled']);
}

function _item($model){

	$s=false;
	if ($model->__get($this->name)>0){
		$s=true;
	}

	return form::checkbox($this->name($model),null,$s);

}

function _search($vars){
	$op=array('-1'=>__('All'), '0'=>__('No'), '1'=>__('Yes'));
	return form::select($this->name,$op,$vars[$this->name]);
}


function handler_save($data,$old_data,$model){
    $r= isset($data[$this->name]) ? 1 : 0;
    if($r==1) {
        $use_time = arr::get($this->param,'use_time',false);
        if($use_time) {
            $r=time();
        }
    }

    $model->set($this->name,$r);
    return $model;
}

function handler_search($model,$vars){

	if (isset($vars[$this->name]) and $vars[$this->name]>=0){
		$this->search_vars[$this->name]=$vars[$this->name];
		return $model->where($this->name,'=',$vars[$this->name]);
	}
	$this->search_vars[$this->name]='';
	return $model;
}





}