<?php 



add_filter( 'wp_mail_from', 'my_mail_from' );
function my_mail_from( $email ) {
    return "wordpress@wodpress.com";
}



function my_mail_from_name( $name ) {
	$bloginfo = get_bloginfo( 'name' );
    return $bloginfo;
}
add_filter( 'wp_mail_from_name', 'my_mail_from_name' );


function se_send_transaction_mail($transaction_id) {

	global $email_notification_settings_page;

	// $to = 'scoutsolution@gmail.com';
	// $subject = 'The subject';
	// $body = 'The email body content';
	//	$headers = array('Content-Type: text/html; charset=UTF-8');

	$headers = "From: wordpress@wpengine.com"."\r\n";
	$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
	// $headers .= "CC: jk@ilogic.co.il\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

	$transaction_data = se_get_transaction_meta($transaction_id);
	$registration_id = $transaction_data[0];
	$registration_title = get_post($transaction_id)->post_title;

	$registration_personal_info = se_get_reg_personal_meta($registration_id);

	$se_email_subject = get_option('se_email_subject');
	$se_email_body = get_option('se_email_body'); 

	$registration_email = $registration_personal_info['2'];
	$subject = (!empty($se_email_subject)) ? $se_email_subject : $email_notification_settings_page->default_due_msg_subject;
	$body = (!empty($se_email_body)) ? $se_email_body : $email_notification_settings_page->default_body_due_msg;

	$event = se_get_event_of_transaction($transaction_id);


	$transaction_pay_link = sprintf( '<a href="%1$s">%1$s</a>',esc_url(home_url( '/' ).TRANSACTION_PAY_LINK.'/?tnx='.$transaction_id) );


	$patterns = array();
	$patterns[0] = '/\[se_fname\]/';
	$patterns[1] = '/\[se_lname\]/';
	$patterns[2] = '/\[se_event\]/';
	$patterns[3] = '/\[se_bkid\]/';
	$patterns[4] = '/\[se_paylink\]/';
	$patterns[5] = '/\[se_amount\]/';
	$patterns[6] = '/\[se_tnxid\]/';
	$patterns[7] = '/\[tab\]/';

	$replacements = array();
	$replacements[0] = $registration_personal_info['0']; // 'SE_FNAME ';
	$replacements[1] = $registration_personal_info['1']; // 'SE_LNAME ';
	$replacements[2] = se_get_event_of_transaction($transaction_id)->post_title; // 'SE_EVENT ';
	$replacements[3] = se_get_registration_of_transaction($transaction_id)->post_title; // 'SE_BKID ';
	$replacements[4] = $transaction_pay_link; // 'SE_PAYLINK ';
	$replacements[5] = $transaction_data['2']; // 'SE_AMOUNT ';
	$replacements[6] = $registration_title; // 'SE_TNXID ';
	$replacements[7] = "&nbsp;&nbsp;&nbsp;&nbsp;"; // 'tab ';

	$body =  preg_replace($patterns, $replacements, $body);

	$sent_message = wp_mail( $registration_email, $subject, $body, $headers);
	return $sent_message;
}

function se_send_transaction_mail_ajax_func() {

	$transaction_id = $_GET['data'];
	
	$sent_message = se_send_transaction_mail($transaction_id);

	if ( $sent_message ) {
        echo '<span class="success">Mail Sent Successfully</span>';
    } else {
        echo '<span class="error">Mail was not Sent</span>';
    }
	
	die();
}
add_action( 'wp_ajax_se_send_transaction_mail_ajax_func', 'se_send_transaction_mail_ajax_func' );
add_action( 'wp_ajax_nopriv_se_send_transaction_mail_ajax_func', 'se_send_transaction_mail_ajax_func' );




