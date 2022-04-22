<?php
if (!class_exists('ToyyibPayAPI')) {

	class ToyyibPayAPI
	{
		private $api_key;

		private $process;
		public $is_production;
		public $url;

		public $header;

		const TIMEOUT = 10; //10 Seconds
		const PRODUCTION_URL = 'https://toyyibpay.com/';
		const STAGING_URL = 'https://dev.toyyibpay.com/';

		public function __construct($api_key, $is_production)
		{
			if ($is_production == '') $this->is_production = true;
			else $this->is_production = $is_production === true || $is_production === 1 || $is_production === '1' ? true : false;

			$this->api_key = $api_key;
			$this->header = $api_key . ':';
		}

		public function setMode()
		{
			if ($this->is_production) {
				$this->url = self::PRODUCTION_URL;
			} else {
				$this->url = self::STAGING_URL;
			}
			return $this;
		}

		public function throwException($message)
		{

			echo "<script> alert('" . trim(addslashes($message)) . "'); </script>";
			echo "<h3>" . addslashes($message) . "</h3>";
		}


		public function createBill($parameter)
		{
			/* Email must be set */
			if (empty($parameter['billEmail'])) {
				$this->throwException("Email must be set! ");
			}
			/* Mobile must be set */
			if (empty($parameter['billPhone'])) {
				$this->throwException("Phone number must be set! ");
			}
			if (empty($parameter['billTo'])) {
				$parameter['billTo'] = 'Payer Name Unavailable';
			}
			if (empty($parameter['categoryCode'])) {
				$this->throwException("Category Code Not Found! ");
			}
			
			/* Create Bills */
			$this->setActionURL('CREATEBILL');
			$bill = $this->toArray($this->submitAction($parameter));
			$billdata = $this->setPaymentURL($bill);

			
			return $billdata;
	
		}

		public function setPaymentURL($bill)
		{
			$return = $bill;
			if ($bill['respStatus']) {
				if (isset($bill['respData'][0]['BillCode'])) {
					$this->setActionURL('PAYMENT', $bill['respData'][0]['BillCode']);
					$bill['respData'][0]['BillURL'] = $this->url;
				}
				$return = array('respStatus' => $bill['respStatus'], 'respData' => $bill['respData'][0]);

			}

			return $return;
		}

		public function checkBill($parameter)
		{
			$this->setActionURL('CHECKBILL');
			$checkData = $this->toArray($this->submitAction($parameter));
			$checkData['respData'] = $checkData['respData'][0];

			return $checkData;
		}

		public function deleteBill($parameter)
		{
			$this->setActionURL('DELETEBILL');
			$checkData = $this->toArray($this->submitAction($parameter));
			$checkData['respData'] = $checkData['respData'][0];

			return $checkData;
		}

		public function setUrlQuery($url, $data)
		{
			if (!empty($url)) {
				if (count(explode("?", $url)) > 1)
					$url = $url . '&' . http_build_query($data);
				else
					$url = $url . '?' . http_build_query($data);
			}
			return $url;
		}

		public function getTransactionData()
		{
			if (isset($_GET['billcode']) && isset($_GET['transaction_id']) && isset($_GET['order_id']) && isset($_GET['status_id'])) {

				$data = array(
					'status_id' => $_GET['status_id'],
					'billcode' => $_GET['billcode'],
					'order_id' => $_GET['order_id'],
					'msg' => $_GET['msg'],
					'transaction_id' => $_GET['transaction_id']
				);
				$type = 'redirect';
			} elseif (isset($_POST['refno']) && isset($_POST['status']) && isset($_POST['amount'])) {

				$data = array(
					'status_id' => $_POST['status'],
					'billcode' => $_POST['billcode'],
					'order_id' => $_POST['order_id'],
					'amount' => $_POST['amount'],
					'reason' => $_POST['reason'],
					'transaction_id' => $_POST['refno']
				);
				$type = 'callback';
			} else {
				return false;
			}

			$checkAction = ($type == 'redirect' ? 'RETURNREDIRECT' : ($type == 'callback' ? 'RETURNCALLBACK' : ''));


			if ($type === 'redirect') {
				//check bill
				$parameter = array(
					'billCode' => $data['billcode'],
					'billExternalReferenceNo' => $data['order_id']
				);
				$checkbill = $this->checkBill($parameter);
				if ($checkbill['respStatus']) {
					if ($checkbill['respData']['billpaymentStatus'] != $data['status_id']) {
						$data['status_id'] = $checkbill['respData']['billpaymentStatus'];
						//$data['status_id'] = 2;
					}
					$data['amount'] = $checkbill['respData']['billpaymentAmount'];
				} else {

				}
			}
			
			//$data['status_id'] = 2;

			//$data['paid'] = $data['status_id'] === '1' ? true : false; /* Convert paid status to boolean */
			$data['paid'] = $data['status_id'];

			if ($data['status_id'] == '1') $data['status_name'] = 'Success';
			else if ($data['status_id'] == '2') $data['status_name'] = 'Pending';
			else $data['status_name'] = 'Unsuccessful';

			$data['type'] = $type;
			return $data;
		}

		public function setActionURL($action, $id = '')
		{
			$this->setMode();
			$this->action = $action;

			if ($this->action == 'PAYMENT') {
				$this->url .= $id;
			} else if ($this->action == 'CREATEBILL') {
				$this->url .= 'index.php/api/createBill';
			} else if ($this->action == 'CHECKBILL') {
				$this->url .= 'index.php/api/getBillTransactions';
			} else if ($this->action == 'DELETEBILL') {
				$this->url .= 'index.php/api/getBillTransactions';
			} else if ($this->action == 'CHANGECODE') {
				$this->url .= 'index.php/api/changeCode';
			} else if ($this->action == 'REQUERY') {
				$this->url .= 'index.php/api/callStatus?code=';
			} else {
				$this->throwException('URL Action not exist');
			}

			return $this;
		}

		public function submitAction($data = '')
		{
			$this->process = curl_init();
			curl_setopt($this->process, CURLOPT_HEADER, 0);
			curl_setopt($this->process, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($this->process, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($this->process, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($this->process, CURLOPT_TIMEOUT, self::TIMEOUT);
			curl_setopt($this->process, CURLOPT_USERPWD, $this->header);

			curl_setopt($this->process, CURLOPT_URL, $this->url);
			curl_setopt($this->process, CURLOPT_POSTFIELDS, http_build_query($data));
			if ($this->action == 'DELETE') {
				curl_setopt($this->process, CURLOPT_CUSTOMREQUEST, "DELETE");
			}
			$response = curl_exec($this->process);
			$httpStatusCode  = curl_getinfo($this->process, CURLINFO_HTTP_CODE);
			curl_close($this->process);

			if ($httpStatusCode == 200) {
				$respStatus = true;
				if (trim($response) == '[FALSE]') {
					$respStatus = false;
					$response = 'API_ERROR ' . trim($response) . ' : Please check your request data with Toyyibpay Admin';
				} else if (trim($response) == '[KEY-DID-NOT-EXIST]') {
					$respStatus = false;
					$response = 'API_ERROR ' . trim($response) . ' : Please check your api key.';
				}

				if (trim($response) == '') {
					$respStatus = false;
					$response = 'API_ERROR : No Response Data From Toyyibpay.';
				}
			} else {
				$respStatus = false;
				$response = 'API_ERROR ' . $httpStatusCode . ' : Cannot Connect To ToyyibPay Server.';
			}

			$return = array('respStatus' => $respStatus, 'respData' => $response);

			return $return;
		}

		public function toArray($json)
		{
			if (is_string($json['respData']) && is_array(json_decode($json['respData'], true))) { //check json ke x
				return array('respStatus' => $json['respStatus'], 'respData' => json_decode($json['respData'], true));
			} else {
				return array('respStatus' => $json['respStatus'], 'respData' => $json['respData']);
			}
		}
		
		public function toChange($parameter)
		{
			$this->setActionURL('CHANGECODE');
			$changeData = $this->toArray($this->submitAction($parameter));
			$changeData['respData'] = $changeData['respData'][0];

			return $changeData;
			
		}
		
		public function requery($invoiceNo)
		{
			$this->setMode();
			$url = $this->url.'index.php/api/callStatus?code='.$invoiceNo;
			
			return $url;
			
		}
		
	} //close class ToyyibPayAPI


}