<?php

    class Controller_Admin_Game extends Super
    {

        public $mark = 'Игры'; //имя
        public $model_name = 'game'; //имя модели
        public $order_by = array('name'); // сортировка
        public $scripts = ['/js/compiled/main.4ecde5c.js'];

        public function configure()
        {
            $this->search = [
                "name",
                'provider',
                'brand',
                "show",
                "tech_type",
                //"existsrelate",
            ];

            $this->list = [
                'id',
                'image',
                'name',
                'visible_name',
                'provider',
                'brand',
                'show',
                'tech_type',
                //'existsrelate',
            ];

            $this->show = [
                'name',
                'visible_name',
                'provider',
                'brand',
                'show',
                'image',
                'tech_type',
            ];

            //$this->vidgets['existsrelate'] = new Vidget_Existsrelate('existsrelate', $this->model);
            //$this->vidgets['existsrelate']->param('related', 'officegame');
            //$this->vidgets['existsrelate']->param('name', 'id');


            $this->vidgets['show'] = new Vidget_Select('show', $this->model);
            $this->vidgets['show']->param('fields',[
                '' => __('Все'),
                0 => __('Нигде'),
                1 => __('Везде'),
                2 => __('Телефон'),
                3 => __('ПК'),
            ]);
            $this->vidgets['tech_type'] = new Vidget_Select('tech_type', $this->model);
            $this->vidgets['tech_type']->param('fields',[
                '' => __('Все'),
                'f' => __('flash'),
                'h' => __('html5'),
                'fh' => __('flash/html5'),
            ]);

            $this->vidgets['image'] = new Vidget_Image('image', $this->model);
            $this->vidgets['image']->param('folder','games'.DIRECTORY_SEPARATOR.'<brand>'.DIRECTORY_SEPARATOR.'thumbs');

            $res = DB::select('provider','brand')
                    ->from('games')
                    ->distinct('provider')
                    ->execute('games')
                    ->as_array();

            $p[''] = 'Все';
            $b[''] = 'Все';
            foreach($res as $v)
            {
                if(!empty($v['provider'])){
                    $p[$v['provider']] = $v['provider'];
                }
                if(!empty($v['brand'])){
                    $b[$v['brand']] = $v['brand'];
                }
            }

            $this->vidgets['provider'] = new Vidget_Select('provider', $this->model);
            $this->vidgets['provider']->param('fields',$p);

            $this->vidgets['brand'] = new Vidget_Select('brand', $this->model);
            $this->vidgets['brand']->param('fields',$b);

            if(arr::get($_GET, 'provider')=='imperium') {
                $this->list[]='link';

                $this->vidgets['link'] = new Vidget_Link('link',$this->model);
                $this->vidgets['link']->param('id','external_id');
                $this->vidgets['link']->param('link','https://'.dd::get_domain(THEME).'/i/index/');
                $this->vidgets['link']->param('query','tstcs=1');
                $this->vidgets['link']->param('text','показать');
            }
        }
    }
