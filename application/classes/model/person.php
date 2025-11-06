<?php

class Model_Person extends ORM
{

    protected $_table_name = 'persons';


    protected $_created_column = array('column' => 'created', 'format' => true);

    protected $_has_many = [
        'offices' => [
            'model' => 'person_office',
            'foreign_key' => 'person_id',
        ],
        'balances' => [
            'model' => 'person_balance',
            'foreign_key' => 'person_id',
        ],
    ];

    protected $_belongs_to = [
        'my_office' => [
            'model' => 'office',
            'foreign_key' => 'office_id',
        ],
        'kassa' => [
            'model' => 'kassa',
            'foreign_key' => 'kassa_id',
        ],
        'kassa_session' => [
            'model' => 'kassa_session',
            'foreign_key' => 'kassa_session_id',
        ],
    ];

    protected $ieOwners = [1042 => 'INFIN',
        1090 => 'EvenBet',
        1134 => 'BETCONSTRUCT',
        1092 => 'SoftGamings',
        1150 => 'PinUp',
        1152 => 'Pinco'];

    protected $KristijonasOwners = [
        1128 => 'Olimp',
        1142 => 'BMP',
    ];

    protected $alexOwners = [
        1146 => 'MLsoft',
        1154 => 'POINT PLACE',
    ];

    protected $zhChinaOwners = [
        1177 => 'Noah',
    ];
	
    protected $b2bTotal = [
		1030 => 'BETB2B',
        1219 => '1xbet',
        1220 => '1xIreland',
    ];
	
	public $sergeiAccOwners = [
        1150 => 'PinUp',
        1152 => 'Pinco',
        1030 => 'BETB2B',
		1219 => '1xbet',
        1220 => '1xIreland',
        1092 => 'SoftGamings',
        1134 => 'BETCONSTRUCT',
        1090 => 'EvenBet',
        1128 => 'Olimp',
        1089 => 'NUX',
        1142 => 'BMP',
		1061 => 'VERTBET',
    ];
	
	public $olgaManAccOwners = [
        1134=>'BETCONSTRUCT',
        1092=>'SoftGamings',
        1150=>'PINUP',
        1152=>'PINCO',
        1042=>'INFIN',
        1090=>'EvenBet',
        1030=>'BETB2B',
        1219=>'1XBET',
        1220=>'1XIRELAND',
        1128=>'Olimp',
        1217=>'OlimpAPP',
        1142=>'BMP',
        1061=>'VERTBET',
        1089=>'NUX',
        1156=>'TVBET',
    ];


    protected $_cashedOffices = null;

