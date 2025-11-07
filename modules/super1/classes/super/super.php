<?php
class Super_Super extends Controller_Admin1_Base{

public $dir='/admin';
public $mark; //имя
public $model_name; //имя модели
public $controller=null; //
public $sh='super2'; //шаблон
public $order_by=null; // сортировка

public $list='all'; //столбцы в списке
public $show='all'; //столбцы на правке/создании
public $search='all'; //столбцы поиска
public $vidgets=array();
public $per_page=20;
public $max_pages=50;
public $actions=array(); //дополнительные действия в index
public $canEdit=true;
public $canDelete=false;
public $canCreate=true;
public $canItem=true;
public $can_csv=false;
public $scripts = [];

public $notSortable=[];

protected $changes = [];

public function configure(){


}

public function before(){

    if ( !$this->request->is_initial()){
            $this->auto_render=false;
    }
    parent::before();
    $this->controller=empty($this->controller) ? $this->model_name : $this->controller;
    $this->model=ORM::factory($this->model_name);

    $this->configure();
    //значения по умолчанию - все столбцы
    if ($this->list=='all'){
            $this->list=array_keys($this->model->list_columns());
    }

    if(!isset($this->listedit)){
            $this->listedit=$this->list;
    }

    if ($this->show=='all'){
            $col=$this->model->list_columns();
            unset($col[$this->model->primary_key()]);
            $this->show=array_keys($col);


    }

    if ($this->search=='all'){
            $this->search=array_keys($this->model->list_columns());
    }



    //формируем виджеты, для не указанных столбцов
    $dbtype=Kohana::$config->load('dbtypes');

    foreach  ($this->model->list_columns() as $key=>$col){
            if (!isset($this->vidgets[$key])){

                    if (isset($dbtype[$col['data_type']])){
                            $this->vidgets[$key]=Vidget::factory($dbtype[$col['data_type']],$key,$this->model);
                    }
                    else{
                            $this->vidgets[$key]=new Vidget_Input($key,$this->model);
                    }
            }

    }

    if($sortby = arr::get($_GET,'sortby')) {
        $this->order_by=[$sortby];
        if($sortas = arr::get($_GET,'sortas')) {
            $this->order_by[]=$sortas;
        }
    }

}




public function action_index(){

	if($this->request->method()=='POST') {
            $this->request->redirect($this->dir . '/' . $this->request->controller().'?'.http_build_query($this->request->post()));
            exit;
        }

        $export     = isset($_GET['export']) ? $_GET['export'] : null;
        //пагинатор

        $page=$this->request->param('id',1);
        $page=max(1,$page);

        if($page>$this->max_pages) {
            $page=$this->max_pages;
        }

        $offset=$this->per_page*($page-1);

        $total = $this->handler_search($_GET)->count_all();

        if($total/$this->per_page>$this->max_pages){
            $total=$this->max_pages*$this->per_page;
        }

		$page_data = array
			(
			      'total_items'    => $total,
                  'items_per_page'  => $this->per_page,
                  'current_page'     => array
                          (
                              'source'     => 'route',
                              'key'         => 'id'
                          ),
                  'auto_hide'         => TRUE,
                );



        //основные данные
        $view=new View($this->sh.'/index');

        $data=$this->model->offset($offset)->limit($this->per_page);
        if (is_array($this->order_by)){
                $this->model->order_by($this->order_by[0],isset($this->order_by[1]) ? $this->order_by[1] : null);
        }

        $csv='';
        if($export && $this->can_csv){

            $vidg=$this->vidgets;
            $head=[];
            $head[0]='';
            $csv_data=$this->handler_search($_GET)->find_all();

            foreach($this->list as $header){
                $head[0].=$header.';';
            }
            $body=[];
            $i=0;
            foreach($csv_data as $c){
                $body[$i]='';
                foreach($this->list as $header){
                    if($header=="amount"){
                        $body[$i].=$c->$header.';';
                    }else{
                        $body[$i].=$vidg[$header]->render($c, 'list').';';
                    }
                }
                $i++;
            }
            $csv=array($head, $body);
            th::to_csv($csv, $time_from, $time_to, $this->request->controller());
        }

        $view->data=$this->handler_search($_GET)->find_all();
        $view->list=$this->list;
        $view->search=$this->search;
        $view->search_vars=$this->search_vars;
        $view->label=$this->model->labels();
        $view->model=$this->controller;
        $view->mark=$this->mark;
		$view->bigcurrent=$this->bigcurrent;
        $view->dir=$this->dir;
        $view->vidgets=$this->vidgets;
        $view->page=Pagination::factory($page_data)->render('pagination/floating');
        $view->actions=$this->actions;
        $view->canEdit=$this->canEdit;
        $view->canDelete=$this->canDelete;
        $view->canCreate=$this->canCreate;
        $view->canItem=$this->canItem;
        $view->notSortable=$this->notSortable;
        $view->currentUrl=Request::current()->query();


        if ($this->request->is_initial()){
                $this->template->content=$view->render();
        }
        else{
                $this->response->body($view->render());
        }
}


public function handler_save($data){

    if (!$this->canCreate and !$this->model->loaded()){
        throw new HTTP_Exception_404();
    }

    if(!$this->canEdit and $this->model->loaded() ){
        throw new HTTP_Exception_404();
    }

    $old_data=$this->model->object();

    foreach ($this->show as $name){
            if ($name==$this->model->primary_key()){
                    continue;
            }
            //TODO Генерация ошибок!!
            $this->model=$this->vidgets[$name]->handler_save($data,$old_data,$this->model);

    }
}


