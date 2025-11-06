<?php

defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Admin1_Base extends Controller_Template
{

    public $template = 'layout/admin1';
    public $dir = '/enter';
    protected $_access_role = ['sa'];
    public $sh = 'super'; //шаблон
    public $order_by = array('created', 'desc'); // сортировка
    public $scripts = ['/js/compiled/main.4ecde5c.js'];
    public $encashment_time = 0;
    public $zone_time = 0;
    public $day_period = [
        'cashier' => 5,
        'administrator' => 5,
        'manager' => 61,
        'gameman' => 61,
        'client' => 61,
    ];
    protected $_roles = [
        'sa' => 0,
        'cashier' => 10,
        'administrator' => 20,
        'manager' => 30,
        'rmanager' => 40,
        'agent' => 50,
        'analitic' => 60,
        'gameman' => 70,
    ];
    protected $_can_roles = [];
	
	protected $bigcurrent='';

    public function before()
    {

        $route_name = $this->request->route()->name($this->request->route());


        if (!GAMEOFFICE) {
            throw new HTTP_Exception_404;
        }


        Person::instance()->get_user();

        if (person::$user_id && !in_array(Person::$role, ['gameman', 'sa', 'client', 'cashier', 'promo', 'report', 'fowner', 'bet'])) {
            person::logout();
            $this->request->redirect('https://www.google.com/search?q=happy+new+year');
        }

        if (person::user()->my_office->blocked > 0) {
            person::logout();
        }

        //если включено апи - кассир уходит
        if (person::$user_id && in_array(Person::$role, ['cashier']) && person::user()->my_office->apienable > 0) {
            person::logout();
        }

        I18n::$lang = Cookie::get('lang', 'en');
        //I18n::$lang='en';

        if (!Person::$user_id) {
            $this->request->redirect('/' . Route::get($route_name)->uri(array('controller' => 'login')));
        }


        parent::before();


        if (person::$role != 'sa') {
            $this->encashment_time = 9;
            $this->zone_time = 3;
        }

        if (in_array(Person::$role, ['administrator', 'cashier'])) {
            $this->encashment_time = person::user()->my_office->encashment_time;
            $this->zone_time = person::user()->my_office->zone_time;
        }

        $menu = Kohana::$config->load('adminnavbar')['personMenu'];

        if (!in_array($this->request->controller(), $menu)) {
            throw new HTTP_Exception_404;
        }

        if ($this->auto_render === TRUE) {
            $this->template->dir = $this->dir;
            $this->template->scripts = $this->scripts;
            $this->template->zone_time = $this->zone_time;
            $this->template->encashment_time = $this->encashment_time;


            $this->template->navbar = $this->makeNavBar();
            $this->template->quickMenu = $this->makeQuickMenu();


        }


    }


    protected function makeNavBar()
    {

        $current = $this->request->controller();
        $bigcurrent = '';

        $menu = Kohana::$config->load('adminnavbar');

        $navBar = $menu['navBar'];

        foreach ($navBar as $mainKey => $linkGroup) {

            foreach ($linkGroup as $key => $link) {
                if (!in_array($key, $menu['personMenu'])) {

                    unset($navBar[$mainKey][$key]);
                }
                if ($key == $current) {
                    $bigcurrent = $mainKey;
                }
            }
        }

        $view = new view('admin1/navbar/left');
        $view->navBar = $navBar;
        $view->current = $current;
        $this->bigcurrent = $bigcurrent;
        $view->bigcurrent = $bigcurrent;
        $view->dir = $this->dir;
        $view->icons = $menu['icons'];

        return $view->render();

    }

    protected function makeQuickMenu()
    {


        $menu = Kohana::$config->load('adminnavbar');

        $navBar = $menu['navBar'];

        unset($navBar['Profile']);

        foreach ($navBar as $mainKey => $linkGroup) {

            foreach ($linkGroup as $key => $link) {
                if (!in_array($key, $menu['personMenu'])) {

                    unset($navBar[$mainKey][$key]);
                }
            }
        }

        $view = new view('admin1/navbar/quick');
        $view->navBar = $navBar;
        $view->dir = $this->dir;
        $view->icons = $menu['icons'];

        return $view->render();

    }


    public function restrict($menu)
    {
        foreach (['list', 'search'] as $actions) {
            if (($key = array_search($menu, $this->$actions)) !== false) {
                unset($this->$actions[$key]);
            }
        }
    }

    protected function calc_changes($model, $type)
    {
        $new = $model->original_values();
        $old = $model->object();

        $new_values = array_diff_assoc($old, $new);

        $diff = [
            'model_name' => $model->object_name(),
            'model_id' => $model->id,
            'type' => $type,
            'new' => [],
            'old' => []
        ];

        foreach ($new_values as $k => $v) {
            $diff['new'][$k] = $v;
            $diff['old'][$k] = $new[$k] ?? '-';
        }

        if ($type == 'delete') {
            $diff['old'] = $old;
        }

        $this->changes = $diff;
    }

    protected function query_log_changes($type, $new_data, $model_name = null, $model_id = null)
    {
        $action = new Model_Action;

        $action->person_id = person::$user_id;
        $action->type = $type;
        $action->model_name = $model_name;
        $action->model_id = $model_id;
        $action->new_data = json_encode($new_data ?? []);

        $action->save();
    }

    protected function log_changes($new_id = null)
    {
        $action = new Model_Action;

        $action->type = $this->changes['type'];
        $action->person_id = person::$user_id;
        $action->model_name = $this->changes['model_name'];
        $action->model_id = $this->changes['model_id'] ?? $new_id;
        $action->new_data = json_encode($this->changes['new'] ?? []);
        $action->old_data = json_encode($this->changes['old'] ?? []);

        $action->save();
    }


}
