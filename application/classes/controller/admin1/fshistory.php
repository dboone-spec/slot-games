<?php

class Controller_Admin1_Fshistory extends Super
{

    public $mark       = 'Freespins history'; //имя
    public $model_name = 'freespinhistory'; //имя модели
    public $canEdit    = false;
    public $canCreate  = false;
    public $canDelete  = false;
    public $canItem    = false;
    public $sh         = 'admin1/super';
    public $controller = 'fshistory'; //имя модели
    public $order_by   = array('created','desc'); // сортировка
    public $scripts    = ['/js/compiled/main.4ecde5c.js'];
	
	public $day_period = [
            'client' => 30,
            'gameman' => 30,
            'sa' => 30,
        ];

    public function configure()
    {
        $this->search = [
                "user_id",
                "external_id",
				"src",
                "office_id",
                'created',
        ];

        $this->list = [
                'user_id',
                'office_id',
                'active',
		'src',
                'fs_count',
                'fs_played',
                'game_id',
                'amount',
                'lines',
                'created',
		'expirtime',
        ];

        $uv                       = new Vidget_Related('game_id',$this->model);
        $uv->param('related','game');
        $uv->param('name','visible_name');
        $this->vidgets['game_id'] = $uv;

        $this->vidgets['active']  = new Vidget_Integer('active',$this->model);
        $this->vidgets['user_id']  = new Vidget_Integer('user_id',$this->model);
        $this->vidgets['payed']   = new Vidget_CheckBox('payed',$this->model);
        $this->vidgets['created'] = new Vidget_Timestamp('created',$this->model);
        $this->vidgets['created']->param('encashment_time',0);
        $this->vidgets['created']->param('zone_time',0);
		
		if(Person::user()->role=='sa'){
            $this->list[]='owner';
            $this->search[]='owner';

            usort($this->list,function ($a,$b) {
                if($b=='owner') return 1;
                return 0;
            });

            $this->vidgets['owner']=new Vidget_Ownerslist('office_id',$this->model);
        }

	$this->vidgets['expirtime'] = new Vidget_Timestamp('expirtime',$this->model);
        $this->vidgets['expirtime']->param('encashment_time',0);
        $this->vidgets['expirtime']->param('zone_time',0);

        $this->vidgets['office_id']=new Vidget_List('office_id',$this->model);
        $this->vidgets['office_id']->param('list',Person::user()->officesName(null,true));

        $this->vidgets['external_id']=new Vidget_Relatedtable('external_id',$this->model);
        $this->vidgets['external_id']->param('related','user');
        $this->vidgets['external_id']->param('name','external_id');
        $this->vidgets['external_id']->param('fkey','user_id');

        $this->vidgets['active'] = new Vidget_fsstatus('active', $this->model);

    }

    public function handler_search($vars)
    {
        $model = parent::handler_search($vars);
        if(Person::$role == 'sa')
        {
            return $model;
        }

        return $model->where('office_id','in',Person::user()->offices());
    }

}
