<?php

define( 'DOING_AJAX', true );
if ( ! defined( 'WP_ADMIN' ) ) {
	define( 'WP_ADMIN', true );
}

/** Load WordPress Bootstrap */
require_once( dirname( dirname( __FILE__ ) ) . '/../../../wp-load.php' );

/** Allow for cross-domain requests (from the front end). */
send_origin_headers();

/** Load WordPress Administration APIs */
require_once( ABSPATH . 'wp-admin/includes/admin.php' );

$wp_tnx_id = $_POST['wp_tnx_id'];

$transactional_data = se_get_all_transactional_data($wp_tnx_id);
$itemName = $transactional_data['event']->post_title .' - '. $transactional_data['ticket_name'];
$itemAmount = $transactional_data['transaction_amount'];
// $itemAmount = '2.5';

$getway_email = get_option('se_payment_paypal_getway_email') ? ''.get_option('se_payment_paypal_getway_email').'' : '';

// PayPal settings. Change these to your account details and the relevant URLs
// for your site.
$paypalConfig = [
	'email' => $getway_email,
	'return_url' => esc_url( home_url( '/' ) ).'/se-confirm/?tnx='.$wp_tnx_id,
	'cancel_url' => esc_url( home_url( '/' ) ).'/se-confirm/?tnx='.$wp_tnx_id,
	'notify_url' => plugins_url('',__FILE__) .'/'. pathinfo(__FILE__, PATHINFO_FILENAME) . '.php' // lilnk of the current file
];

$paypalUrl = get_option('se_payment_paypal_sendbox') == 'enabled' ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';


// Include Functions
require 'functions.php';

// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {

	// code for request

	// Grab the post data so that we can set up the query string for PayPal.
	// Ideally we'd use a whitelist here to check nothing is being injected into
	// our post data.
	$data = [];
	foreach ($_POST as $key => $value) {
		$data[$key] = stripslashes($value);
	}

	// Set the PayPal account.
	if(!empty($paypalConfig['email']))
		$data['business'] = $paypalConfig['email'];
	else {
		print_r('Missing Receiver Email. Please contact admin to go "SE Setting" of admin panel and set "Gateway Account Email:". Thanks for help.');
		exit();
	}

	// Set the PayPal return addresses.
	$data['return'] = stripslashes($paypalConfig['return_url']);
	$data['cancel_return'] = stripslashes($paypalConfig['cancel_url']);
	$data['notify_url'] = stripslashes($paypalConfig['notify_url']);

	// Set the details about the product being purchased, including the amount
	// and currency so that these aren't overridden by the form data.
	$data['item_name'] = $itemName;
	$data['amount'] = $itemAmount;
	$data['currency_code'] = 'USD';

	// Add any custom fields for the query string.
	$data['custom'] = $wp_tnx_id;

	// Build the query string from the data.
	$queryString = http_build_query($data);
	// echo($paypalUrl . '?' . $queryString);
	
	// Redirect to paypal IPN
	header('location:' . $paypalUrl . '?' . $queryString);
	exit();

} else {

	// Handle the PayPal response.
	$message = "Response: " . print_r( $_POST, true );

	// Assign posted variables to local data array.
	$data = [
		'item_name' => $_POST['item_name'],
		'item_number' => $_POST['item_number'],
		'payment_status' => $_POST['payment_status'],
		'payment_amount' => $_POST['mc_gross'],
		'payment_currency' => $_POST['mc_currency'],
		'txn_id' => $_POST['txn_id'],
		'receiver_email' => $_POST['receiver_email'],
		'payer_email' => $_POST['payer_email'],
		'custom' => $_POST['custom'],
	];

    // We need to verify the transaction comes from PayPal and check we've not
    // already processed the transaction before adding the payment to our
    // database.
    if (verifyTransaction($_POST) ) { //  && checkTxnid($data['txn_id'])) {

        global $transaction_complete_index;


		$transaction_id = $_POST['custom'];
		$transaction_gateway_id = $_POST['txn_id'];


		$prev_transaction_data = se_get_transaction_formated_meta($transaction_id);

		$transaction_data[0] = $prev_transaction_data['registration_id'];
		$transaction_data[1] = 'se_payment_method-paypal';
		$transaction_data[2] = $prev_transaction_data['transaction_amount'];
		$transaction_data[3] = $transaction_complete_index;
		$transaction_data[4] = $prev_transaction_data['transaction_event_id'];
		$transaction_data[5] = $prev_transaction_data['transaction_ticket_id'];
		$transaction_data[6] = $transaction_gateway_id;

		update_post_meta( $transaction_id, 'transaction_info_meta_key', $transaction_data );  

		$message .= 'success result: '. se_send_transaction_success_mail($transaction_id);

		$relatedTransactions = se_get_transactions_of_registration( $prev_transaction_data['registration_id'] );

		foreach ($relatedTransactions as $relatedTransactionId => $relatedTransactionInfo) { 

			if ($relatedTransactionId != $transaction_id) {

				$message .= 'pending result: '. se_send_transaction_mail($relatedTransactionId);
			}
		
		}

		// mail('scoutsolution@gmail.com', $_POST['txn_id'].' - PAYPAL POST RESPONSE', $message);

	}
	
}

?>