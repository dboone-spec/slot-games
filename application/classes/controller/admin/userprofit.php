<?php

class Controller_Admin_Userprofit extends Super{



	public $mark='Бонусы ТП'; //имя
	public $model_name='user_profit'; //имя модели
	public $order_by=array('date', 'desc'); // сортировка
	public $controller='userprofit';
    public $scripts = ['/js/compiled/main.4ecde5c.js'];
    public $sh='userprofit';

	public function configure(){
		$this->search = ['date'];
        $this->list = [
            'date',
            'person_id',
            'amount',
            'profit'
        ];

        $dsrc = new Vidget_Related('person_id',$this->model);
		$dsrc->param('related','person');
		$dsrc->param('name','name');
		$this->vidgets['person_id'] = $dsrc;

        $date = new Vidget_Timestamp('date',$this->model);
		$this->vidgets['date'] = $date;
	}

    public function action_index(){
        $date_start = arr::get($_GET, 'date_start');
        $date_end = arr::get($_GET, 'date_end');

        $date_start = $date_start != '' ? $date_start : null;
        $date_end = $date_end != '' ? $date_end : null;

        /*
         * для формирования "Итого" в статистике
         */
        $total_row = [];
        if($date_start AND $date_end) {
            $rows_for_total = $this->handler_search($_GET)->find_all();
        }

		//основные данные
		$view=new View($this->sh.'/index');

		if (is_array($this->order_by)){
            if(is_array($this->order_by[0])) {
                foreach ($this->order_by[0] as $field) {
                    $this->model->order_by($field, isset($this->order_by[1]) ? $this->order_by[1] : null);
                }
            } else {
                $this->model->order_by($this->order_by[0],isset($this->order_by[1]) ? $this->order_by[1] : null);
            }
		}

        $result = $this->handler_search($_GET)->find_all();

        /*
         * для формирования "Итого" в статистике
         */
        if(!$date_start OR !$date_end) {
            $rows_for_total = $result;
        }

        foreach ($rows_for_total as $row) {
            $person_id = $row->person_id;
            $p = new Model_Person($person_id);
            $person = $p->name;

            if(!isset($total_row[$person])) {
                $total_row[$person] = ['amount'=>0, 'profit'=>0, 'person_id'=>$person];
            }

            $total_row[$person]['amount'] += $row->amount;
            $total_row[$person]['profit'] += $row->profit;
        }

		$view->data=$result;
        $view->total_row = $total_row;
		$view->list=$this->list;
		$view->search=$this->search;
		$view->search_vars=$this->search_vars;
		$view->label=$this->model->labels();
		$view->model=$this->controller;
		$view->mark=$this->mark;
		$view->dir=$this->dir;
		$view->vidgets=$this->vidgets;
		$view->actions=$this->actions;

		if ($this->request->is_initial()){
			$this->template->content=$view->render();
		}
		else{
			$this->response->body($view->render());
		}
	}

    public function handler_search($vars) {
        $model=$this->model;
		$this->search_vars=array();
		foreach ($this->search as $name){
            if($name == 'date') {
                $vars['date_start'] = arr::get($vars, 'date_start', date('Y-m-d', mktime(0, 0, 0, date('n'), date('d')-6)));
                $vars['date_end'] = arr::get($vars, 'date_end', date('Y-m-d'));
            }
			$model=$this->vidgets[$name]->handler_search($model,$vars);
			$this->search_vars=$this->search_vars+$this->vidgets[$name]->search_vars;
		}

		return $model;
    }

}
