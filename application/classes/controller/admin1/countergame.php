<?php

class Controller_Admin_Countergame extends Super
{

    public $mark       = 'Счетчики'; //имя
    public $model_name = 'counter_game'; //имя модели
    public $sh         = 'admin/countergame'; //шаблон
    public $controller = 'countergame';
    public $per_page   = 100;
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function configure()
    {
        $this->list = [
                'game'             => 'game',
                'provider'          => 'provider',
                'type'              => 'type',
                'Normal' => [
                        'in',
                        'out',
                        'percent_normal',
                ],
                'Bonus' => [
                        'bonus',
                        'percent_bonus'
                ],
                'Free'  => [
                        'free',
                        'percent_free',
                ],
                'Double' => [
                        'double_in',
                        'double_out',
                        'percent_double',
                ],
                'Сбросить'=>'clear',
        ];


        $this->search = [
                'office_id',
                'game',
                'type',
                'provider',
        ];

        $this->vidgets['out'] = new vidget_sum('out', $this->model);
        $this->vidgets['out']->param('all',['out','bonus','free']);
        
        $ai                             = new Vidget_Persent('in',$this->model);
        $ai->param('all',['bonus']);
        $this->vidgets['percent_bonus'] = $ai;

        $ai                            = new Vidget_Persent('in',$this->model);
        $ai->param('all',['free']);
        $this->vidgets['percent_free'] = $ai;

        $ai                              = new Vidget_Persent('in',$this->model);
        $ai->param('all',['out','bonus','free']);
        $ai->param('reverse',true);
        $this->vidgets['percent_normal'] = $ai;

        $ai                              = new Vidget_Persent('double_in',$this->model);
        $ai->param('all',['double_out']);
        $this->vidgets['percent_double'] = $ai;

        $this->vidgets['office_id']      = new Vidget_Select('office_id',$this->model);
        $this->vidgets['office_id']->param('fields',$this->offices());

        
        $type     = new Vidget_Select('type',$this->model);
        $r        = DB::select('type')
                ->from('counters_games')
                ->distinct('type')
                ->execute()
                ->as_array();
        $param[0] = 'Все';
        foreach($r as $v)
        {
            $param[$v['type']] = $v['type'];
        }
        $type->param('fields',$param);
        $this->vidgets['type'] = $type;

        $provider     = new Vidget_Select('provider',$this->model);
        $res        = DB::select('provider')
                ->from('counters_games')
                ->distinct('provider')
                ->execute()
                ->as_array();
        $p[0] = 'Все';
        foreach($res as $v)
        {
            if(!empty($v['provider'])){
                $p[$v['provider']] = $v['provider'];
            }
        }
        $provider->param('fields',$p);
        $this->vidgets['provider'] = $provider;

        $clear = new Vidget_ClearCounter('id',$this->model);
        $this->vidgets['clear'] = $clear;

    }

    public function handler_search($vars) {
        $model = parent::handler_search($vars);
        return $model->where('office_id','in',$this->offices());
    }

    public function action_clear() {
        $this->auto_render=false;

        $id = $this->request->param('id');

        $provider=['our','imperium'];

        if($id=='our') {
            unset($provider[1]);
        }
        elseif($id=='imperium') {
            unset($provider[0]);
        }
        
        //TODO добавить ППС в будущем

            $sql = 'insert into counters_history ("date","in","out",bonus,game,bettype,provider,double_in,double_out,office_id,"free","type")
        SELECT extract(\'epoch\' from CURRENT_TIMESTAMP) as "date","in","out",bonus,game,bettype,provider,double_in,double_out,office_id,"free","type" from counters_games where provider in :provider';

            if((int) $id > 0) {
                $sql.=' and id=:id';
            }

            $sql.=' and office_id in :office_id';
            
            db::query(Database::INSERT,$sql)
                    ->param(':provider',$provider)
                    ->param(':id',$id)
                    ->param(':office_id',$this->offices())
                    ->execute();
            
            $sql = 'delete from counters_games where provider in :provider';
            if((int) $id > 0) {
                $sql.=' and id=:id';
            }
            
            $sql.=' and office_id in :office_id';
			
            db::query(Database::DELETE,$sql)
                ->param(':provider',$provider)
                ->param(':id',$id)
                ->param(':office_id',$this->offices())
                ->execute();


        $this->request->redirect('/'.ADMINR.'/countergame');
    }

}
