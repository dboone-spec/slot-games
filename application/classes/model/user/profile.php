<?php

class Model_User_Profile extends ORM
{

    protected $_belongs_to = [
            'user' => [
                    'model' => 'user',
                    'foreign_key' => 'user_id',
            ],
    ];

    public function labels()
    {
        $l = [
                'user_id' => 'пользователь',
                'first_name' => 'имя',
                'last_name' => 'фамилия',
                'middle_name'=>'отчество',
                'birthday'=>'дата рождения',
                'gender'=>'пол',
        ];
        return $l;
    }

}
