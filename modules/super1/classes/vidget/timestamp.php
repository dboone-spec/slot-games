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


    if(PROJECT==1) {
        $js = '<script>
                $(window).ready(function() {
                    $(function() {
                        $(\'input[name="'.$this->name.'"]\').daterangepicker({
                            timePicker: true,
                            timePicker24Hour: true,
                            timePickerSeconds: true,
                            autoApply: true,
                            isCustomDate: function(date) {
                                console.log(date);
                            },
                            startDate: moment(\''.$vars[$this->name.'_start'].' '.$vars[$this->name.'_h_start'].':'.$vars[$this->name.'_m_start'].':'.$vars[$this->name.'_s_start'].'\'),
                            endDate: moment(\''.$vars[$this->name.'_end'].' '.$vars[$this->name.'_h_end'].':'.$vars[$this->name.'_m_end'].':'.$vars[$this->name.'_s_end'].'\'),
                            locale: {
                                format: \'M/DD HH:mm:ss\'
                            }
                        });
                        $(\'input[name="'.$this->name.'"]\').on(\'apply.daterangepicker\', function(ev, picker) {
                            $(\'input[name="'.$this->name.'_start'.'"]\').val(picker.startDate.format(\'YYYY-MM-DD\'));
                            $(\'input[name="'.$this->name.'_h_start'.'"]\').val(picker.startDate.format(\'HH\'));
                            $(\'input[name="'.$this->name.'_m_start'.'"]\').val(picker.startDate.format(\'mm\'));
                            $(\'input[name="'.$this->name.'_s_start'.'"]\').val(picker.startDate.format(\'ss\'));

                            $(\'input[name="'.$this->name.'_end'.'"]\').val(picker.endDate.format(\'YYYY-MM-DD\'));
                            $(\'input[name="'.$this->name.'_h_end'.'"]\').val(picker.endDate.format(\'HH\'));
                            $(\'input[name="'.$this->name.'_m_end'.'"]\').val(picker.endDate.format(\'mm\'));
                            $(\'input[name="'.$this->name.'_s_end'.'"]\').val(picker.endDate.format(\'ss\'));

                            $(this).val(picker.startDate.format(\'Y/M/DD HH:mm:ss\') + \' - \' + picker.endDate.format(\'Y/M/DD HH:mm:ss\'));
                        });
                    });
                });
                </script>';

        $option['hidden']='hidden';
        $option1['hidden']='hidden';

        $html = '';
        $html.=form::input($this->name.'_start',$vars[$this->name.'_start'],$option);
        $html.=form::input($this->name.'_h_start',$vars[$this->name.'_h_start'],$option);
        $html.=form::input($this->name.'_m_start',$vars[$this->name.'_m_start'],$option);
        $html.=form::input($this->name.'_s_start',$vars[$this->name.'_s_start'],$option);

        $html.=form::input($this->name.'_end',$vars[$this->name.'_end'],$option1);
        $html.=form::input($this->name.'_h_end',$vars[$this->name.'_h_end'],$option1);
        $html.=form::input($this->name.'_m_end',$vars[$this->name.'_m_end'],$option1);
        $html.=form::input($this->name.'_s_end',$vars[$this->name.'_s_end'],$option1);

        $html.='<input type="text" name="'.$this->name.'" id="'.'super_'.$this->element_id.'" class="form-control">'.$js;

        return $html;
    }

    $html='<div style="width:300px; padding-left:20px"> ';
    $html.= __('From').' '.form::input($this->name.'_start',$vars[$this->name.'_start'],$option).'&nbsp;';

	$html.=form::select($this->name.'_h_start',$hh, $vars[$this->name.'_h_start'],$option);
	$html.=form::select($this->name.'_m_start',$mm, $vars[$this->name.'_m_start'],$option);
	$html.=form::select($this->name.'_s_start',$mm, $vars[$this->name.'_s_start'],$option);

    $html.='<br /> '.__('To').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  '.form::input($this->name.'_end',$vars[$this->name.'_end'],$option1).'&nbsp;';

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

public $start_time;
public $end_time;

function handler_search($model, $vars) {

        $encashment_time = arr::get($this->param, 'encashment_time', 0);
        $zone_time = arr::get($this->param, 'zone_time', 0);
        $day_period = arr::get($this->param, 'day_period', 0);
		$default_day_period = arr::get($this->param, 'default_day_period', false);
		$default_minute_period = arr::get($this->param, 'default_minute_period', 0);

        if(empty($vars) && $default_day_period!==false) {
            $day_period=$default_day_period;
        }
		
		if($encashment_time===false) {
            $encashment_time=date('H');
        }

        if($default_minute_period>0) {
            $default_minute_period=date('i')-$default_minute_period;
        }
		
        $start_day = explode('-',arr::get($vars,$this->name . '_start',date('Y-m-d', mktime($encashment_time, 0, 0,date('m'),date('d')-$day_period,date('Y')))));
        $end_day = explode('-',arr::get($vars,$this->name . '_end',date('Y-m-d', mktime($encashment_time, 0, 0,date('m'),date('d')+1,date('Y')))));

        $created_h_start = arr::get($vars,'created_h_start',$encashment_time);
        $created_m_start = arr::get($vars,'created_m_start',$default_minute_period);
        $created_s_start = arr::get($vars,'created_s_start',0);

        $created_h_end = arr::get($vars,'created_h_end',$encashment_time);
        $created_m_end = arr::get($vars,'created_m_end',0);
        $created_s_end = arr::get($vars,'created_s_end',-1);

        $start_time = $start_time_m = mktime($created_h_start, $created_m_start, $created_s_start, $start_day[1], $start_day[2], $start_day[0]);
        $end_time = $end_time_m = mktime($created_h_end, $created_m_end, $created_s_end, $end_day[1], $end_day[2], $end_day[0]);

        $limit = mktime($encashment_time, 0, 0, date('m'), date('d')-$day_period, date('Y'));
        if ( ($this->param['limit']??100500) ===false )      {
            $limit=0;
        }
        
        if($day_period>0 && $start_time<$limit){
            $start_time = $start_time_m = $limit;
            $end_time = $end_time_m = $limit+($day_period*Date::DAY)+23*Date::HOUR+59*60+59;
        }
        $this->search_vars[$this->name . '_start'] = date('Y-m-d',$start_time);
        $this->search_vars[$this->name . '_h_start'] = date('H',$start_time);
        $this->search_vars[$this->name . '_m_start'] = date('i',$start_time);
        $this->search_vars[$this->name . '_s_start'] = date('s',$start_time);

        $this->search_vars[$this->name . '_end'] = date('Y-m-d',$end_time);
        $this->search_vars[$this->name . '_h_end'] = date('H',$end_time);
        $this->search_vars[$this->name . '_m_end'] = date('i',$end_time);
        $this->search_vars[$this->name . '_s_end'] = date('s',$end_time);

		$this->start_time=$start_time_m-($zone_time*3600);
        $this->end_time=$end_time_m-($zone_time*3600);

        return $model
            ->where($this->m_name.'.'.$this->name, '>=', $this->start_time)
            ->where($this->m_name.'.'.$this->name, '<=', $this->end_time);
    }


}