function se_send_transaction_success_mail($transaction_id) {

	global $email_notification_settings_page;

	$headers = "From: wordpress@wpengine.com"."\r\n";
	$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
	// $headers .= "CC: jk@ilogic.co.il\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	// $headers = array('Content-Type: text/html; charset=UTF-8');

	$transactional_data = se_get_all_transactional_data($transaction_id);
	$transaction_pay_link = sprintf( '<a href="%1$s">%1$s</a>',esc_url(home_url( '/' ).TRANSACTION_PAY_LINK.'/?tnx='.$transaction_id) );

	// $body[] = 'Name:'.$transactional_data['first_name'].' '.$transactional_data['last_name'];
	// $body[] = 'Event:'.$transactional_data['event']->post_title;
	// $body[] = 'Event Start:'.$transactional_data['event_start_date'];
	// $body[] = 'Event End:'.$transactional_data['event_end_date'];
	// $body[] = 'Ticket:'.$transactional_data['ticket_name'];
	// $body[] = 'Ticket Price:'.$transactional_data['ticket_price'];
	// $body[] = 'Payment Method:'.$transactional_data['payment_method_title'];
	// $body[] = 'Payment Type:'.$transactional_data['registration_deposit_title'];
	// $body[] = 'Booking ID:'.$transactional_data['registration']->post_title;
	// $body[] = 'Booking Status:'.$transactional_data['registration_status'];
	// $body[] = 'Transaction ID:'.$transactional_data['transaction']->post_title .' ('.$transactional_data['transaction_status'].')';
	// if ($transactional_data['transaction2'])
	// $body[] = 'Transaction ID2:'.$transactional_data['transaction2']->post_title.' ('.$transactional_data['transaction_status2'].')';

	$se_email_subject = get_option('se_success_email_subject');
	$se_email_body = get_option('se_success_email_body'); 

	$subject = (!empty($se_email_subject)) ? $se_email_subject : $email_notification_settings_page->default_success_msg_subject;
	$body = (!empty($se_email_body)) ? $se_email_body : $email_notification_settings_page->default_body_success_msg;


	$patterns = array();
	$patterns[0] = '/\[se_fname\]/';
	$patterns[1] = '/\[se_lname\]/';
	$patterns[2] = '/\[se_event\]/';
	$patterns[3] = '/\[se_bkid\]/';
	$patterns[4] = '/\[se_paylink\]/';
	$patterns[5] = '/\[se_amount\]/';
	$patterns[6] = '/\[se_tnxid\]/';
	$patterns[7] = '/\[tab\]/';

	$replacements = array();
	$replacements[0] = $transactional_data['first_name']; // 'SE_FNAME ';
	$replacements[1] = $transactional_data['last_name']; // 'SE_LNAME ';
	$replacements[2] = $transactional_data['event']->post_title; // 'SE_EVENT ';
	$replacements[3] = $transactional_data['registration']->post_title; // 'SE_BKID ';
	$replacements[4] = $transaction_pay_link; // 'SE_PAYLINK ';
	$replacements[5] = $transactional_data['transaction_amount']; // 'SE_AMOUNT ';
	$replacements[6] = $transactional_data['transaction']->post_title; // 'SE_TNXID ';
	$replacements[7] = "&nbsp;&nbsp;&nbsp;&nbsp;"; // 'tab ';

	$body =  preg_replace($patterns, $replacements, $body);

	$sent_message = wp_mail( $transactional_data['email'], $subject, $body, $headers);

	return $sent_message;
}


function se_send_transaction_successfull_mail_ajax_func() {

	$transaction_id = $_GET['transaction_id'];
	
	$sent_message = se_send_transaction_success_mail($transaction_id);

	if ( $sent_message ) {
		echo '<span class="success">Mail Sent Successfully</span>';
	} else {
		echo '<span class="error">Mail was not Sent</span>';
	}
	
	die();
}
add_action( 'wp_ajax_se_send_transaction_successfull_mail_ajax_func', 'se_send_transaction_successfull_mail_ajax_func' );
add_action( 'wp_ajax_nopriv_se_send_transaction_successfull_mail_ajax_func', 'se_send_transaction_successfull_mail_ajax_func' );






function se_send_mail_to_prev_pending_transaction_of_events()
{
    $events = se_get_prev_day_th_events("+30");

    if (is_array($events)) {
    	foreach ($events as $event) {

    		$transactions = se_get_all_pending_transaction_of_event($event->ID);
    		// $meta_data = get_post_meta( $event->ID, 'se_start_date', true );

    		// var_dump($event);
    		// var_dump($meta_data);
    		// var_dump($transactions);

    		se_send_transaction_mail($transactions->ID);

    			
    	}
    }		
}