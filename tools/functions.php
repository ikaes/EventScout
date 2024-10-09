<?php 

require SE_DIR.'/helpers/admin-helpers.php';
require SE_DIR.'/helpers/events-helper.php';
require SE_DIR.'/helpers/tickets-helper.php';
require SE_DIR.'/helpers/registrations-helper.php';
require SE_DIR.'/helpers/transaction-helper.php';
require SE_DIR.'/helpers/mail-helper.php';
require SE_DIR.'/helpers/discount-helper.php';

function se_get_payment_method_title($payment_method_value)
{	
	$payment_title = '';
	if ($payment_method_value == 'se_payment_method-cc')	
		$payment_title = 'Credit Card';
	else if ($payment_method_value == 'se_payment_method-paypal')
		$payment_title = 'Paypal';
	else if ($payment_method_value == 'se_payment_method-check')
		$payment_title = 'Check';
	return $payment_title;
}

function se_get_payment_check_address()
{
	return get_option('se_payment_check_address') ?  get_option('se_payment_check_address') : "Rome Art Workshops, Inc. <br>\n 1427 25th Street #2 <br>\n Santa Monica, CA 90404";
}
function se_get_payment_processing_notice()
{
	return get_option('se_payment_processing_notice') ?  get_option('se_payment_processing_notice') : "All Credit Card and PayPal transactions will have a 3% processing fee added to the total.";
}

function se_get_payment_processing_percent()
{
	return get_option('se_payment_processing_percent') ?  get_option('se_payment_processing_percent') : '0';
}