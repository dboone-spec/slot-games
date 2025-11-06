<?php

class Controller_Admin1_Events extends Controller_Admin1_Base{

    public $mark       = 'Events'; //имя
    public $model_name = 'event'; //имя модели
    public $sh         = 'admin1/event'; //шаблон
    public $controller = 'event';

    protected $days_of_week=[
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        '5,6,0'=>'Every Friday,Saturday,Sunday',
    ];

    protected $_types=[
        'dayweek'=>'dayweek',
        'progressive'=>'Lucky Spins',
        'promo'=>'100% CASHBАСK',
    ];

    //фриспины по умолчанию по дням. первый день-Воскресенье!!
    protected $_progressive_map=[
        5,
        7,
        10,
        12,
        15,
        17,
        20,
    ];

    protected $_partners=[
        '1win.prod'=>'1win'
    ];

    //todo если нужно, можно добавить в games поле branded_by и использовать его для связки казино
    protected $_branded_games=[
        '1win.prod'=>['1windoublehot','1win']
    ];

    public function action_index(){

        $limit=20;

        $page     = $this->request->param('id',1);
        $page     = max(1,$page);
        $offset   = $limit * ($page - 1);

        $current_office=arr::get($_GET,'office_id',-1);
        $offices = $alloffices = Person::user()->officesName(null,true);

        if($current_office>0 && isset($offices[$current_office])) {
            $offices=[$current_office=>$alloffices[$current_office]];
        }

        $partners = [-1=>'all']+Person::user()->uniquePartners($offices);

        $view = new View('admin1/events/index');

        $page_data = array
        (
            'total_items'    => orm::factory('event')
                ->where('office_id','in',array_keys($offices))
                ->or_where('partner','in',$partners)
                ->count_all(),
            'items_per_page'  => $limit,
            'current_page'     => array
            (
                'source'     => 'route',
                'key'         => 'id'
            ),
            'auto_hide'         => TRUE,
        );

        $view->events = orm::factory('event')
            ->where('office_id','in',array_keys($offices))
            ->or_where('partner','in',$partners)
            ->order_by('id')
            ->limit($limit)
            ->offset($offset)
            ->find_all();

        $errors=[];
        $message='';

        $view->page=Pagination::factory($page_data)->render('pagination/floating');
        $view->message = $message;
        $view->errors = $errors;
        $view->current_office = $current_office;
        $view->offices = [-1=>'All']+$alloffices;
        $view->dir = $this->dir;

        $view->daysofweek = $this->days_of_week;

        $this->template->content = $view;
	}

