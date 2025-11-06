<?php

class Controller_Admin1_Moonroundbets extends Controller_Admin1_Base
{

    public function action_index()
    {

        $round_id = (int) arr::get($_GET,'round_id');

        $round=db::query(1,'select * from moon_results where id=:rid and finished is not null order by id desc limit 100')
            ->param(':rid',$round_id)
            ->execute()->as_array('id');

        if(!empty($round_id) && empty($round)) {
            throw new HTTP_Exception_404();
        }


        if($this->request->method()=='POST') {
            $bet_to_dispatch=$this->request->post('bet_to_dispatch');
            $bets_to_dispatch=$this->request->post('bets_to_dispatch');

            if($bet_to_dispatch) {
                $bets_to_dispatch=$bet_to_dispatch;
            }

            $values=[];
            foreach($bets_to_dispatch as $office_id=>$userbet) {
                foreach($userbet as $user_id=>$bets) {
                    foreach($bets as $bet) {
                        $values[]='('.implode(',',[$bet,$office_id,$user_id,$round[$round_id]['rate'],$round[$round_id]['finished']]).')';
                    }
                }
            }

            db::query(Database::INSERT,
                'insert into moon_dispatch_bets(initial_id,office_id,user_id,rate,created) values '.implode(',',$values))
            ->execute();
        }

        $view          = new View('admin1/moonroundbets/index');

        $made_bets=[];
        $missed_bets=[];

        if(!empty($round_id)) {

            $allbets=db::query(1,'select * from bets where game in :game and come::int4 = '.$round_id.' limit 200')
                ->param(':game',th::getMoonGames())
                ->execute()->as_array('id');

            if(!empty($allbets)) {
                $dispatched_bets=db::query(1,'select * from moon_dispatch_bets where initial_id in :ids')
                    ->param(':ids',array_keys($allbets))
                    ->execute()->as_array('initial_id');

                foreach($allbets as $betin) {
                    $was_win=false;
                    foreach($allbets as $bet) {
                        if($bet['initial_id']==$betin['id']) {
                            $was_win=true;
                        }
                    }

                    if(empty($betin['initial_id'])) {
                        $made_bets[$betin['id']]=[
                            'id'=>$betin['id'],
                            'user_id'=>$betin['user_id'],
                            'office_id'=>$betin['office_id'],
                        ];
                    }

                    if(!$was_win && office::instance($betin['office_id'])->office()->enable_moon_dispatch==1) {
                        if(empty($betin['initial_id']) && !isset($dispatched_bets[$betin['id']])) {
                            $missed_bets[$betin['id']]=[
                                'id'=>$betin['id'],
                                'user_id'=>$betin['user_id'],
                                'office_id'=>$betin['office_id'],
                            ];
                        }
                    }
                }
            }
        }

        $view->round_id = $round_id;
        $view->made_bets = $made_bets;
        $view->missed_bets = $missed_bets;

        $this->template->content = $view;
    }

}
