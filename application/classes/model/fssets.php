<?php

class Model_Fssets extends ORM {

    protected $_table_name = 'freespins_sets';
    protected $_serialize_columns = array('params');

    public function to_process_stack($office_id,$login=null,$expire=null) {
        if(!$this->loaded()) {
            return;
        }

        $stack = new Model_freespinsstack();

        $stack->set_id=$this->id;
        $stack->status=0;

        $stack->name = $this->name;
        $stack->visible_name = $this->visible_name;
        $stack->game = $this->game;
        $stack->game_id = $this->game_id;
        $stack->office_id = $office_id;
        $stack->mass = $this->mass;
        $stack->expirtime = $expire;
        $stack->amount = $this->amount;
        $stack->fs_count = $this->fs_count;
        $stack->params = $this->params;
        $stack->login = $login;
        $stack->dentab_index = $this->dentab_index;
        $stack->lines = $this->lines;
        $stack->time_to_start = time()+Date::MINUTE;

        $stack->save();
    }
}

