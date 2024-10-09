<?php 



function save_new_event_registration($normalized_data)

{

	$registration_title = uniqid('');



	$registration_id = wp_insert_post(array (

		'post_type' => SE_REGISTRATION_POST_TYPE,

		'post_title' => $registration_title,

		'post_content' => '',

		'post_status' => 'publish',

		'comment_status' => 'closed',   // if you prefer

		'ping_status' => 'closed',      // if you prefer

	));



	$personal_info_meta_data[0] = $normalized_data['se_first_name']; // customer_first_name_field

	$personal_info_meta_data[1] = $normalized_data['se_last_name']; // customer_last_name_field

	$personal_info_meta_data[2] = $normalized_data['se_email']; // customer_email_field

	$personal_info_meta_data[3] = $normalized_data['se_street_address']; // customer_street_address_field

	$personal_info_meta_data[4] = $normalized_data['se_city']; // customer_city_field

	$personal_info_meta_data[5] = $normalized_data['se_state']; // customer_state_field

	$personal_info_meta_data[6] = $normalized_data['se_postal']; // customer_postal_field

	$personal_info_meta_data[7] = $normalized_data['raw-country']; // customer_country_field

	$personal_info_meta_data[8] = $normalized_data['se_phone']; // customer_phone_field

	$personal_info_meta_data[9] = $normalized_data['se_emrgnc_contact']; // customer_emrgnc_contact_field

	$personal_info_meta_data[10] = $normalized_data['se_emrgnc_rel_contact']; // customer_emrgnc_rel_contact_field

	$personal_info_meta_data[11] = $normalized_data['se_emrgnc_phone']; // customer_emrgnc_phone_field

	$personal_info_meta_data[12] = $normalized_data['se_emrgnc_email']; // customer_emrgnc_email_field

	$personal_info_meta_data[13] = $normalized_data['se_emrgnc_promotion']; // customer_emrgnc_promotion_field

	$personal_info_meta_data[14] = $normalized_data['se_citizen']; // citizen_field

	$personal_info_meta_data[15] = $normalized_data['se_birthday']; // birthday_field

	$personal_info_meta_data[16] = $normalized_data['se_birth_place']; // birth_place_field

	$personal_info_meta_data[17] = $normalized_data['se_pp_name']; // pp_name_field

	$personal_info_meta_data[18] = $normalized_data['se_pp_issue']; // pp_issue_field

	$personal_info_meta_data[19] = $normalized_data['se_pp_exp_date']; // pp_exp_date_field

	$personal_info_meta_data[20] = $normalized_data['se_gender']; // gender_field



	update_post_meta( $registration_id, 'se_reg_personal_info_meta_key', $personal_info_meta_data );   



	$reg_info_meta_data[0] = $normalized_data['se_event_deposit'];

	$reg_info_meta_data[1] = ($normalized_data['se_event_deposit'] == '1' && $normalized_data['payment-method'] == 'se_payment_method-cc') ? '1' : '0'; // registration_status_field

	$reg_info_meta_data[2] = $normalized_data['raw-event-select']; // static event id

	$reg_info_meta_data[3] = $normalized_data['raw-ticket-select']; // static event ticket id

	$reg_info_meta_data[4] = $normalized_data['se_ticket_price']; // static event ticket price



	update_post_meta( $registration_id, 'se_registration_info_meta_key', $reg_info_meta_data ); 



	return $registration_id;

}





