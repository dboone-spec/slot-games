<?php

class Model_Kassa extends ORM {

	protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_table_name = 'kasses';

    public function can_use(model_person $person) {
        if($person->loaded() AND !$this->person_id AND !$person->kassa_id 
            AND $this->office_id==$person->office_id AND $this->loaded()
        ) {
            return true;
        }
        
        return false;
    }
    
    public function set_person(model_person $person) {
        if($this->can_use($person)) {
            $this->person_id = $person->id;
            $this->save();
            
            $history = new Model_Kassa_Session();
            $history->kassa_id = $this->id;
            $history->person_id = $person->id;
            $history->time_start = time();
            $history->save()->reload();
            
            $person->kassa_id = $this->id;
            $person->kassa_session_id = $history->id;
            $person->save();
    
            person::user()->reload();
            
            return true;
        }
        
        return false;            
    }
    
    
    public function update_amount($amount, $session_id=null) {
        $this->amount += $amount;
        $this->save();

        if($session_id) {
            $in = $amount>0?$amount:0;
            $out = $amount>0?0:abs($amount);
            
            $kassa_session = new Model_Kassa_Session($session_id);
            $kassa_session->pay_in += $in;
            $kassa_session->pay_out += $out;
            $kassa_session->amount += $amount;
            $kassa_session->save();
        }
        
        return $this->amount;
    }
}

