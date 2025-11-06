<?php

class Controller_Admin1_Bets extends Controller_Admin1_Base
{

    protected $_server = 1;

    protected $filters = [
        'id' => [
            'filters' => ['id'],
            'table' => 'bets_id',
        ],
        'office' => [
            'filters' => ['office_id', 'created'],
            'table' => 'bets_office',
            'sort' => ['real_win desc'],
        ],
        'user' => [
            'filters' => ['user_id', 'created'],
            'table' => 'bets_user',
            'sort' => ['created desc'],
        ],
        'game' => [
            'filters' => ['game_id', 'created'],
            'table' => 'bets_game',
            'sort' => ['created desc'],
        ],
        'owner' => [
            'filters' => ['owner', 'created'],
            'table' => 'bets_owner',
			'sort' => ['created desc'],
        ],
    ];

    public $vidgets = [];
    public $per_page=20;
    public $max_pages=200;

    public function action_index()
    {

        $this->model = new Model_Clickhouse_bet();

        $action = $_GET['action'] ?? 'id';

        if ($action == 'partneruser') {
            $user = new Model_User(['external_id'=> $_GET['partneruseruser_id']]);
            $action = 'user';
        }


        $this->vidgets['id'] = new Vidget_Input('id', $this->model);

        $dv = new Vidget_Timestamp('created', $this->model);
        $dv->param('encashment_time', false);
        $dv->param('zone_time', false);
//        $dv->param('day_period', 30);
//        $dv->param('default_day_period',0);
//        $dv->param('default_minute_period',10);

        $this->vidgets['created'] = $dv;

        $this->vidgets['office_id'] = new Vidget_Listnoall('office_id', $this->model);
        $this->vidgets['office_id']->param('list', Person::user()->officesName(null, true));

        $this->vidgets['user_id'] = new Vidget_Input('user_id', $this->model);

        $this->vidgets['owner'] = new Vidget_Ownerslistnoall('office_id', $this->model);

        $sql = 'select g.id, g.visible_name
                    from games g
                    join office_games og on g.id=og.game_id
                    where og.office_id in :oid and brand=\'agt\'
                    order by g.name';

        if (Person::$role == 'sa') {
            $sql = 'select g.id, g.visible_name
                        from games g
                        where brand=\'agt\'
                        order by g.name';

        }

        $games = db::query(1, $sql)->param(':oid', Person::user()->offices())->execute()->as_array('id');
        $kg = ['JP'];
        foreach ($games as $k => $g) {
            $kg[$k] = $games[$k]['visible_name'];
        }
        asort($kg);

        $this->vidgets['game_id'] = new Vidget_List('game_id', $this->model);
        $this->vidgets['game_id']->param('list', $kg);

        $cur=[];
        $sql='select id, code,source, default_k_max_lvl
                from currencies
                where source=\'agt\'
                order by code';

        foreach( db::query(1,$sql)->execute()->as_array() as $row){
            $cur[$row['id']]=$row['code'];
            if($row['source']!='agt') {
                $cur[$row['id']].=' ('.$row['source'][0].')';
            }
        }

        $this->vidgets['currency_id'] = new Vidget_Echo_List('currency_id',$this->model);
        $this->vidgets['currency_id']->param('list',$cur);

        $this->vidgets['result'] = new Vidget_SlotResult('result', $this->model);

        $id = new Vidget_Sum('balance', $this->model);
        $id->param('all',['real_amount','balance','-real_win']);
        $id->param('nomult',true);
        $this->vidgets['balance_before'] = $id;


        $id = new Vidget_Echo('balance', $this->model);
        $this->vidgets['balance_after'] = $id;

        $this->search_vars = array();

        foreach (array_keys($this->vidgets) as $name) {
            $this->model = $this->vidgets[$name]->handler_search($this->model, $_GET);
            $this->search_vars = $this->search_vars + $this->vidgets[$name]->search_vars;
        }

        $where = [];

        foreach ($this->filters[$action]['filters'] as $filter) {
            $request_param = arr::get($_GET, $filter);

            if (empty($request_param)) {
                continue;
            }

            if ($filter == 'created') {
                $where[] = $filter . '>=:' . $filter . '_start';
                $where[] = $filter . '<=:' . $filter . '_end';
                $params[':' . $filter . '_start'] = $this->vidgets[$filter]->start_time;
                $params[':' . $filter . '_end'] = $this->vidgets[$filter]->end_time;
            }
            else {
                $where[] = $filter . '=:' . $filter;
                $params[':' . $filter] = $request_param;
            }
        }

        $fields = [
            'id',
            'user_id',
//            'external_id', логин игрока наверное лучше хранить
            'office_id',
            'currency_id',
            'info',
            'real_amount',
            'real_win',
            'come',
            'game_id',
            'created',
            'result',
            'balance',
            'game',
            'type',
        ];

        if (Person::user()->role == 'sa') {
//            $fields[] = 'method';
            array_unshift($fields,'owner');
        }

        $page=$this->request->param('id',1);
        $page=max(1,$page);

        if($page>$this->max_pages) {
            $page=$this->max_pages;
        }

        $offset=$this->per_page*($page-1);

        $sql = 'select ' . implode(',', $fields) . ' from :table where server=1 and ';
        $sql_count = 'select count(id) as cnt from :table where server=1 and ';

        $sql .= implode(' and ', $where);
        $sql_count .= implode(' and ', $where);

        $sort='';

        if (isset($this->filters[$action]['sort'])) {
            list($sort,$dir) = explode(' ',$this->filters[$action]['sort'][0]);
            $sql .= ' order by ' . implode(',', $this->filters[$action]['sort']);
        }

        $sql .= ' limit '.$this->per_page.' offset '.$offset;

        $data = [];

        $page_data = array
        (
            'total_items'    => 0,
            'items_per_page'  => $this->per_page,
            'current_page'     => array
            (
                'source'     => 'route',
                'key'         => 'id'
            ),
            'auto_hide'         => TRUE,
        );

        if (!empty($params)) {

            $params[':table'] = $this->filters[$action]['table'];

            $data_count = db::query(1, $sql_count)
                ->parameters($params)
                ->execute('clickhouse')
                ->as_array('cnt');

            $total = key($data_count);

            if($total/$this->per_page>$this->max_pages){
                $total=$this->max_pages*$this->per_page;
            }

            $page_data['total_items'] = $total;

            $data = db::query(1, $sql)
                ->parameters($params)
                ->execute('clickhouse','model_clickhouse_bet');

        }

        unset($fields[array_search('game', $fields)]);
        unset($fields[array_search('type', $fields)]);
        unset($fields[array_search('balance', $fields)]);

        array_splice( $fields, 8, 0, ['balance_before','balance_after'] );

        $view = new View('admin1/bets/index');
        $view->model = $this->model;
        $view->fields = $fields;
        $view->sort = $sort;
        $view->page=Pagination::factory($page_data)->render('pagination/floating');
        $view->labels = $this->model->labels();
        $view->filters = $this->filters;
        $view->search_vars = $this->search_vars;
        $view->action = $action;
        $view->data = $data;
        $view->vidgets = $this->vidgets;
        $this->template->content = $view;

    }


}
