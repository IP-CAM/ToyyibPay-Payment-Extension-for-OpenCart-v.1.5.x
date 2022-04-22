<?php

/**
 * toyyibPay OpenCart Plugin
 * 
 * @package Payment Gateway
 * @author toyyibPay Team
 * @version 2.0.0
 */
 
require_once __DIR__ . '/toyyibpay-api.php';

class ControllerPaymentToyyibPay extends Controller
{
    private function get_domain_forwebmaster()
    {
        return substr($_SERVER['HTTP_HOST'], 0, 3) == 'www' ? substr($_SERVER['HTTP_HOST'], 4) : $_SERVER['HTTP_HOST'];
    }

    protected function index()
    {
        $this->language->load('payment/toyyibpay');

        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $products = $this->cart->getProducts();
        foreach ($products as $product) {
            $localData['prod_desc'][] = $product['name'] . " x " . $product['quantity'];
        }

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['description'] = '';
        $this->data['amount'] = '';
        $this->data['mobile'] = '';
        $this->data['name'] = '';
        $this->data['email'] = '';
        $this->data['redirect_url'] = '';
        $this->data['callback_url'] = '';
        $this->data['action'] = $this->url->link('payment/toyyibpay/proceed', '', true);
				
        $_SESSION['name'] = $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
        $_SESSION['email'] = empty($order_info['email']) ? '' : $order_info['email'];
        $_SESSION['description'] = "Payment for Order " . $this->session->data['order_id'];
        $_SESSION['mobile'] = empty($order_info['telephone']) ? '' : $order_info['telephone'];
        $_SESSION['order_id'] = $this->session->data['order_id'];
        $_SESSION['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $_SESSION['redirect_url'] = $this->url->link('payment/toyyibpay/return_ipn', '', 'SSL');
        $_SESSION['callback_url'] = $this->url->link('payment/toyyibpay/callback_ipn', '', 'SSL');


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/toyyibpay.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/toyyibpay.tpl';
        } else {
            $this->template = 'default/template/payment/toyyibpay.tpl';
        }

        $this->render();
		
    }
	
