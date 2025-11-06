<?php

class Controller_Admin_Profile extends Controller_Admin_Base
{

    public function action_index()
    {
        $view   = new View('admin/profile/settings');
        $errors = [];

        if($this->request->method() == 'POST')
        {
            $telegram     = intval(arr::get($_POST,'telegram',0));
            $confirm_code = arr::get($_POST,'confirm_code');

            if(person::user()->enable_telegram != $telegram)
            {
                if($confirm_code == person::user()->auth_code)
                {
                    person::user()->enable_telegram = $telegram;
                }
                else
                {
                    $errors['telegram'] = [
                            'error' => 1,
                            'text' => 'Не верный telegram код подтверждения',
                    ];
                }
            }

            $phone = th::clearphone(arr::get($_POST,'phone'));

            $canChangePhone=true;

            if(person::$role=='kassa' && person::user()->phone_confirm) {
                $canChangePhone=false;
            }

            if(!$phone OR $phone == person::user()->phone) {
                $canChangePhone=false;
            }

            if($canChangePhone)
            {
                $errors['phone'] = [
                        'error' => 1,
                        'text' => '',
                ];

                $user_phone = new Model_Person(['phone' => $phone,'phone_confirm' => 1,]);

                if(th::checkphone($phone))
                {
                    $errors['phone']['text'] = 'Не верный номер телефона';
                }
                elseif($user_phone->loaded() AND $user_phone->id != person::$user_id)
                {
                    $errors['phone']['text'] = 'Невозможно использовать данный номер телефона';
                }
                else
                {
                    person::user()->phone           = $phone;
                    person::user()->phone_confirm   = 0;
                    person::user()->enable_telegram = 0;
                    $errors['phone']                = [
                            'error' => 0,
                            'text' => 'Номер телефона изменен',
                    ];
                }
            }

            $this->calc_changes(person::user(),'profile');
            person::user()->save();
            $this->log_changes(person::user()->id);
        }

        $view->dir            = $this->dir;
        $view->errors            = $errors;
        $this->template->content = $view;
    }

    public function action_pass()
    {
        if($this->request->method() == "POST")
        {
            $this->auto_render = false;

            $ans    = ['error' => 1,'errors' => [],'text' => ''];
            $errors = array();

            $pass   = arr::get($_POST,'password');
            $pass_c = arr::get($_POST,'password_confirm');

            if($pass != $pass_c)
            {
                $errors['password_confirm'] = __('Пароли не совпадают');
            }

            if(strlen($pass) < 6)
            {
                $errors['password'] = __('Пароль должен быть не меньше 6 символов');
            }

            $p = new Model_Person(person::$user_id);

            if($p->loaded() AND count($errors) == 0)
            {
                $p->salt     = rand(1,10000000);
                $p->password = auth::pass($pass,$p->salt);
                $this->calc_changes($p,'pass');
                $p->save();
                $this->log_changes($p->id);

                $ans['text']  = __('Пароль успешно изменен');
                $ans['error'] = 0;
            }
            else
            {
                $errors['critical'] = __('Ошибка при сохранении данных');
            }

            $ans['errors'] = $errors;
            $this->response->body(json_encode($ans));
        }
    }

    public function action_tgcode()
    {
        $this->auto_render = false;

        $ans = [
                'error' => 1,
                'text' => 'Ошибка при отправке кода',
        ];

        $code = mt_rand(10000,99999);

        person::user()->auth_code = $code;
        $this->calc_changes(person::user(),'tgcode');
        person::user()->save();
        $this->log_changes(person::user()->id);

        if(!tgbot::phoneExists(person::user()->phone)) {
            $ans = [
                    'error' => 1,
                    'text' => 'Вы не подписаны на telegram бота',
            ];
        }

        if(th::tgsend(person::user()->phone,$code))
        {
            $ans = [
                    'error' => 0,
                    'text' => 'Код отправлен',
            ];
        }

        $this->response->body(json_encode($ans));
    }

    public function action_phone()
    {
        $this->auto_render = false;

        $phone = arr::get($_GET,'phone');
        $phone = th::clearphone($phone);
        $ans   = ['error' => 1];

        if(th::checkphone($phone))
        {
            $ans['text'] = 'Не верный номер телефона ' . $phone;
            $this->response->body(json_encode($ans));
            return null;
        }

        $bad_phone_count = Cookie::get('bad_phone_count',0) + 1;
        Cookie::set('bad_phone_count', $bad_phone_count, 60 * 60);
        
        if($bad_phone_count > 3 && person::user()->last_sms_send + 60 * 60 > time())
        {
            $ans['text'] = 'СМС можно отправлять не чаще раз в час';
            $this->response->body(json_encode($ans));
            return null;
        }

        $code                         = mt_rand(10000,99999);
        person::user()->phone         = $phone;
        person::user()->phone_code    = $code;
        person::user()->last_sms_send = time();
        $this->calc_changes(person::user(),'phone');
        person::user()->save();
        $this->log_changes(person::user()->id);

        if(th::smssend($phone,$code))
        {
            $ans['error'] = 0;
        }
        else
        {
            $ans['text'] = 'Не удалось отправить СМС попробуйте позже';
        }

        $this->response->body(json_encode($ans));
    }

    public function action_code()
    {
        $this->auto_render = false;

        $code = arr::get($_GET,'code');

        $ans['error'] = 1;
        $ans['text']  = 'Не верный код подтверждения';

        if(person::user()->phone_code == $code)
        {
            $ans['error']                 = 0;
            $ans['text']                  = '';
            person::user()->phone_confirm = 1;
            $this->calc_changes(person::user(),'code');
            person::user()->save();
            $this->log_changes(person::user()->id);
        }

        $this->response->body(json_encode($ans));
    }

}
