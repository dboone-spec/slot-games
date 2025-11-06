<?php

class Controller_Admin_Share extends Super
{

    public $mark       = 'Акции'; //имя
    public $model_name = 'share'; //имя модели
    public $order_by = ['created', 'desc'];
    public $scripts = ['/js/compiled/main.4ecde5c.js'];

    public function configure()
    {
        $this->search = [
            'name',
        ];

        $this->list = [
            'name',
            'enabled',
            'rules',
            'title',
            'time_from',
            'time_to',
        ];

        $description = new Vidget_Htmleditor('description',$this->model);
		$this->vidgets['description'] = $description;

        $email_text = new Vidget_Htmleditor('email_text',$this->model);
		$this->vidgets['email_text'] = $email_text;

        $rules = new Vidget_Sharerules('rules',$this->model);
		$this->vidgets['rules'] = $rules;

        $time_from = new Vidget_Timestamp('time_from',$this->model);
        $time_from->param('encashment_time',$this->encashment_time);
        $time_from->param('zone_time',$this->zone_time);
        $this->vidgets['time_from'] = $time_from;

        $time_to = new Vidget_Timestamp('time_to',$this->model);
        $time_to->param('encashment_time',$this->encashment_time);
        $time_to->param('zone_time',$this->zone_time);
        $this->vidgets['time_to'] = $time_to;

        $created = new Vidget_Timestamp('created',$this->model);
        $created->param('encashment_time',$this->encashment_time);
        $created->param('zone_time',$this->zone_time);
        $this->vidgets['created'] = $created;

        $image = new Vidget_Image('image',$this->model);
        $image->param('folder', '/uploads/shares/');
        $this->vidgets['image'] = $image;

        $email_img = new Vidget_Image('email_img',$this->model);
        $email_img->param('folder', '/uploads/shares/');
        $this->vidgets['email_img'] = $email_img;

        $slider_img = new Vidget_Image('slider_img',$this->model);
        $slider_img->param('folder', '/uploads/shares/');
        $this->vidgets['slider_img'] = $slider_img;

        $enabled = new Vidget_Select('enabled',$this->model);
        $enabled->param('fields', [
            0 => 'Нет',
            1=> 'Да',
        ]);
        $this->vidgets['enabled'] = $enabled;

        $notification = new Vidget_Select('notification',$this->model);
        $notification->param('fields', [
            0 => 'Нет',
            1=> 'Да',
        ]);
        $this->vidgets['notification'] = $notification;

        $ready = new Vidget_Select('ready',$this->model);
        $ready->param('fields', [
            0 => 'Нет',
            1=> 'Да',
        ]);
        $this->vidgets['ready'] = $ready;

        $this->vidgets['send_test'] = new Vidget_CheckBox('send_test', $this->model);
    }

    public function handler_save($data)
    {
        parent::handler_save($data);
        //TODO move this to action_item
        if(!$this->model->ready AND $this->model->send_test){
            if(!empty($data['name']) AND !empty($data['email_text']) AND !empty($data['url'])){
                $v = [19, 571341];
                if(THEME=='white') {
                    $v = [542500, 528243];
                }
                
                
                
                $this->model->reload();
                if($this->model->type=='unreg') {
                    $v = db::query(1,'select email from users where id in :ids and email is not null')->param(':ids',$v)->execute()->as_array('email');
                    $this->model->notification_unreg($v);
                }
                else {
                    $this->model->notification($v);
                }
                $this->model->notification = 0;
                $this->model->save();
            }
        }
    }

    public function action_item() {
        $this->model=ORM::factory($this->model_name,$this->request->param('id'));

		$sh=$this->request->is_initial() ? '/item' : '/item_related';
		$view=new View($this->sh.$sh);
		$new=!$this->model->loaded();//новая запись

        if(!$new AND $this->model->type == 'tournament') {
            $this->show[] = 'tournament_prizes';
            $this->show[] = 'tournament_games';

            $this->vidgets['tournament_prizes'] = new Vidget_Tournamentprizes('tournament_prizes', $this->model);
            $this->vidgets['tournament_games'] = new Vidget_Tournamentgames('tournament_games', $this->model);
        }

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
}
