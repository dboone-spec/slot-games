<?php

class Model_Tournament_Game extends ORM {
    protected $_primary_key = 'game_id';

    protected $_has_one = [
        'info' => [
            'model' => 'game',
            'foreign_key' => 'id',
        ],
    ];

}