function save_new_event_transaction($normalized_data, $registration_id) {



	$transaction_title = uniqid('');



	$transaction_id = wp_insert_post(array (

		'post_type' => SE_TRANSACTION_POST_TYPE,

		'post_title' => $transaction_title,

		'post_content' => '',

		'post_status' => 'publish',

		'comment_status' => 'closed',   // if you prefer

		'ping_status' => 'closed',      // if you prefer

	));





	$transaction_data[0] = $registration_id;	// event_registration_id_field

	$transaction_data[1] = $normalized_data['payment-method'];	// payment_method_field



	$total_price = floatval($normalized_data['se_ticket_price']);

	$calculated_price = $total_price / 2 ;



	$str_calc_price = (string)( number_format($calculated_price,2,'.', '') );



	$transaction_amount = ($normalized_data['se_event_deposit'] == '1') ? $str_calc_price : $normalized_data['se_ticket_price'];

	$transaction_status = '0';



	$transaction_data[2] = $transaction_amount;	// transaction_amount_field

	$transaction_data[3] = $transaction_status;	// transaction_status_field

	$transaction_data[4] = $normalized_data['raw-event-select'];	// se_transaction_event_field

	$transaction_data[5] = $normalized_data['raw-ticket-select'];	// se_transaction_ticket_field

	$transaction_data[6] = "";	// transaction_getway_id_field



    update_post_meta( $transaction_id, 'transaction_info_meta_key', $transaction_data );  



    return $transaction_id; 

	

}



function se_create_registration_with_transactons($normalized_data)

{

	global $se_registrationStatusArr;

	global $transaction_complete_index;

	global $registration_complete_index;



	$transaction_id2 = $transactional_data2 ='';

	// $result = $normalized_data;



	$registration_id = save_new_event_registration($normalized_data);

	$transaction_id  = save_new_event_transaction($normalized_data, $registration_id);





	$result = $transactional_data  = se_get_all_transactional_data($transaction_id);



	if ($normalized_data['se_event_deposit'] == '1') {

		$transaction_id2  = save_new_event_transaction($normalized_data, $registration_id);

		$transactional_data2  = se_get_all_transactional_data($transaction_id2);



		$result['transaction2'] = $transactional_data2['transaction'];

	}



	$transaction_title2 = ($normalized_data['se_event_deposit'] == '1') ? get_post($transaction_id2)->post_title : '';





	$result['transaction_status'] = SE_TNX_STATUS_PENDING;

	$result['transaction_status2'] = SE_TNX_STATUS_PENDING;



	if($normalized_data['payment-method'] == 'se_payment_method-cc') {





		$prev_transaction_data = se_get_transaction_formated_meta($transaction_id);



		$transaction_data[0] = $prev_transaction_data['registration_id'];

		$transaction_data[1] = $prev_transaction_data['payment_method'];

		$transaction_data[2] = $prev_transaction_data['transaction_amount'];

		$transaction_data[3] = $transaction_complete_index;;

		$transaction_data[4] = $prev_transaction_data['transaction_event_id'];

		$transaction_data[5] = $prev_transaction_data['transaction_ticket_id'];

		$transaction_data[6] = $normalized_data['getway']['tnx_id'];



		$is_update = update_post_meta( $transaction_id, 'transaction_info_meta_key', $transaction_data );  

		

		if ($is_update) {

			

			$result['transaction_status'] = SE_TNX_STATUS_PAID;

			

			if($normalized_data['se_event_deposit'] != '1') 

				$result['registration_status'] = $se_registrationStatusArr[$registration_complete_index];

		}

	}

	else if($normalized_data['payment-method'] == 'se_payment_method-check') {}

	else if($normalized_data['payment-method'] == 'se_payment_method-paypal') {}



	return $result;

	

}