    public function action_item(){
        $id=$this->request->param('id');

        $new_created=arr::get($_GET,'s',false);

        $view=new View('admin1/events/item');

        $errors=[];
        $message='';

        $offices = Person::user()->officesName(null,true);
//        $partners = Person::user()->uniquePartners($offices);

        $partners=$this->_partners;

        $event=new Model_Event($id);

        if($id && !$event->loaded()) {
            throw new HTTP_Exception_404();
        }

        if($id && $event->calc==1) {
            $this->request->redirect('/enter/events');
        }

        if($this->request->method()=='POST') {

            if(!$event->loaded()) {
                $event->type=$this->_types[arr::get($_POST,'type','dayweek')];
            }

            $games_ids=array_keys(arr::get($_POST,'games_ids',[]));

            $event->partner=arr::get($_POST,'partner');

            if(!$id && $event->type=='promo') {
                $sql='select  g.id, g.visible_name
                    from games g
                    where
                    g.show=1 and g.brand=\'agt\' and g.type!=\'videopoker\' ';

                $sql.=' and g.branded=0 ';

                $branded_games=[];

                if(!empty($event->partner)) {
                    $sql.=' or (g.branded=1 and g.name in :branded_games)';
                    $branded_games=$this->_branded_games[$event->partner];
                }

                $sql.=' order by g.visible_name ';

                $games_ids=array_keys(db::query(1, $sql)
                    ->param(':branded_games',$branded_games)
                    ->execute()->as_array('id'));
            }

            $time=arr::get($_POST,'time','00:00');

            list($event->h,$event->m)=explode(':',$time);

            $event->dom=arr::get($_POST,'dom',-1);
            $event->mon=arr::get($_POST,'mon',-1);

            $event->dow=arr::get($_POST,'dow',-1);
            $event->starts=strtotime(arr::get($_POST,'starts'));
            $event->ends=strtotime(arr::get($_POST,'ends'));
            $event->games_ids=$games_ids;
            $event->active=(int) arr::get($_POST,'active');
            $event->once=(int) arr::get($_POST,'once',0);
            $event->fs_amount=arr::get($_POST,'fs_amount',0);
            $event->fs_count=arr::get($_POST,'fs_count',0);
            $event->extra_params=arr::get($_POST,'progressive_map',[]);
            $event->max_payout=arr::get($_POST,'max_payout');
            if($event->type=='promo' && empty($event->max_payout)) {
                $errors[]='Set the MAX PAYOUT';
            }

            $duration=arr::get($_POST,'duration','23:59');

            list($duration_h,$duration_m)=explode(':',$duration);

            $event->duration=$duration_h*Date::HOUR+$duration_m*Date::MINUTE;

            $time_to_collect=arr::get($_POST,'time_to_collect','00:00');

            list($time_to_collect_h,$time_to_collect_m)=explode(':',$time_to_collect);

            $event->time_to_collect=$time_to_collect_h*Date::HOUR+$time_to_collect_m*Date::MINUTE;

            if(!$id) {
                $event->office_id=arr::get($_POST,'office_id',0);

                if($event->office_id<=0 && empty($event->partner)) {
                    $errors[]='Choose office ID or partner';
                }

                if(!empty($event->partner)) {
                    $event->office_id=-1;
                }
            }

            if($event->duration<=0) {
                $errors[]='Duration must be positive';
            }

            if($event->ends<=$event->starts) {
                $errors[]='Wrong start/end range';
            }

            $time_seconds=$event->h*Date::HOUR+$event->m*Date::MINUTE;

            if($event->time_to_collect>0 && ($event->duration+$time_seconds+$event->time_to_collect>Date::DAY)) {
                $errors[]='Duration and "time to collect" is too long. Maximum duration with "time to collect" with such "time" could be '.date('H:i',Date::DAY-$time_seconds-$event->time_to_collect);
            }

            if($event->duration+$time_seconds>Date::DAY) {
                $errors[]='Duration is too long. Maximum duration with such "time" could be '.date('H:i',Date::DAY-$time_seconds);
            }

            if(!count($errors)) {

                if(!$id && !empty($event->partner)) {

                    $partner_offices=db::query(1,'select o.id,c.val,c.mult,c.timezone from offices o join currencies c on c.id=o.currency_id where o.external_name=:partner')
                        ->param(':partner',$event->partner)
                        ->execute()
                        ->as_array();

                    foreach($partner_offices as $o) {
                        $timestr=explode('UTC',$o['timezone'])[1];

                        list($hour,$minute)=explode(':',$timestr);
                        $hour=trim($hour,'±');

                        $e=new Model_Event(['partner'=>$event->partner,'office_id'=>$o['id'],'type'=>$event->type,'dow'=>$event->dow]);
                        $e->values($event->as_array());

                        $e->starts-=$hour*Date::HOUR+$minute*Date::MINUTE;
                        $e->ends-=$hour*Date::HOUR+$minute*Date::MINUTE;
                        $e->h-=$hour;
                        $e->m-=$minute;

                        $e->dow=$event->dow;

                        if($e->h>24) {
                            $e->h-=24;
                        }

                        if($e->h<0) {
                            $e->h+=24;
                        }

                        if($e->m>60) {
                            $e->m-=60;
                        }

                        if($e->m<0) {
                            $e->m+=24;
                        }

                        $e->office_id=$o['id'];
                        $e->calc=$e->calc<$e->starts?0:$e->calc;
                        $e->max_payout=$e->getValueForLS($o['val'],$o['mult'],$e->max_payout);

                        $e->save();

                    }

                    $this->request->redirect('/enter/events/');
                }

				$this->calc_changes($event,!$id ? 'insert' : 'update');

                $event->save()->reload();
				
				$this->log_changes($event->id);
				
                $this->request->redirect('/enter/events/item/'.$event->id.'?s=1');
            }

        }

        if($new_created) {
            $message='Event #'.$event->id.' saved successfully';
        }

        $view->offices = [-1=>'Choose office']+$offices;
        $view->partners = [null=>'Choose partner']+$partners;
        $view->progressive_map = $event->extra_params ?? $this->_progressive_map;
        $view->message = $message;
        $view->errors = $errors;
        $view->event = $event;
        $view->types = $this->_types;
        $view->id = $id;
        $view->daysofweek = [-1=>'Every day']+$this->days_of_week;

        $allgames=[];

        if($id || $new_created) {
            $sql='select  g.id, g.visible_name
            from games g
            where
                g.show=1 and g.brand=\'agt\' order by g.visible_name ';

            $allgames=db::query(1, $sql)->execute()->as_array('id');
        }


        $view->allgames = $allgames;
        $view->dir = $this->dir;
        $this->template->content = $view;
    }
}