	public function handler_search($vars){

		$model=$this->model;
		$this->search_vars=array();
		foreach ($this->search as $name){
			$model=$this->vidgets[$name]->handler_search($model,$vars);
			$this->search_vars=$this->search_vars+$this->vidgets[$name]->search_vars;
		}

		return $model;
	}



public function canItem(){
    return $this->canItem;
}


public function action_item(){

    if(!person::user()->can_edit($this->model)) {
        $this->request->redirect($this->dir.'/'.$this->controller);
    }

    $this->model=ORM::factory($this->model_name,$this->request->param('id'));

    if(!$this->canItem()){
        throw new HTTP_Exception_404();
    }

    $sh=$this->request->is_initial() ? '/item' : '/item_related';
    $view=new View($this->sh.$sh);
    $new=!$this->model->loaded();//новая запись

    if ($this->request->method() == 'POST'){
        $this->handler_save(Request::current()->post());
        $errors = array();
        $v=$this->model->validation();
        $v->check();
        $errors=$v->errors($this->model_name);

        if (count($errors)==0){

                $type = $new?'insert':'update';
                $this->calc_changes($this->model,$type);
                $this->model->save();
                $this->log_changes($this->model->id);

                if ($new) {
                        if ($this->request->initial()){
                                $this->request->redirect($this->dir.'/'.$this->model_name.'/item/'.$this->model->id.'?s=1');
                        }
                        else {
                                return null;
                        }
                }
                $view->suc=1;
        }
        else{
                $view->error=$errors;
        }
    }


    $view->item=$this->model;
    $view->label=$this->model->labels();
    $view->show=$this->show;
    $view->model=$this->model_name;
    $view->model=$this->controller;
    $view->mark=$this->mark;
    $view->dir=$this->dir;
    $view->vidgets=$this->vidgets;
    $view->actions=$this->actions;

    if ($this->request->is_initial()){
            $this->template->content=$view->render();
    }
    else{
            $this->response->body($view->render());
    }

}


	public function action_delete(){

                if (!$this->delete){
                    throw new HTTP_Exception_404();
                }

		$this->model=ORM::factory($this->model_name,$this->request->param('id'));
		if ($this->model->loaded()){
                        $this->calc_changes($this->model,'delete');
			$this->model->delete();
                        $this->log_changes();
		}
		$this->request->redirect($_SERVER['HTTP_REFERER']);
	}



	public function handler_listsave($model){

		$old_data=$model->object();
		$data=array();
		$post=Request::current()->post();

		//Собираем данные
		foreach ($this->listedit as $column){
			if ($column==$model->primary_key() or $column=='__delete'){
				continue;
			}
			if (isset($post[$column][$model->pk()])){
				$data[$column]=$post[$column][$model->pk()];
			}
		}

		//записываем в модель
		foreach ($this->listedit as $name){
			if ($name==$model->primary_key() or $name=='__delete'){
				continue;
			}
			//TODO Генерация ошибок!!
			$model=$this->vidgets[$name]->handler_save($data,$old_data,$model);
		}

		return $model;
	}


