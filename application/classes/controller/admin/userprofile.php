<?php

class Controller_Admin_Userprofile extends Super
{

    public $mark       = 'Профили'; //имя
    public $model_name = 'user_profile'; //имя модели
    public $controller = 'userprofile';
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function configure()
    {
        $this->search = [
                'user_id',
        ];

        $this->list = [
                'user_id',
                'first_name',
                'last_name',
                'middle_name',
                'birthday',
                'gender',
        ];

        $this->show = [
        ];

        $no_edit_fields = [
            'first_name',
            'last_name',
            'middle_name',
        ];

        foreach ($no_edit_fields as $field) {
            $this->vidgets[$field] = new Vidget_Echo($field, $this->model);
        }

        $this->vidgets['birthday'] = new Vidget_Date('birthday',$this->model);

        $id = new Vidget_Userwithparent('user_id', $this->model);
        $id->param('related','user');
        $id->param('name','name');
        $this->vidgets['user_id'] = $id;

        $this->vidgets['gender'] = new vidget_gender('gender',$this->model);

    }

    public function handler_search($vars) {
        $model = parent::handler_search($vars);
        $model->distinct('*')->join('users')
                ->on('users.id', '=', 'user_profile.user_id')
                ->where('users.office_id', 'not in', kohana::$config->load('static.test_offices'));

        return $model->where('users.office_id','in',$this->offices()); //rub, usd online
    }
}
