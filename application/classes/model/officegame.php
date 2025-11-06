<?php

//model only for admin

class Model_Officegame extends Model_Office_Game {
    protected $_primary_key = 'id';
    protected $_table_name='office_games';

    protected $_belongs_to = [
	    'office' => [
		    'model'		 => 'office',
		    'foreign_key'	 => 'office_id',
	    ],
	    'game' => [
		    'model'		 => 'game',
		    'foreign_key'	 => 'game_id',
	    ],
    ];
}

