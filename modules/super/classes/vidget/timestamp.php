<?php

class Vidget_Timestamp extends Vidget{


function _list($model){
    $zone_time = arr::get($this->param,'zone_time',3);
    return !is_null($model->__get($this->name)) ? date($format='d.m.y H:i:s',$model->__get($this->name)+($zone_time*3600)) : '';
}

function _item($model){
	$option=array('id'=>'super_'.$this->element_id);

	$js='<script>
			$(function(){
				$("#super_'.$this->element_id.'").datepicker({ dateFormat:"dd-mm-yy"});
			});
		</script>';

	$value=$model->loaded() ? $model->__get($this->name) : time();

	$html=form::input($this->name($model).'_date',  date('d-m-y',$value),$option);

	$hh=array();
	for($i=0;$i<=24;$i++){
		$k=$i;
		if ($k<10){
			$k='0'.$k;
		}
		$hh[$k]=$k;
	}

	$mm=array();
	for($i=0;$i<=60;$i++){
		$k=$i;
		if ($k<10){
			$k='0'.$k;
		}
		$mm[$k]=$k;
	}



	$html.=form::select($this->name($model).'_h',$hh, date('H',$value),$option);
	$html.=form::select($this->name($model).'_m',$mm, date('i',$value),$option);
	$html.=form::select($this->name($model).'_s',$mm, date('s',$value),$option);

	return $html.$js;
}

function handler_save($data,$old_data,$model){
	$d=explode('-',$data[$this->element_name.'_date']);
	$h=$data[$this->name($model).'_h'];
	$m=$data[$this->name($model).'_m'];
	$s=$data[$this->name($model).'_s'];

	$model->set($this->name,  mktime($h, $m, $s, $d[1], $d[0], $d[2]));
	return $model;
}


function _search($vars){
	$option=array('id'=>'super_'.$this->name.'_start');
	$option1=array('id'=>'super_'.$this->name.'_end');

    $hh=array();
	for($i=0;$i<=24;$i++){
		$k=$i;
		if ($k<10){
			$k='0'.$k;
		}
		$hh[$k]=$k;
	}

	$mm=array();
	for($i=0;$i<=60;$i++){
		$k=$i;
		if ($k<10){
			$k='0'.$k;
		}
		$mm[$k]=$k;
	}

        $html='<div style="width:300px; padding-left:20px"> ';
        $html.= 'From '.form::input($this->name.'_start',$vars[$this->name.'_start'],$option).'&nbsp;';

	$html.=form::select($this->name.'_h_start',$hh, $vars[$this->name.'_h_start'],$option);
	$html.=form::select($this->name.'_m_start',$mm, $vars[$this->name.'_m_start'],$option);
	$html.=form::select($this->name.'_s_start',$mm, $vars[$this->name.'_s_start'],$option);

    $html.='<br /> To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  '.form::input($this->name.'_end',$vars[$this->name.'_end'],$option1).'&nbsp;';

	$js='<script>
			$(function(){
				$("#super_'.$this->name.'_start").datepicker({ dateFormat:"yy-mm-dd"});
				$("#super_'.$this->name.'_end").datepicker({ dateFormat:"yy-mm-dd"});
			});
		</script>';


	$html.=form::select($this->name.'_h_end',$hh, $vars[$this->name.'_h_end'],$option1);
	$html.=form::select($this->name.'_m_end',$mm, $vars[$this->name.'_m_end'],$option1);
	$html.=form::select($this->name.'_s_end',$mm, $vars[$this->name.'_s_end'],$option1);
        $html.='</div>';
	return $html.$js;
}

function handler_search($model, $vars) {

        $encashment_time = arr::get($this->param, 'encashment_time', 0);
        $zone_time = arr::get($this->param, 'zone_time', 0);
        $day_period = arr::get($this->param, 'day_period', 0);

        $start_day = explode('-',arr::get($vars,$this->name . '_start',date('Y-m-d', mktime($encashment_time, 0, 0,date('m'),date('d')-$day_period,date('Y')))));
        $end_day = explode('-',arr::get($vars,$this->name . '_end',date('Y-m-d', mktime($encashment_time, 0, 0,date('m'),date('d')+1,date('Y')))));

        $created_h_start = arr::get($vars,'created_h_start',$encashment_time);
        $created_m_start = arr::get($vars,'created_m_start',0);
        $created_s_start = arr::get($vars,'created_s_start',0);

        $created_h_end = arr::get($vars,'created_h_end',$encashment_time);
        $created_m_end = arr::get($vars,'created_m_end',0);
        $created_s_end = arr::get($vars,'created_s_end',-1);

        $start_time = $start_time_m = mktime($created_h_start, $created_m_start, $created_s_start, $start_day[1], $start_day[2], $start_day[0]);
        $end_time = $end_time_m = mktime($created_h_end, $created_m_end, $created_s_end, $end_day[1], $end_day[2], $end_day[0]);

        $limit = mktime($encashment_time, 0, 0, date('m'), date('d')-$day_period, date('Y'));
        if($day_period>0 && $start_time<$limit){
            $start_time = $start_time_m = $limit;
        }
        $this->search_vars[$this->name . '_start'] = date('Y-m-d',$start_time);
        $this->search_vars[$this->name . '_h_start'] = date('H',$start_time);
        $this->search_vars[$this->name . '_m_start'] = date('i',$start_time);
        $this->search_vars[$this->name . '_s_start'] = date('s',$start_time);

        $this->search_vars[$this->name . '_end'] = date('Y-m-d',$end_time);
        $this->search_vars[$this->name . '_h_end'] = date('H',$end_time);
        $this->search_vars[$this->name . '_m_end'] = date('i',$end_time);
        $this->search_vars[$this->name . '_s_end'] = date('s',$end_time);

        return $model->where($this->m_name.'.'.$this->name, '>=', $start_time_m-($zone_time*3600))->where($this->m_name.'.'.$this->name, '<=', $end_time_m-($zone_time*3600));
    }


}