function se_request_order_ajax_func() {

	$form_data = $_POST['form_data'];



	$normalized_data = [];



	foreach ($form_data as $form) {

		foreach ($form as $input ) {

			$normalized_data[ $input['name'] ] = $input['value'];

		}

	}



	

	if($normalized_data['payment-method'] == 'se_payment_method-cc') {



		$normalized_data['se_product_desc'] = 'Ticket: '. se_get_tickets_by_registration($registration_id)['name'];





		$creditCardData['cardNumber'] = $normalized_data['cardNumber'];

		$creditCardData['exp_year'] = $normalized_data['exp_year'];

		$creditCardData['exp_month'] = $normalized_data['exp_month'];

		$creditCardData['cvv'] = $normalized_data['cvv'];



		$creditCardData['first_name'] = $normalized_data['se_first_name'];

		$creditCardData['last_name'] = $normalized_data['se_last_name'];

		$creditCardData['email'] = $normalized_data['se_email'];

		$creditCardData['product_desc'] = $normalized_data['se_product_desc'];

		$creditCardData['registration_title'] = $normalized_data['registration_title'];



		$creditCardData['transaction_title'] = $normalized_data['transaction_title'];

		$creditCardData['street_address'] = $normalized_data['se_street_address'];

		$creditCardData['city'] = $normalized_data['se_city'];

		$creditCardData['state'] = $normalized_data['se_state'];

		$creditCardData['postal'] = $normalized_data['se_postal'];

		$creditCardData['country'] = $normalized_data['raw-country'];



		if ($normalized_data['se_event_deposit'] == '1') {

			$creditCardData['ticket_price'] = ( number_format( $normalized_data['se_ticket_price']/2 ,2,'.', '') );

		} else 

			$creditCardData['ticket_price'] = $normalized_data['se_ticket_price'];



		$transactionGetway = chargeCreditCard($creditCardData);



		if (isset($transactionGetway['tnx_id'])) {

			$normalized_data['getway'] = $transactionGetway;

			$result = se_create_registration_with_transactons($normalized_data);

		} else {

			$result['error'] = $transactionGetway['tnx_error'];

		}

	}

	else if($normalized_data['payment-method'] == 'se_payment_method-check') {



		$result = se_create_registration_with_transactons($normalized_data);

	}

	else if($normalized_data['payment-method'] == 'se_payment_method-paypal') {

		

		$result = se_create_registration_with_transactons($normalized_data);



	}



	echo json_encode($result);



	die();

}



add_action( 'wp_ajax_se_request_order_ajax_func', 'se_request_order_ajax_func' );

add_action( 'wp_ajax_nopriv_se_request_order_ajax_func', 'se_request_order_ajax_func' );













function se_pay_transaction_by_credit_card_ajax_func() {

	global $transaction_complete_index;



	$form_data = $_GET['form_data'];

	$normalized_data = [];



	foreach ($form_data as $input ) {

		$normalized_data[ $input['name'] ] = $input['value'];

	}



	$transaction_id = $normalized_data['wp_tnx_id'];

	$transactional_data = se_get_all_transactional_data($transaction_id);



	$creditCardData['cardNumber'] = $normalized_data['cardNumber'];

	$creditCardData['exp_year'] = $normalized_data['exp_year'];

	$creditCardData['exp_month'] = $normalized_data['exp_month'];

	$creditCardData['cvv'] = $normalized_data['cvv'];



	$creditCardData['first_name'] = $transactional_data['first_name'];

	$creditCardData['last_name'] = $transactional_data['last_name'];

	$creditCardData['email'] = $transactional_data['email'];

	$creditCardData['product_desc'] = $normalized_data['item_name'];

	$creditCardData['registration_title'] = $transactional_data['registration']->post_title;



	$creditCardData['transaction_title'] = $transactional_data['transaction']->post_title;

	$creditCardData['street_address'] = $transactional_data['street_address'];

	$creditCardData['city'] = $transactional_data['city'];

	$creditCardData['state'] = $transactional_data['state'];

	$creditCardData['postal'] = $transactional_data['postal'];

	$creditCardData['country'] = $transactional_data['country'];

	$creditCardData['ticket_price'] = $transactional_data['transaction_amount'];

	

	$transactionGetway = chargeCreditCard($creditCardData);
	//echo $form_data;die();// Testing payment issues
	if (isset($transactionGetway['tnx_id'])) {



		$transaction_data = se_get_transaction_meta($transaction_id);

		$transaction_data[1] = 'se_payment_method-cc';

		$transaction_data[3] = $transaction_complete_index;

		$transaction_data[6] = $transactionGetway['tnx_id'];



		$result = update_post_meta( $transaction_id, 'transaction_info_meta_key', $transaction_data ); 



	} else {

		$result['error'] = $transactionGetway['tnx_error'];

	}



	echo json_encode($result);



	die();

}



add_action( 'wp_ajax_se_pay_transaction_by_credit_card_ajax_func', 'se_pay_transaction_by_credit_card_ajax_func' );

add_action( 'wp_ajax_nopriv_se_pay_transaction_by_credit_card_ajax_func', 'se_pay_transaction_by_credit_card_ajax_func' );








	