    public function offices($test = -1)
    {

        if (empty($this->_cashedOffices)) {

            $sql = 'select office_id as id , o.visible_name as name 
                    from person_offices po
                    join offices o on po.office_id=o.id
                    where person_id=:pid';

            if ($this->role == 'sa') {
                $sql = 'select id , o.visible_name as name 
                    from offices o where true';
            }

            if (in_array($this->role, ['fowner','bet'])) {
                $sql = 'select id, o.visible_name as name 
                    from offices o where owner=:owner';
            }

            //IE
            if ($this->id == 1043) {
                $sql = 'select id, o.visible_name as name 
                    from offices o where owner in :ieOwners';
            }

            //Kristijonas
            if ($this->id == 1149) {
                $sql = 'select id, o.visible_name as name 
                    from offices o where owner in :KristijonasOwners';
            }

            //alex
            if ($this->id == 1144) {
                $sql = 'select id, o.visible_name as name 
                    from offices o where owner in :alexOwners';
            }

            //zh China
            if ($this->id == 1171) {
                $sql = 'select id, o.visible_name as name 
                    from offices o where owner in :zhChinaOwners';
            }
			
			//sergei acc manager
            if ($this->id == 1214) {
                $sql = 'select id, o.visible_name as name 
                    from offices o where owner in :sergeiAccOwners';
            }
			
			//olga manager
            if ($this->id == 1232) {
                $sql = 'select id, o.visible_name as name 
                    from offices o where owner in :olgaManAccOwners';
            }
			
            //b2bTotal
            if ($this->id == 1030) {
                $sql = 'select id, o.visible_name as name 
                    from offices o where owner in :b2bTotal';
            }


            if ($test == 0) {
                $sql .= ' and o.is_test=0';
            } elseif ($test == 1) {
                $sql .= ' and o.is_test=1';
            }

            $sql .= ' order by o.id';


            $offices = db::query(1, $sql)->param(':pid', $this->id)
                ->param(':owner', $this->owner_id)
                ->param(':ieOwners', array_keys($this->ieOwners))
                ->param(':KristijonasOwners', array_keys($this->KristijonasOwners))
				->param(':sergeiAccOwners', array_keys($this->sergeiAccOwners))
                ->param(':alexOwners', array_keys($this->alexOwners))
                ->param(':zhChinaOwners', array_keys($this->zhChinaOwners))
				->param(':b2bTotal', array_keys($this->b2bTotal))
				->param(':olgaManAccOwners', array_keys($this->olgaManAccOwners))
                ->execute()
                ->as_array('id');


            if (count($offices) == 0) {
                $offices = [-1 => -1];
            }

            //названия
            if (count($offices) == 0) {
                $this->_cashedOfficesList = [];
                $this->_cashedOfficesListWithID = [];
            } else {
                foreach ($offices as $off) {
                    $this->_cashedOfficesList[$off['id']] = $off['name'];
                    $this->_cashedOfficesListWithID[$off['id']] = $off['id'] . ' ' . $off['name'];
                }
            }

            if ($this->office_id && !isset($offices[$this->office_id])) {
                $offices[$this->office_id] = $this->office_id;
            }

            $this->_cashedOffices = array_keys($offices);
        }

        return $this->_cashedOffices;
    }


    protected $_cashedOfficesList = null;
    protected $_cashedOfficesListWithID = null;
    protected $_partners = [];

    public function uniquePartners($offices = [])
    {
        if (empty($this->_partners)) {
            $sql = 'select distinct partner
              from offices o
              where id in :list
              and partner is not null
              order by 1';

            $partners = db::query(1, $sql)
                ->param(':list', array_keys($offices))
                ->execute()
                ->as_array('partner');

            foreach ($partners as $p) {
                $this->_partners[$p['partner']] = $p['partner'];
            }
        }

        return $this->_partners;
    }

    public function officesName($id = null, $nameWithId = false)
    {

        if (empty($this->_cashedOfficesList)) {

            $this->offices();
        }
        if (empty($id)) {
            return $nameWithId ? $this->_cashedOfficesListWithID : $this->_cashedOfficesList;
        }

        if (!isset($this->_cashedOfficesList[$id])) {
            return 'NoName';
        }

        return $nameWithId ? $this->_cashedOfficesListWithID[$id] : $this->_cashedOfficesList[$id];


    }

    public function create(Validation $validation = NULL)
    {


        if ($this->role == 'client') {
            $this->parent_id = Person::$user_id;
            $this->office_id = 0;
        }


        $r = parent::create($validation);

        $p = '0';
        if ($this->role == 'gameman') {
            $p = '1';
        }

        if ($this->role == 'client') {
            $p = '2';
        }

        if ($this->role == 'cashier') {
            $p = '3';
        }

        if ($this->role == 'report') {
            $p = '4';
        }

        if ($this->role == 'fowner') {
            $p = '5';
        }

        $this->name = $p . $this->id;
        $this->save();

        return $r;

    }


    //TODO deprecated donn't use
    public function can_edit($model)
    {


        if (person::user()->name == 'sa') {
            return true;
        }

        if ($model instanceof Model_Usersstatistics) {
            return false;
        }

        if ($model == 'bet') {
            return false;
        }
        if (in_array(person::$role, ['kassa', 'administrator'])) {
            return false;
        }
        if ($model == 'user' && person::$role != 'sa') {
            return false;
        }
        if ($model == 'operation') {
            return false;
        }
        if ($model == 'terminal') {
            return false;
        }
        //todo добавить ролевую модель
        //не надо сюда ничего добавлять!!!
        return true;
    }

