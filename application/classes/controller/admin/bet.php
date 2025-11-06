<?php

    class Controller_Admin_Bet extends Super
    {

        public $scripts = ['/js/compiled/main.4ecde5c.js'];
        public $mark = 'Bets'; //имя
        public $model_name = 'bet'; //имя модели
        public $canEdit=false;
        public $canCreate=false;
        public $canDelete=false;
        public $canItem=false;
        public $sh='super/normal';
        public $notSortable=['balance_before','balance_after'];


        public $day_period = [
            'client' => 30,
            'gameman' => 30,
            'sa' => 30,
        ];

        public function configure()
        {


            $this->search = [
                'id',
                'game',
                'created',
                'user_id',
//                'visible_name',
                'office_id',
//                'is_test',
            ];



            $this->list = [
                'id',
                'user_id',
                'office_id',
                'currency',
                'is_freespin',
                'amount',
                'win',
                'balance_before',
                'balance_after',
                'come',
                'game',
                'created',
                'result',
            ];

            if(Person::user()->role=='sa'){
                $this->list[]='method';
                $this->list[]='owner';
                $this->search[]='owner';

                usort($this->list,function ($a,$b) {
                    if($b=='owner') return 1;
                    return 0;
                });

                $this->vidgets['owner']=new Vidget_Ownerslist('office_id',$this->model);
                $this->vidgets['is_freespin']=new Vidget_Isfreespin('is_freespin',$this->model);
            }


            $this->order_by = ['created', 'desc'];

            $this->vidgets['result'] = new Vidget_SlotResult('result', $this->model);
            $this->vidgets['come'] = new Vidget_Betcome('come', $this->model);

            $id = new Vidget_Integer('user_id', $this->model);
            $this->vidgets['user_id'] = $id;

            $dv = new Vidget_Timestamp('created', $this->model);
            $dv->param('encashment_time', 0);
            $dv->param('zone_time',0);
            if ($day_period = arr::get($this->day_period, person::$role,3))
            {
                $dv->param('day_period', $day_period);
            }
            $this->vidgets['created'] = $dv;


            $sql='select g.name, g.visible_name
                    from games g
                    join office_games og on g.id=og.game_id
                    where og.office_id in :oid
                    order by g.name';

            if (Person::$role=='sa'){
                $sql='select g.name, g.visible_name
                        from games g
                        order by g.name';

            }

            $amount = new Vidget_Betamount('amount', $this->model);
            $this->vidgets['amount'] = $amount;

            $id = new Vidget_Sum('balance', $this->model);
            $id->param('all',['amount','balance','-win']);
            $this->vidgets['balance_before'] = $id;

            $id = new Vidget_Echo('balance', $this->model);
            $this->vidgets['balance_after'] = $id;

            $games=db::query(1,$sql)->param(':oid', Person::user()->offices())->execute()->as_array('name');

            $kg=[];

            foreach($games as $k => $g) {
                $kg[$k]=$games[$k]['visible_name'];
            }

            asort($kg);

            $this->vidgets['game']=new Vidget_List('game',$this->model);
            $this->vidgets['game']->param('list',$kg);

            $this->vidgets['office_id']=new Vidget_List('office_id',$this->model);
            $this->vidgets['office_id']->param('list',Person::user()->officesName(null,true));

//            $this->vidgets['visible_name']=new Vidget_Relatedtable('visible_name',$this->model);
//            $this->vidgets['visible_name']->param('related','user');
//            $this->vidgets['visible_name']->param('name','visible_name');
//            $this->vidgets['visible_name']->param('fkey','user_id');

            $office = new Vidget_Officecurrency('currency', $this->model);
            $this->vidgets['currency'] = $office;


        }

        public function handler_search($vars)
        {
            $model = parent::handler_search($vars);

            $model->join('pokerbets','LEFT')->on('pokerbets.id','=','bet.external_id')
                    ->unselect_fields(['amount'])
                    ->select(DB::expr('CASE WHEN "bet"."external_id" > 0 then "pokerbets"."amount" else "bet"."amount" end as amount'));

            if (Person::$role=='sa'){
                return $model;
            }

            return $model->where('bet.office_id','in',Person::user()->offices());
        }

    }
