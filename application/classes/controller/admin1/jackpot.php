<?php

class Controller_Admin1_Jackpot extends Super
{

    public $mark            = "Jackpots";
    /* Сортировка по умолчанию */
    public $model_name      = "jackpot";
    public $controller_name = "jackpot";
    public $sh='admin1/super';

    public function before()
    {
        if(arr::get($_GET,'s') == '1')
        {
            $this->request->redirect($this->dir . '/' . $this->controller_name);
        }
        parent::before();
    }

    public function action_delete()
    {
        $this->request->redirect($_SERVER['HTTP_REFERER']);
        exit;
    }

    public function action_item()
    {
        if(!$this->request->param('id'))
        {
            $strict=['type','min_value','max_value','active','min_bet','max_bet','procent'];
        }
        else {
            $this->vidgets['office_id']->param('can_edit',false);
            $strict=['min_value','max_value','min_bet','max_bet',];

            $this->show[]='values_range';
            $this->vidgets['values_range'] = new vidget_jprange('values_range',$this->model);
            $this->vidgets['values_range']->param('min','min_value');
            $this->vidgets['values_range']->param('max','max_value');

            $this->show[]='bet_range';
            $this->vidgets['bet_range'] = new vidget_jprange('bet_range',$this->model);
            $this->vidgets['bet_range']->param('min','min_bet');
            $this->vidgets['bet_range']->param('max','max_bet');


            if(Person::$role!='sa') {
                $this->vidgets['bet_range']->param('edit_min',false);
                $this->vidgets['bet_range']->param('edit_max',false);
            }
        }

        foreach($strict as $v) {
            unset($this->show[array_search($v,$this->show)]);
            unset($this->vidgets[$v]);
        }

        parent::action_item();
    }

    public function handler_search($vars)
    {
        $o_ids = array_keys(Person::user()->officesName(null));
        return parent::handler_search($vars)->where('office_id','in',$o_ids);
    }

    public function configure()
    {

        $this->order_by = [DB::expr('office_id,type')];

        $this->search = [
                'office_id'
        ];

        $this->list = [
                'office_id',
                'type',
                'active',
                'min_bet',
                'max_bet',
                'min_value',
                'max_value',
                'current',
                'hot_percent',
                'procent',
        ];

        $this->show = [
                'active',
                'type',
                'office_id',
                'min_value',
                'max_value',
                'min_bet',
                'max_bet',
                'hot_percent',
                'procent',
        ];

        $this->vidgets["hot_percent"] = new Vidget_jphotpercent('hot_percent',$this->model);

        if(Person::$role!='sa') {
            unset($this->show[array_search('procent',$this->show)]);
            unset($this->list[array_search('procent',$this->list)]);
            unset($this->show[array_search('hot_percent',$this->show)]);
            unset($this->list[array_search('hot_percent',$this->list)]);
        }

        $this->vidgets["type"] = new Vidget_List('type',$this->model);
        $this->vidgets["type"]->param('list',array(1 => 1,2 => 2,3 => 3,4 => 4));
        $this->vidgets['type']->param('can_edit',false);


        $this->vidgets['office_id'] = new Vidget_List('office_id',$this->model);
        $this->vidgets['office_id']->param('list',Person::user()->officesName(null,true));

        $this->vidgets['max_bet'] = new Vidget_Echo('max_bet',$this->model);

        $this->vidgets['active'] = new Vidget_CheckBox('active',$this->model);
    }

    public function handler_save($data)
    {

        $o_id = arr::get($data,'office_id');
        $o_ids = array_keys(Person::user()->officesName(null));
        if(!in_array($o_id,$o_ids)) {
            throw new HTTP_Exception_403;
        }

        if($this->model->loaded())
        {
            parent::handler_save($data);
        }
        else
        {
            $jpconfig  = Kohana::$config->load('jackpot');
            $office_id = $data['office_id'];

            $j=new Model_Jackpot(['office_id'=>$office_id]);

            if($j->loaded()) {
                return;
            }

            foreach($jpconfig as $jpcfg_type => $jpcfg)
            {
                $j            = new Model_Jackpot();
                $j->office_id = $office_id;
                $j->type      = $jpcfg_type;
                foreach($jpcfg as $f => $v)
                {
                    $j->$f = $v;
                }

                $j->value = $j->rand_value();

                $j->save();
                $j->clear();
            }
        }
    }

}
