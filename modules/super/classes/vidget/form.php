<?php
/* параметры
 *	path - путь до контроллера с присоединяемыми элементами
 *  model - название прицепляемой модели
 *  list - список выводимых полей
 */

class Vidget_Form extends Vidget {
	
	function _list($model){
		return "cann't list in form vidget";
	}

	function _item($model){
		
		if ($this->many){
			return get_class($this).' Not support listedit';
		}

		if (!$model->loaded()){
			return 'Для добавления связанных позиций сохраните объект';
		}
		
		$str='<div id="this_'.$this->name.'_div">';
		$num=0;
		$form_model=ORM::factory($this->param['model'])->where($this->m_name.'_id','=',$model->pk());
		$path=arr::get($this->param,'path');

		foreach($form_model->find_all() as $m){
			$str.='<div>';
			$query=array('__super_id'=>$this->name, '__num'=>$num);
			$item=Request::factory($path.'/item/'.$m->pk())
														->method(HTTP_Request::GET)
														->query($query)
														->execute()
														->body();
			
			$str.=$item;
			$str.='</div>';
			$num++;
		}
		$str.='</div>';
		
		//new element
		$str.='<div id="'.$this->name.'_newhtml" style="display:none" >';
		$query=array('__super_id'=>$this->name, '__num'=>'%%num%%');
		$item=Request::factory($path.'/item/')
													->method(HTTP_Request::GET)
													->query($query)
													->execute()
													->body();

		$str.=$item;
		$str.='</div>';

		
		$js=<<<ACC
<input type="button" id="button_new_element_{$this->name}" value="Добавить"> <br><br><br>
<script>
$(document).ready(function(){
	var num_{$this->name}={$num};
	$('#button_new_element_{$this->name}').click(function(){
		num_{$this->name}++;
		var html;
		html=$('#{$this->name}_newhtml').html();
		html=html.replace(/%%num%%/g,num_{$this->name});
		html='<div id="asdasdasd">'+html+'</div>';
		$('#this_{$this->name}_div').append(html);
		
		});
});
</script>
		
ACC
;
		return $str.$js;
		
		
	}	

	function _search($vars){

		return "cann't find in form vidget";
	}	

	
	
	function handler_save($data,$old_data,$model){
		
		$data=arr::get($_POST,$this->name,array());
		$path=arr::get($this->param,'path');
		
		foreach ($data as $key=>$post){
			//помним о скрытом %%num%%
			if (!is_numeric($key)){
				continue;
			}
				
			$item=Request::factory($path.'/item/')
													->method(HTTP_Request::POST)
													->post($post)
													->execute();
													
			
		}
		
		return $model;
	}

	
	
}