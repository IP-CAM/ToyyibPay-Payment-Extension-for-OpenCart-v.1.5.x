<?php

/**
 * toyyibPay OpenCart Plugin
 * 
 * @package Payment Gateway
 * @author toyyibPay Team
 * @version 2.0.0
 */
class ControllerPaymentToyyibPay extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('payment/toyyibpay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_setting_setting->editSetting('toyyibpay', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');

		$this->data['entry_company_email'] = $this->language->get('entry_company_email');
		$this->data['entry_company_phone'] = $this->language->get('entry_company_phone');
		 
        $this->data['entry_api_key'] = $this->language->get('entry_api_key');
		$this->data['entry_api_environment'] = $this->language->get('entry_api_environment');
        $this->data['entry_category_code'] = $this->language->get('entry_category_code');
        $this->data['entry_payment_channel'] = $this->language->get('entry_payment_channel');
        $this->data['entry_payment_charge'] = $this->language->get('entry_payment_charge');
        
        $this->data['entry_minlimit'] = $this->language->get('entry_minlimit');
        $this->data['entry_extra_email'] = $this->language->get('entry_extra_email');
        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_pending_status'] = $this->language->get('entry_pending_status');
        $this->data['entry_success_status'] = $this->language->get('entry_success_status');
        $this->data['entry_failed_status'] = $this->language->get('entry_failed_status');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->data['tab_general'] = $this->language->get('tab_general');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        if (isset($this->error['account'])) {
            $this->data['error_api_key'] = $this->error['account'];
        } else {
            $this->data['error_api_key'] = '';
        }

        if (isset($this->error['api_environment'])) {
            $this->data['error_api_environment'] = $this->error['api_environment'];
        } else {
            $this->data['error_api_environment'] = '';
        }

        if (isset($this->error['secret'])) {
            $this->data['error_category_code'] = $this->error['secret'];
        } else {
            $this->data['error_category_code'] = '';
        }

        if (isset($this->error['payment_channel'])) {
            $this->data['error_payment_channel'] = $this->error['payment_channel'];
        } else {
            $this->data['error_payment_channel'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/toyyibpay', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('payment/toyyibpay', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');


		if (isset($this->request->post['toyyibpay_company_email'])) {
            $this->data['toyyibpay_company_email'] = $this->request->post['toyyibpay_company_email'];
        } else {
            $this->data['toyyibpay_company_email'] = $this->config->get('toyyibpay_company_email');
        }
		
		if (isset($this->request->post['toyyibpay_company_phone'])) {
            $this->data['toyyibpay_company_phone'] = $this->request->post['toyyibpay_company_phone'];
        } else {
            $this->data['toyyibpay_company_phone'] = $this->config->get('toyyibpay_company_phone');
        }
		
        if (isset($this->request->post['toyyibpay_api_key'])) {
            $this->data['toyyibpay_api_key'] = $this->request->post['toyyibpay_api_key'];
        } else {
            $this->data['toyyibpay_api_key'] = $this->config->get('toyyibpay_api_key');
        }
		
        if (isset($this->request->post['toyyibpay_api_environment'])) {
            $this->data['toyyibpay_api_environment'] = $this->request->post['toyyibpay_api_environment'];
        } else {
            $this->data['toyyibpay_api_environment'] = $this->config->get('toyyibpay_api_environment');
        }

        if (isset($this->request->post['toyyibpay_category_code'])) {
            $this->data['toyyibpay_category_code'] = $this->request->post['toyyibpay_category_code'];
        } else {
            $this->data['toyyibpay_category_code'] = $this->config->get('toyyibpay_category_code');
        }
        
        if (isset($this->request->post['toyyibpay_payment_channel'])) {
            $this->data['toyyibpay_payment_channel'] = $this->request->post['toyyibpay_payment_channel'];
        } else {
            $this->data['toyyibpay_payment_channel'] = $this->config->get('toyyibpay_payment_channel');
        }

        if (isset($this->request->post['toyyibpay_payment_charge'])) {
            $this->data['toyyibpay_payment_charge'] = $this->request->post['toyyibpay_payment_charge'];
        } else {
            $this->data['toyyibpay_payment_charge'] = $this->config->get('toyyibpay_payment_charge');
        }
        
        if (isset($this->request->post['toyyibpay_minlimit'])) {
            $this->data['toyyibpay_minlimit'] = $this->request->post['toyyibpay_minlimit'];
        } else {
            $this->data['toyyibpay_minlimit'] = $this->config->get('toyyibpay_minlimit');
        }

        if (isset($this->request->post['toyyibpay_sandbox'])) {
            $this->data['toyyibpay_sandbox'] = $this->request->post['toyyibpay_sandbox'];
        } else {
            $this->data['toyyibpay_sandbox'] = $this->config->get('toyyibpay_sandbox');
        }

        if (isset($this->request->post['toyyibpay_extra_email'])) {
            $this->data['toyyibpay_extra_email'] = $this->request->post['toyyibpay_extra_email'];
        } else {
            $this->data['toyyibpay_extra_email'] = $this->config->get('toyyibpay_extra_email');
        }

        if (isset($this->request->post['toyyibpay_order_status_id'])) {
            $this->data['toyyibpay_order_status_id'] = $this->request->post['toyyibpay_order_status_id'];
        } else {
            $this->data['toyyibpay_order_status_id'] = $this->config->get('toyyibpay_order_status_id');
        }

        if (isset($this->request->post['toyyibpay_pending_status_id'])) {
            $this->data['toyyibpay_pending_status_id'] = $this->request->post['toyyibpay_pending_status_id'];
        } else {
            $this->data['toyyibpay_pending_status_id'] = $this->config->get('toyyibpay_pending_status_id');
        }

       if (isset($this->request->post['toyyibpay_success_status_id'])) {
            $this->data['toyyibpay_success_status_id'] = $this->request->post['toyyibpay_success_status_id'];
        } else {
            $this->data['toyyibpay_success_status_id'] = $this->config->get('toyyibpay_success_status_id');
        }

        if (isset($this->request->post['toyyibpay_failed_status_id'])) {
            $this->data['toyyibpay_failed_status_id'] = $this->request->post['toyyibpay_failed_status_id'];
        } else {
            $this->data['toyyibpay_failed_status_id'] = $this->config->get('toyyibpay_failed_status_id');
        }

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['toyyibpay_status'])) {
            $this->data['toyyibpay_status'] = $this->request->post['toyyibpay_status'];
        } else {
            $this->data['toyyibpay_status'] = $this->config->get('toyyibpay_status');
        }

        if (isset($this->request->post['toyyibpay_sort_order'])) {
            $this->data['toyyibpay_sort_order'] = $this->request->post['toyyibpay_sort_order'];
        } else {
            $this->data['toyyibpay_sort_order'] = $this->config->get('toyyibpay_sort_order');
        }

        $this->layout = 'common/layout';
        $this->template = 'payment/toyyibpay.tpl';
        $this->children = array(
            'common/header',
            'common/footer',
        );

        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'payment/toyyibpay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['toyyibpay_api_key']) {
            $this->error['account'] = $this->language->get('error_api_key');
        }

        if (!$this->request->post['toyyibpay_category_code']) {
            $this->error['secret'] = $this->language->get('error_category_code');
        }

        //Akan datang, masukkan code untuk verify API Key
        //Dan Category Code Disini
        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

}
