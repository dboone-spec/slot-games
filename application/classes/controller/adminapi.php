<?php

class Controller_adminapi extends Controller
{

    protected $_ans;
    protected $_req_params=[];

    public function before()
    {

        if(!API_DOMAIN) {
            throw new HTTP_Exception_403;
        }

        parent::before();

        //all checks

        $this->_req_params=$this->request->query();

        if($this->request->method()=='POST') {
            $this->_req_params=$this->request->post();
        }

        unset($this->_req_params['kohana_uri']);

        $apitoken=arr::get($this->_req_params,'apitoken');
        $personlogin=arr::get($this->_req_params,'login');


        if(empty($apitoken)) {
            $this->error('empty API token',501);
        }

        if(empty($personlogin)) {
            $this->error('empty login',502);
        }

        $this->person = new Model_Person([
            'name'=>$personlogin,
            'apitoken'=>$apitoken
        ]);

        if(!$this->person->loaded()) {
            $this->error('login not found',404);
        }

        $this->_ans['error']      = '1';
        $this->_ans['error_code'] = '222';
        $this->_ans['error_message'] = 'unknown';
        $this->_ans['data']       = '';
    }

    public function action_newoffice(){

        if(!gameapi::isOurAPI($this->person->id)) {
            $this->error('login not found',405);
        }

        $currency=arr::get($this->_req_params,'currency');
        if(empty($currency)) {
            $this->error('empty currency',504);
        }

        //MBT B2B
        if (th::isB2B($this->person->id) && $currency=='MBT'){
            throw new Exception('currency not allowed');
        }

        $o = Model_Office::newFromAPI([
            'owner'=>$this->person,
            'currency_code'=>$currency,
            'title'=>arr::get($this->_req_params,'title'),
			'partner'=>arr::get($this->_req_params,'partner'),
            'apiurl'=>arr::get($this->_req_params,'apiurl'),
            'secretkey'=>arr::get($this->_req_params,'secretkey'),
        ]);
		
		if(!$o) {
            $this->error(Model_Office::$error_api_text,510);
        }

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = [
            'id'=>$o->id,
            'is_new'=>(int) $o->is_new,
        ];

    }

	public function action_offices()
    {

        $sql="select o.id,c.code as currency,o.external_name,o.partner,o.gameapiurl,o.secretkey,o.blocked from offices o join currencies c on o.currency_id=c.id where o.owner=:p_id ";

        $partner=arr::get($this->_req_params,'partner');

        $parameters=[
            ':p_id'=>$this->person->id,
        ];

        if(!empty($partner)) {
            $sql.=' and o.partner=:partner';
            $parameters[':partner']=$partner;
        }

        $result = db::query(1,$sql)
            ->parameters($parameters)
            ->execute()
            ->as_array('id');

        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->_ans['data'] = $result;

    }

    public function after() {

        $this->response();
    }

    protected function error($message,$code) {
        $this->_ans['error']=1;
        $this->_ans['error_code'] = $code;
        $this->_ans['error_message'] = $message;
        $this->response();
    }

    protected function response()
    {

        echo json_encode($this->_ans);
        //Kohana::$log->add(Log::INFO,'action: '.$this->request->action().'; params: '.print_r($this->_req_params,1)."\n".Debug::vars($_SERVER));
        //todo need log
        exit;
    }


    public function action_checksum()
    {
        $region = (arr::get($this->_req_params, 'region'));
        $region = UTF8::strtoupper($region);


        $this->_ans['data'] = [];
        if ($region == 'LT') {
            $this->_ans['data']['slot.php'] = hash_hmac('sha256', 'oisaiasodihgoiweugto8643t5634', 'secret155674');
            $data = db::query(1, 'select name,id,visible_name from games where show=1')->execute()->as_array();
            foreach ($data as $row) {
                $this->_ans['data'][$row['name'].'.php'] = hash_hmac('sha256', implode($row), 'secret155674');
            }
        }
        $this->_ans['error']=0;
        $this->_ans['error_code'] = 0;
        $this->_ans['error_message'] = '';
        $this->response();


    }

}


