<?php

class Controller_Payeerpayment extends Controller {
	
	
	public function action_success(){
		$this->request->redirect('http://kohana3.ru?paysuccess=1');
	}
	
	public function action_fail(){
		$this->request->redirect('http://kohana3.ru?payfail=1');
	}
	
	
	public function action_status(){
		
		if ($_SERVER['REMOTE_ADDR'] != '37.59.221.230'){
			return;
		}
		 
		$l=new logfile;
		$l->payeer="\r\n\r\n\r\n".th::date()."\r\n".print_r($_POST, true);

		$config=Kohana::$config->load('secret.payeer');
		if (isset($_POST['m_operation_id']) && isset($_POST['m_sign'])){
			$m_key=$config['m_key'];
			$arHash = array($_POST['m_operation_id'],
							$_POST['m_operation_ps'],
							$_POST['m_operation_date'],
							$_POST['m_operation_pay_date'],
							$_POST['m_shop'],
							$_POST['m_orderid'],
							$_POST['m_amount'],
							$_POST['m_curr'],
							$_POST['m_desc'],
							$_POST['m_status'],
							$m_key);
			$sign_hash=strtoupper(hash('sha256', implode(':', $arHash)));
			if ($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success'){
				$id=(int) $_POST['m_orderid'];
				$p=new Model_Payment($id);
				if (!$p->loaded() or $p->gateway!='payeer'){
					$this->response->body("$id|error");
					$l->payeer="Payment nor exist or bad system ".$p->gateway;
					$l->payeer="Response: $id|error";
					return null;
				}
				
				if ($p->status!=PAY_NEW){
					$this->response->body("$id|success");
					$l->payeer="Status is ".$p->status;
					$l->payeer="Response: $id|success";
					return null;
				}
				
				$p->external_id=arr::get($_POST,'m_operation_id');
                $p->currency = arr::get($_POST, 'm_curr');
				$p->ps=arr::get($_POST,'m_operation_ps');
				$p->payed=time();
				$p->status=PAY_SUCCES;

				$created_ext=arr::get($_POST,'m_operation_date');
				$d=date_parse_from_format('d.M.Y H:i', $created_ext);
				$p->external_created= mktime($d['hour'],$d['minute'],$d['second'],$d['month'],$d['day'],$d['year']);
				
				$pay_ext=arr::get($_POST,'m_operation_pay_date');
				$d=date_parse_from_format('d.M.Y H:i', $pay_ext);
				$p->external_payed= mktime($d['hour'],$d['minute'],$d['second'],$d['month'],$d['day'],$d['year']);
				$p->account=arr::get($_POST,'client_account');
				$p->client_contact=arr::get($_POST,'client_email'); 
				
				$p->amount=(float) $_POST['m_amount'];
				
				$p->pay();
				
				$l->payeer=print_r($p->object(),true);
				$this->response->body($_POST['m_orderid'].'|success');
				$l->payeer="Response: {$_POST['m_orderid']}|success";
				return null;
			}
			$l->payeer="Response: {$_POST['m_orderid']}|error";
			$this->response->body($_POST['m_orderid'].'|error');
		}

		
		
	}
	
	
}

