<?php

class Controller_Admin_Persons extends Super {

    public $template = 'layout/admin';
    public $mark='Персонал'; //имя
	public $model_name='person'; //имя модели
	public $controller='persons';
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function configure() {
        $this->show = [
            "password",
        ];

        $this->vidgets['password'] = new Vidget_Password('password',$this->model);
        $this->vidgets['password']->param('salt','salt');
        $this->vidgets['password']->param('func','auth::pass');
    }

    public function action_index() {
        $view = new View('admin/persons/index');

        if(person::$role=="agent"){
            $sql = <<<SQL
                Select p.name, coalesce(p.office_id,t.office_id) as office_id, p.role
		From persons p left JOIN (
				Select person_id, office_id
				From person_offices
				Where office_id in :office_id
		) as t ON p.id = t.person_id
		Where 
		(role in ('administrator','kassa') and p.office_id in :office_id) OR 
		(p.parent_id = :person_id) OR 
		(role in ('manager','rmanager') and t.office_id in :office_id)
SQL;
            $res = db::query(1, $sql)
                    ->param(':office_id', person::user()->offices())
                    ->param(':person_id', person::$user_id)
                    ->execute()->as_array();
        }else if(person::$role=="rmanager"){
            $sql = <<<SQL
                Select p.name, coalesce(p.office_id,t.office_id) as office_id, p.role
		From persons p left JOIN (
				Select person_id, office_id
				From person_offices
				Where office_id in :office_id
		) as t ON p.id = t.person_id
		Where 
		(role in ('administrator','kassa') and p.office_id in :office_id) OR 
		(role in ('manager') and t.office_id in :office_id)
SQL;
            $res = db::query(1, $sql)
		->param(':office_id', person::user()->offices())
                ->param(':person_id', person::$user_id)
		->execute()->as_array();
        }else{
            $sql = <<<SQL
                Select p.name, p.office_id, p.role
                From persons p JOIN (
                    Select office_id
                    From person_offices
                    Where person_id = :person_id
                ) as t ON p.office_id = t.office_id
                Where role in ('administrator','kassa')
SQL;
            $res = db::query(1, $sql)->param(':person_id', person::$user_id)->execute()->as_array();
        }

        $admins = [];
        $kassirs = [];
        $managers = [];
        $rmanagers = [];

        foreach ($res as $v) {
            if($v['role'] == 'kassa') {
                $kassirs[$v['office_id']][] = $v['name'];
            } elseif($v['role']=='administrator') {
                $admins[$v['office_id']][] = $v['name'];
            } elseif($v['role']=='manager') {
                $managers[$v['office_id']][] = $v['name'];
            }elseif($v['role']=='rmanager') {
                $rmanagers[$v['office_id']][] = $v['name'];
            }
        }

        $view->dir = $this->dir;
        $view->roles = $this->_can_roles;
        $view->kassirs = $kassirs;
        $view->managers = $managers;
        $view->admins = $admins;
        $view->rmanagers = $rmanagers;

        $this->template->content = $view;
    }



}
