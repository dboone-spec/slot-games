<?php

class vidget_sum extends Vidget_Echo
{

    public function _list($model)
    {
        $all = 0;
        foreach($this->param['all'] as $v)
        {
            if($model instanceof Model_Bet && (int) $model->__get('is_freespin')>0 && $v=='amount') {
                continue;
            }
            if(strpos($v, '-')===0) {
                $v = str_replace('-', '', $v);
                $all -= $model->__get($v);
            }
            else {
                $all += $model->__get($v);
            }
        }

		if(arr::get($this->param,'nomult',false)) {
            return th::number_format($all);
        }
		
        if(isset($model->office)) {
            $mult = $model->office->currency->mult??2;
        }
        else {
            $mult=$model->user->office->currency->mult??2;
        }

	if($mult==0) {
            return str_replace(' ','&nbsp;',th::number_format($all));
        }

        return str_replace(' ','&nbsp;',th::float_format($all,$mult));
    }

}
    