	public function action_listedit(){
		/*
		$this->listedit[]='__delete';
		$this->vidgets['__delete']=new Vidget_Superdelete('__delete',$this->model);
		*/
		//пагинатор
		$page=$this->request->param('id',1);
		$page=max(1,$page);
		$offset=$this->per_page*($page-1);

        $total = $this->handler_search($_GET)->count_all();

        if($total/$this->per_page>$this->max_pages){
            $total=$this->max_pages*$this->per_page;
        }

		$page_data = array
			(
			  'total_items'        => $total,
			  'items_per_page'  => $this->per_page,
			  'current_page'     => array
				  (
				      'source'     => 'route',
				      'key'         => 'id'
				  ),
			  'auto_hide'         => TRUE,
			);



		//Сохраняем
		if ($this->request->method() == 'POST'){

			$pk=$this->model->primary_key();
			$errors = array();
			$data=array();
			$class=$this->model->object_name();
			$post=Request::current()->post();



			foreach ($post[$pk] as $id){
				$m=ORM::factory($class,$id);

				//удаляем
				if (isset($post['__delete'][$id])){
					$m->delete();
					continue;
				}
				//сохраняем
				else{
					$m=$this->handler_listsave($m);

					$v=$m->validation();
					$v->check();
					$err=$v->errors($this->model_name);

					if (count($err)==0){
						$m->save();
					}
					else{
						$errors[$id]=$err;
					}
				}

				$data[$id]=$m;


			}




		}
		//Просто запрос без сохранения
		else{
			$data=$this->model->offset($offset)->limit($this->per_page);
			if (is_array($this->order_by)){
				$this->model->order_by($this->order_by[0],isset($this->order_by[1]) ? $this->order_by[1] : null);
			}
			$data=$this->handler_search($_GET)->find_all();
		}

		//основные данные
		$view=new View($this->sh.'/listedit');

		$view->data=$data;
		$view->list=$this->listedit;
		$view->search=$this->search;
		$view->search_vars=$this->search_vars;
		$view->label=$this->model->labels();
		$view->model=$this->controller;
		$view->mark=$this->mark;
		$view->dir=$this->dir;
		$view->vidgets=$this->vidgets;
		$view->page=Pagination::factory($page_data)->render('pagination/floating');
		$view->actions=$this->actions;

		if ($this->request->is_initial()){
			$this->template->content=$view->render();
		}
		else{
			$this->response->body($view->render());
		}


	}

	public function action_checkbox() {
        $this->auto_render = false;
        $id = $this->request->param('id');
        $field = arr::get($_POST, 'field');
        $checked = intval(arr::get($_POST, 'checked', 0));

        $ans = ['error' => 1];
        $model = ORM::factory($this->model_name, $id);

        if($model->loaded() AND in_array($field, $this->list)) {
            if($model instanceof Model_User AND !$model->parent_id) {
                $childs = orm::factory('user')->where('parent_id', '=', $model->id)->find_all();
                foreach ($childs as $c) {
                    $c->__set($field, $checked);
                    $this->calc_changes($c,'update');
                    $c->save();
                    $this->log_changes();
                }
            }

            if($model instanceof Model_Office) {
                $checked= ($checked==1) ? time() : 0;
            }

            if($model instanceof Model_Office && $field=='blocked') {
                if($checked==0) {
                    office::unblock($model->id);
                }
                else {
                    office::block($model->id);
                }
            }

            if($model instanceof Model_Jackpot && $field=='active') {
                $redis = dbredis::instance();
                $redis->select(1);

                $redis->set('jpa-'.$model->office_id,$checked);
            }

            if($model instanceof Model_Office && $field=='enable_jp') {
                $redis = dbredis::instance();
                $redis->select(1);

                $redis->set('jpa-'.$model->office_id,$checked);
            }

            $model->__set($field, $checked);

            $this->calc_changes($model,'update');
            $model->save();
            $this->log_changes();

            $ans['error'] = 0;
        }

        $this->response->body(json_encode($ans));
    }
    public function action_input() {
        $this->auto_render = false;
        $id = $this->request->param('id');
        $field = arr::get($_POST, 'field');

        $fields = ['bets_arr'];

        $value = arr::get($_POST, 'value', 0);

        if(!in_array($field,$fields)) {
            $value = floatval($value);
        }


        $ans = ['error' => 1];
        $model = ORM::factory($this->model_name, $id);

        if($model->loaded() AND in_array($field, $this->list)) {
            if($model instanceof Model_User AND !$model->parent_id) {
                $childs = orm::factory('user')->where('parent_id', '=', $model->id)->find_all();
                foreach ($childs as $c) {
                    $c->__set($field, $value);
                    $c->save();
                }
            }

            $model->__set($field, $value);
            $model->save();

            $ans['error'] = 0;
        }

        $this->response->body(json_encode($ans));
    }

    public function after() {
        if ($this->auto_render === TRUE)
		{
            $this->template->scripts = $this->scripts;
        }
        parent::after();
    }

}