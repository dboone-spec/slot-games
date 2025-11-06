<?php


class Model_Topwin extends ORM {
	
	protected $_created_column = array('column' => 'created', 'format' => true);
    protected $_belongs_to = [
	    'game' => [
		    'model'		 => 'game',
		    'foreign_key'	 => 'game_id',
	    ],
    ];
    
	public function maxWins($limit = 5, $curr = 'RUB', $cached=null) {
            
            $t = new Model_Topwin();
            $t->where('created', '>', time()-60*60*24*30);
            $t->where('currency', '=', $curr);
            $t->order_by('amount','desc');
            $t->limit($limit);

            return $t->cached($cached)->find_all();
        }
        
    public function winners($limit = 5, $min, $max, $cached=null) {

        $t = new Model_Topwin();
        $t->where('created', '>', time()-60*60*24*30);
        $t->where('amount', '>=', $min);
        $t->where('amount', '<=', $max);
        $t->order_by('created','desc');
        $t->limit($limit);
        
        return $t->cached($cached)->find_all();
    }
}