    public function proceed()
    {
		$this->load->model('checkout/order');
		
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $api_key = $this->config->get('toyyibpay_api_key');
        $api_prod = $this->config->get('toyyibpay_api_environment');
        $category_code = $this->config->get('toyyibpay_category_code');
        $payment_channel = $this->config->get('toyyibpay_payment_channel');
        $payment_charge = $this->config->get('toyyibpay_payment_charge');
        $company_email = $this->config->get('toyyibpay_company_email');
        $company_phone = $this->config->get('toyyibpay_company_phone');

        if ($payment_charge == 0) {
            $payment_charge_on = '';
        } elseif ($payment_charge == 1) {
            $payment_charge_on = "0";
        } elseif ($payment_charge == 2) {
            $payment_charge_on = "1";
        } else {
            $payment_charge_on = "2";
        }

        $extra_email = $this->config->get('toyyibpay_extra_email');
	
        $billName = "Order " . $this->session->data['order_id'];
        $name = $_SESSION['name'];
        $email = $_SESSION['email'] ?: $company_email;
        $description = $_SESSION['description'];
        $mobile = preg_replace('/[^0-9]/', '', $_SESSION['mobile']) ?: $company_phone;
        $ext_ref_no = $_SESSION['order_id'];
        $amount = preg_replace("/[^0-9.]/", "", $_SESSION['amount']) * 100;
        $redirect_url = $_SESSION['redirect_url'];
        $callback_url = $_SESSION['callback_url'];

        unset($_SESSION['name']);
        unset($_SESSION['email']);
        unset($_SESSION['description']);
        unset($_SESSION['mobile']);
        unset($_SESSION['order_id']);
        unset($_SESSION['amount']);
        unset($_SESSION['redirect_url']);
        unset($_SESSION['callback_url']);
		
		$parameter = array(
			'userSecretKey'		        => trim($api_key),
			'categoryCode'		        => trim($category_code),
			'billName'			        => $billName,
			'billDescription'	        => $description,
			'billPriceSetting'	        => 1,
			'billPayorInfo'		        => 1, 
			'billAmount'		        => $amount, 
			'billReturnUrl'		        => $redirect_url,
			'billCallbackUrl'	        => $callback_url,
			'billExternalReferenceNo'   => $ext_ref_no,
			'billTo'			        => $name,
			'billEmail'			        => trim($email),
			'billPhone'			        => trim($mobile),
			'billSplitPayment'	        => 0,
			'billSplitPaymentArgs'      => '',
            'billPaymentChannel'        => $payment_channel,
            'billContentEmail'          => $extra_email,
            'billChargeToCustomer'      => $payment_charge_on,
            'billASPCode'               => 'aminoc1'
		);  

        $toyyibpay = new ToyyibPayAPI(trim($api_key),$api_prod);
		$createBill = $toyyibpay->createBill($parameter);

		if( $createBill['respStatus']===false ) {
            $toyyibpay->throwException( $createBill['respData'] );
            exit;
		}
		
		if ( empty($createBill['respData']['BillCode'])) {
			$toyyibpay->throwException( 'ERROR : BillCode not found!' );
            exit;
        }
        
		
		
		$tranID = $createBill['respData']['BillCode'];
		$orderHistNotes = "Time (O): " . date(DATE_ATOM) . " BillCode:" . $tranID . " Status: Pending";
		
		$order_status_id = $this->config->get('toyyibpay_order_status_id');
		$this->model_checkout_order->confirm($ext_ref_no, $order_status_id);
		$this->model_checkout_order->update($ext_ref_no, $order_status_id, $orderHistNotes, false);
		
        header('Location: ' . $createBill['respData']['BillURL'] );
		
    }

    public function return_ipn()
    {
        $this->load->model('checkout/order');
        /*
         * Get Data. Die if input is tempered or X Signature not enabled
         */
		 
        $api_key = $this->config->get('toyyibpay_api_key');
		$api_prod = $this->config->get('toyyibpay_api_environment');
		
		$toyyibpay = new ToyyibPayAPI(trim($api_key),$api_prod);
        $data = $toyyibpay->getTransactionData();
        
		$tranID = $data['billcode'];
        $orderid = $data['order_id'];
        $invoice = $data['transaction_id'];
        $status = $data['paid'];
        $amount = $data['amount'];
		
		$parameter = array('code' => $invoice);
		$invoiceSecure = $toyyibpay->toChange($parameter);
		$invoiceSecure = $invoiceSecure['respData']['code'];
		$urlLink = $toyyibpay->requery($invoiceSecure); 
        		
        $order_info = $this->model_checkout_order->getOrder($orderid); // orderid
        $order_status_id = $this->config->get('toyyibpay_order_status_id');
		
		

        if ($status == '1') {
			$orderHistNotes = "Time (R): " . date(DATE_ATOM) . " BillCode:" . $tranID . " Invoice No:" . $invoice . " Status: " . $data['status_name'];
            $order_status_id = $this->config->get('toyyibpay_success_status_id');
            $goTo = $this->url->link('checkout/success');
			$this->cart->clear();
        } else if($status == '2') {
			$orderHistNotes = "Time (R): " . date(DATE_ATOM) . " BillCode:" . $tranID . " Invoice No:" . $invoice . " Status: " . $data['status_name'];
			$order_status_id = $this->config->get('toyyibpay_order_status_id');
			$this->session->data['error'] = 'Payment pending. Please contact admin if this message is wrong.';
            $goTo = $this->url->link('checkout/checkout');

		} else {
			$orderHistNotes = "Time (R): " . date(DATE_ATOM) . " BillCode:" . $tranID . " Invoice No:" . $invoice . " Status: " . $data['status_name'];
            $order_status_id = $this->config->get('toyyibpay_failed_status_id');
            $this->session->data['error'] = 'Payment failed, please try again. Please contact admin if this message is wrong.';
            $goTo = $this->url->link('checkout/checkout');
        }

        if (!$order_info['order_status']) {
            $this->model_checkout_order->confirm($orderid, $order_status_id);
        }

        /*
         * Prevent same order status id from adding more than 1 update
         */
		if ($order_status_id != $order_info['order_status_id'] || $data['status_name'] == 'Pending') {
            $this->model_checkout_order->update($orderid, $order_status_id, $orderHistNotes, false);
		}


        if (!headers_sent()) {
            header('Location: ' . $goTo);
        } else {
            echo "If you are not redirected, please click <a href=" . '"' . $goTo . '"' . " target='_self'>Here</a><br />"
            . "<script>location.href = '" . $goTo . "'</script>";
        }
    }
	
	
    /*     * ***************************************************
     * Callback with IPN(Instant Payment Notification)
     * **************************************************** */

