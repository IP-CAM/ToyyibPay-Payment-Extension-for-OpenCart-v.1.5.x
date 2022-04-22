<?php

/**
 * toyyibPay OpenCart Plugin
 * 
 * @package Payment Gateway
 * @author toyyibPay Team
 * @version 2.0.0
 */
// Versioning
$_['toyyibpay_ptype'] = "OpenCart";
$_['toyyibpay_pversion'] = "2.0.0";

// Heading
$_['heading_title'] = 'toyyibPay Payment Gateway';

// Text 
$_['text_payment'] = 'Payment';
$_['text_success'] = 'Success: You have modified toyyibPay Payment Gateway account details!';
$_['text_toyyibpay'] = '<a onclick="window.open(\'https://toyyibpay.com/\');" style="text-decoration:none;"><img src="view/image/payment/toyyibpay-logo.png" alt="toyyibPay Online Payment Gateway" title="toyyibPay Malaysia Online Payment Gateway" style="border: 0px solid #EEEEEE;" height=25 width=94/></a>';

// Entry
$_['entry_company_email'] = 'Company Email :<br /><span class="help">[Optional]</span>';
$_['entry_company_phone'] = 'Company Phone :<br /><span class="help">[Optional]</span>';

$_['entry_api_key'] = 'User Secret Key :<br /><span class="help">[Required] Please refer to your toyyibPay account.</span>';
$_['entry_category_code'] = 'Category Code :<br /><span class="help">[Required] Please refer to your toyyibPay account.</span>';
$_['entry_api_environment'] = 'API Environment :<br /><span class="help">[Important] Production / Sandbox Mode.</span>';
$_['entry_payment_channel'] = 'Payment Channel :<br /><span class="help">[Important] Choose which payment channel your customer can use.</span>';
$_['entry_payment_charge'] = 'Payment Charge :<br /><span class="help">[Important] Impose the transaction charge on transaction amount or add extra to the customer.</span>';

$_['entry_extra_email'] = 'Extra e-mail to customer :<br /><span class="help">[Optional] Edit here to send extra e-mail from toyyibPay or delete this to not send extra e-mail.</span>';
$_['entry_order_status'] = 'Order Status :';
$_['entry_pending_status'] = 'Pending Status :';
$_['entry_success_status'] = 'Success Status :';
$_['entry_failed_status'] = 'Failed Status :';
$_['entry_status'] = 'Status :';
$_['entry_sort_order'] = 'Sort Order :';
$_['entry_minlimit'] = 'Minimum Limit : RM<br /><span class="help">Please input 0 if you do not want to use this feature.</span>';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify toyyibPay Malaysia Online Payment Gateway!';
$_['error_api_key'] = '<b>toyyibPay API Key</b> Required!';
$_['error_category_code'] = '<b>toyyibPay Category Code</b> Required!';
$_['error_api_environment'] = '<b>toyyibPay API Environment</b> Required!';
$_['error_payment_channel'] = '<b>toyyibPay Payment Channel</b> Required!';
$_['error_settings'] = 'toyyibPay API Key and Category Code  mismatch, contact support@toyyibpay.com to assist.';
$_['error_status'] = 'Unable to connect toyyibPay API.';