<?php

class Controller_Admin1_Moonban extends Controller_Admin1_Base
{

	public function before()
    {
        parent::before();

        dbredis::instance()->select(6); //!! та же база должна быть что и в ноде!
    }

    public function action_delete() {
        $user_id = (int) $this->request->param('id');
        $u=new Model_User($user_id);
        if(!$u->loaded()) {
            throw new Exception('user not found');
        }

        try {
            dbredis::instance()->del('moonBanned-' . $u->id);
            dbredis::instance()->del('moonBannedTime-' . $u->id);

            $this->query_log_changes(
                "banMoonUser",
                [
                    "percent" => 'deleted',
                    'office_id' => $u->office_id,
                    'created' => time(),
                ],
                null,$u->id
            );
        }
        catch (Exception $e) {
            throw $e;
        }

        $this->request->redirect('enter/moonban');
    }

    public function action_index()
    {


        if($this->request->method()=='POST') {
            $u=new Model_User($this->request->post('user_id'));
            $persent=(int) $this->request->post('percent');
            $persent/=100;

            if($persent<0 || $persent>1) {
                throw new Exception('wrong persent '.$persent);
            }

            if(!$u->loaded()) {
                throw new Exception('user not found');
            }

            try {
                dbredis::instance()->set('moonBanned-' . $u->id, $persent);
                dbredis::instance()->set('moonBannedTime-' . $u->id, time());

                $this->query_log_changes(
                    "banMoonUser",
                    [
                        "percent" => $persent,
                        'office_id' => $u->office_id,
                        'created' => time(),
                    ],
                    null,$u->id
                );
            }
            catch (Exception $e) {
                throw $e;
            }
        }

        $find_keys=dbredis::instance()->keys('moonBanned-*');

        $banned_users=[];

        if($find_keys) {
            foreach($find_keys as $k) {
                list($key1,$user_id)=explode('-',$k);
                $banned_users[$user_id]=[
                    'val'=>dbredis::instance()->get($k),
                    'time'=>dbredis::instance()->get('moonBannedTime-'.$user_id),
                ];
            }
        }

        $view          = new View('admin1/moonban/index');

        $view->banned_users = $banned_users;

        $this->template->content = $view;
    }

}