    public function callback_ipn()
    {
        $this->load->model('checkout/order');
        /*
         * Get Data. Die if input is tempered or X Signature not enabled
         */
		 
        $api_key = $this->config->get('toyyibpay_api_key');
		$api_prod = $this->config->get('toyyibpay_api_environment');
		
        $toyyibpay = new ToyyibPayAPI(trim($api_key),$api_prod);
		$data = $toyyibpay->getTransactionData();
		
        $tranID = $data['billcode'];
        $orderid = $data['order_id'];
        $status = $data['paid'];
        $invoice = $data['transaction_id'];
        $amount = $data['amount'];
		//$orderHistNotes = "Time (C): " . date(DATE_ATOM) . " BillCode:" . $tranID . " Invoice No:" . $invoice . " Status:" . $data['status_name'];
		
        $order_info = $this->model_checkout_order->getOrder($orderid); // orderid
        $order_status_id = $this->config->get('toyyibpay_order_status_id');
		
		$parameter = array('code' => $invoice);
		$invoiceSecure = $toyyibpay->toChange($parameter);
		$invoiceSecure = $invoiceSecure['respData']['code'];
		$urlLink = $toyyibpay->requery($invoiceSecure); 

		
		if ($status == '1') {
			$orderHistNotes = "Time (C): " . date(DATE_ATOM) . " BillCode:" . $tranID . " Invoice No:" . $invoice . " Status: " . $data['status_name'];
            $order_status_id = $this->config->get('toyyibpay_success_status_id');
            $goTo = $this->url->link('checkout/success');
			$this->cart->clear();
        } else if($status == '2') {
			$orderHistNotes = "Time (C): " . date(DATE_ATOM) . " BillCode:" . $tranID . " Invoice No:" . $invoice . " Status: <a href='$urlLink' target='_blank'> " . $data['status_name']. '</a>';
			$order_status_id = $this->config->get('toyyibpay_order_status_id');
			$this->session->data['error'] = 'Payment pending. Please contact admin if this message is wrong.';
            $goTo = $this->url->link('checkout/checkout');

		} else {
			$orderHistNotes = "Time (C): " . date(DATE_ATOM) . " BillCode:" . $tranID . " Invoice No:" . $invoice . " Status: " . $data['status_name'];
            $order_status_id = $this->config->get('toyyibpay_failed_status_id');
            $this->session->data['error'] = 'Payment failed, please try again. Please contact admin if this message is wrong.';
            $goTo = $this->url->link('checkout/checkout');
        }
	

        if (!$order_info['order_status']) {
            $this->model_checkout_order->confirm($orderid, $order_status_id);
        }

        /*
         * Prevent same order status id from adding more than 1 update
         */
        if ($order_info['order_status'] == 'Pending') {
            $this->model_checkout_order->update($orderid, $order_status_id, $orderHistNotes, false);
        }
        exit('Callback Success');
    }
	
}