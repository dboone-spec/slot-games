<?php

class Controller_Admin1_Currency extends Super
{

    public $mark = 'Currencies'; //имя
    public $model_name = 'currency'; //имя модели
    public $order_by = ['code', 'asc'];
    public $sh = 'admin1/currency';
    public $max_pages = 100;
    public $per_page = 500;

    public $canCreate = false;



    public function configure()
    {
        $this->search = [

            'code',
            'name',
            'crypto',

        ];

        $this->list = [
            'code',
            'name',
            'iso_4217',
            'crypto',
            'timezone',
            'country',
        ];

        $this->canCreate = false;
        $this->canEdit = false;
        $this->canItem = false;

        if (person::$role == 'sa') {
            $this->canEdit = true;
            $this->canItem = true;

            $this->search = [
                'code',
                'name',
                'crypto',
                'source'
            ];


            $this->list = [
                'code',
                'name',
                'iso_4217',
                'source',
                'val',
                'updated',
                'crypto',
                'timezone',				
                'country',
				
            ];

        }


        $this->vidgets['crypto'] = new Vidget_CheckBoxb('crypto', $this->model);
        $this->vidgets['updated'] = new Vidget_Date('updated', $this->model);
		$this->vidgets['country_update'] = new Vidget_Date('country_update', $this->model);
        $this->vidgets['source'] = new Vidget_List('source', $this->model);
        $this->vidgets['source']->param('list', ['agt' => 'AGT', 'vertbet' => 'Vertbet']);


    }

    public function action_index()
    {
        if (   ( $_GET['xls'] ?? '0') != 'go') {
            return parent::action_index();
        }

        $this->auto_render = false;
        $data = $this->handler_search($_GET)->find_all();

        $csv = [$this->list];

        foreach ($data as $row) {
            $a = [];
            foreach ($this->list as $key) {
                $a[] = $row->__get($key);
            }
            $csv[] = $a;
        }

        $writer = new XLSXWriter();
        $writer->writeSheet($csv, 'Sheet1');            // no headers
        $this->response->headers('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->headers('Content-Disposition', 'attachment;filename="currencies.xlsx"');
        $this->response->headers('Cache-Control', 'max-age=0');
        $this->response->body($writer->writeToString());
        $this->auto_render = false;
        return null;
    }


    public function handler_search($vars)
    {
        if (isset($vars['code'])) {
            $vars['code'] = UTF8::strtoupper($vars['code']);
        }
        $model = parent::handler_search($vars);

        if (person::$role == 'sa') {
            return $model;
        }

        return $model->where('source', '=', 'agt');
    }


}
