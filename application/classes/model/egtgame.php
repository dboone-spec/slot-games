<?php

class Model_Egtgame extends ORM {
    protected $_belongs_to = [
	    'game' => [
		    'model'		 => 'game',
		    'foreign_key'	 => 'game_id',
	    ]
    ];
}

