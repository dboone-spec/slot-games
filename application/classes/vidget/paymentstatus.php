<?php

//0 - новый
//10 - подтверждено для выплаты
//20 - начат процесс выплаты
//30 - выплачено
//40 - отменен

class Vidget_Paymentstatus extends Vidget_Echo
{
    protected $params_select = [
        0 => 'Новый',
        10 => 'Подтвержден',
        20 => 'В процессе выплаты',
        30 => 'Выплачен',
        40 => 'Отменен',
        'all' => 'Все'
    ];

    protected function _convertToText($model) {
		switch($model->__get($this->name)) {
			case 0;
				$status=__('Новый');
			break;
			case 10;
				$status=__('Подтвержден');
			break;
			case 20;
				$status=__('В процессе выплаты');
			break;
			case 30;
				$status=__('Выплачен');
			break;
			case 40;
				$status=__('Отменен');
			break;
			default:
				$status = '?';
			break;
		}
		return $status;
	}


	public function _item($model)
	{
        $text=$this->_convertToText($model);
        if($model->status==20 && $model->gateway=='interkassa') {
            $i = new interkassa();
            if($st = $i->getWithdrawList(['paymentNo'=>$model->id])) {
                $text .= "<br />".'interkassa status: '.$st[0]['stateName']??'?';
            }
        }
		return $text;

	}

	public function _list($model)
	{
        return $this->_convertToText($model);
	}

    public function _search($vars) {
        return form::select($this->name, $this->params_select, $vars[$this->name]);
    }

    public function handler_search($model, $vars) {
        if (isset($vars[$this->name]) and $vars[$this->name] != ''){
            $this->search_vars[$this->name]=$vars[$this->name];
            if($vars[$this->name] == 'all') {
                return $model;
            }
            return $model->where($this->m_name.'.'.$this->name,'=',$vars[$this->name]);
        }
        $this->search_vars[$this->name] = 30;
        return $model->where($this->m_name.'.'.$this->name, '=', 30);
    }
}
