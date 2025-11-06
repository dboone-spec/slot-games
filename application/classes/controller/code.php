<?php

class Controller_Code extends Controller_Base {

	public $need_auth=true;

    public function action_set() {
        $game_name = $this->request->param('id');

        $code = new Model_Bonus_Code([
            'name' => 'fs_reg_' . $game_name,
            //для разделения фриспинов при регистрации по офисам
            'office_id' => auth::user()->office_id,
        ]);


        if($code->loaded() AND auth::user()->reg_fs==-1) {
            $use_info = auth::user()->can_use_code($code->id);

            if(!$use_info['error']) {
                $code->use_code(auth::$user_id, $_SERVER['REMOTE_ADDR']);
                auth::user()->reg_fs = 1;
                auth::user()->save()->reload();

                $game = new Model_Game(['name'=>$game_name,'provider'=>'our']);

                $this->request->redirect($game->get_link());
            }
        }

        $this->request->redirect('/');
    }

    public function action_delete() {
        auth::user()->reg_fs = 0;
        auth::user()->save()->reload();

        $this->request->redirect('/');
    }

    public function action_paydayly() {

        $game_name = $this->request->param('id');

        $name = 'dayly_fs2_' . $game_name;
        
        $parent_bonus_type = auth::parent_acc()->dayly_bonus_type;
        
        if($parent_bonus_type=='freespins') {
            $name = 'dayly_fs_' . $game_name;
        }
        
        $code = new Model_Bonus_Code([
            'name' => $name,
            'office_id' => auth::user()->office_id,
        ]);

        $game_model = new Model_Game(['name'=>$game_name,'provider'=>'our']);
        
        if($code->loaded() AND $game_model->loaded()) {
            $code->bind_daylyfs(auth::$user_id);

            if($parent_bonus_type=='freespins') {
                db::query(Database::UPDATE, 'update users set dayly_bonus_type = :dayly_bonus_type where id=:id')
                        ->param(':dayly_bonus_type','freespins2')
                        ->param(':id',auth::user()->parent_id)
                        ->execute();
            }
            
            $this->request->redirect($game_model->get_link());
            return;
        }

        Flash::warning('/popup/chest');
        $this->request->redirect('/');
    }

}