    public function balances()
    {
        $balances = orm::factory('person_balance')
            ->select('*')
            ->join('currencies')
            ->on('person_balance.currency_id', '=', 'currencies.id')
            ->where('person_id', '=', $this->id)
            ->find_all();

        return $balances;
    }

    public function balance($currency_id)
    {
        $sql = <<<SQL
            Select amount
            From person_balances
            Where currency_id = :currency_id
                AND person_id = :person_id
SQL;
        $balance = db::query(1, $sql)
            ->parameters([
                ':currency_id' => $currency_id,
                ':person_id' => $this->id,
            ])
            ->execute()
            ->as_array();

        return $balance[0]['amount'] ?? 0;
    }

    public function reduce_balance($amount, $currency_id)
    {
        $sql = <<<SQL
            Update person_balances set amount = amount-:amount
            Where currency_id = :currency_id
                AND person_id = :person_id
SQL;
        db::query(3, $sql)
            ->parameters([
                ':amount' => $amount,
                ':person_id' => $this->id,
                ':currency_id' => $currency_id,
            ])->execute();
    }

    public function increase_balance($amount, $currency_id)
    {
        $sql = <<<SQL
            Update person_balances set amount = amount+:amount
            Where currency_id = :currency_id
                AND person_id = :person_id
SQL;
        db::query(3, $sql)
            ->parameters([
                ':amount' => $amount,
                ':person_id' => $this->id,
                ':currency_id' => $currency_id,
            ])->execute();
    }

    public function currency($currency_id)
    {
        return (new Model_Currency($currency_id))->code;
    }

    public function save(\Validation $validation = NULL)
    {
        if (!$this->loaded() && !$this->parent_id) {
            $this->parent_id = person::$user_id;
        }
        return parent::save($validation);
    }


    public function labels()
    {
        return [
            'office_id' => __('Office'),
            'enable_telegram' => __('Enable telegram when login'),
            'fio' => __('Print name'),
            'name' => __('Login'),
            'role' => __('Role'),
            'amount' => __('Amount'),
            'password' => __('Password'),
            'owner' => 'Owner_id',
            'our_api' => 'Our API?',

        ];
    }

    public function showOwners()
    {

        if ($this->role == 'sa') {
            return true;
        }

        if ($this->id == 1043) {
            return true;
        }

        if ($this->id == 1149) {
            return true;
        }

        if ($this->id == 1144) {
            return true;
        }
		
		if ($this->id == 1214) {
            return true;
        }
		
		if ($this->id == 1232) {
            return true;
        }


        return false;

    }


    public function owners($addToList = [])
    {
        $owners = [];
        if ($this->role == 'sa') {
            $sql = 'select p.id,p.comment from persons p where comment is not null and comment !=\'\'';
            $dataOwners = db::query(1, $sql)->execute()->as_array('id');
            $owners = $addToList;
            foreach ($dataOwners as $one) {
                $owners[$one['id']] = $one['comment'];
            }
        }

        //IE
        if ($this->id == 1043) {
            $owners = $addToList + $this->ieOwners;
        }

        //Kristijonas
        if ($this->id == 1149) {
            $owners = $addToList + $this->KristijonasOwners;
        }

        //Kristijonas
        if ($this->id == 1144) {
            $owners = $addToList + $this->alexOwners;
        }

        //zhChina
        if ($this->id == 1171) {
            $owners = $addToList + $this->zhChinaOwners;
        }
		
		if ($this->id == 1214) {
            $owners = $addToList + $this->sergeiAccOwners;
        }

		if ($this->id == 1232) {
            $owners = $addToList + $this->olgaManAccOwners;
        }

        if ($this->id == 1030) {
            $owners = $addToList + $this->b2bTotal;
        }

        return $owners;

    }

}

