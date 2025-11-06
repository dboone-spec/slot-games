<?php

class Controller_Admin_Newsletter extends Super{


	public $mark='Письма'; //имя
	public $model_name='newsletter'; //имя модели
        public $scripts = ['/js/compiled/main.4ecde5c.js'];
        public $order_by = array('need_to_send', 'desc'); // сортировка


	public function configure(){

            $this->search = [
                    'to',
                    'title',
                    'msrc',
            ];


            $this->list = [
                    'to',
                    'title',
                    'msrc',
                    'need_to_send',
                    'sended',
                    'opened',
                    'message',
            ];

            $this->show = [
            ];
            
            $timestamps = [
                'need_to_send',
                'sended',
                'opened'
            ];

            foreach ($timestamps as $field) {
                $this->vidgets[$field] = new Vidget_Timestampecho($field, $this->model);
            }
            
            $this->vidgets['message'] = new Vidget_HTMLrender('message',$this->model);
            
            $this->order_by = [DB::expr('need_to_send desc, sended desc')];
	}
}
