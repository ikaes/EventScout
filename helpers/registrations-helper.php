<?php 


function se_get_registration_meta($registration_id)
{
    $registration_data = get_post_meta( $registration_id, 'se_registration_info_meta_key', true ); 
    return $registration_data;
}

function se_get_reg_personal_meta($registration_id)
{
    $reg_personal_data = get_post_meta( $registration_id, 'se_reg_personal_info_meta_key', true ); 
    return $reg_personal_data;
}


function se_get_reg_formated_metadata($registration_id)
{
	$registration_data = [];
    $reg_personal_data = se_get_reg_personal_meta($registration_id); 
	$reg_info_meta_data = se_get_registration_meta($registration_id);

	$registration_data['first_name'] = $reg_personal_data[0];
	$registration_data['last_name'] = $reg_personal_data[1];
	$registration_data['email'] = $reg_personal_data[2];
	$registration_data['street_address'] = $reg_personal_data[3];
	$registration_data['city'] = $reg_personal_data[4];
	$registration_data['state'] = $reg_personal_data[5];
	$registration_data['postal'] = $reg_personal_data[6];
	$registration_data['country'] = $reg_personal_data[7];
	$registration_data['phone'] = $reg_personal_data[8];
	$registration_data['emrgnc_contact'] = $reg_personal_data[9];
	$registration_data['emrgnc_rel_contact'] = $reg_personal_data[10];
	$registration_data['emrgnc_phone'] = $reg_personal_data[11];
	$registration_data['emrgnc_email'] = $reg_personal_data[12];
	$registration_data['emrgnc_promotion'] = $reg_personal_data[13];
	$registration_data['citizen'] = $reg_personal_data[14];
	$registration_data['birthday'] = $reg_personal_data[15];
	$registration_data['birth_place'] = $reg_personal_data[16];
	$registration_data['pp_name'] = $reg_personal_data[17];
	$registration_data['pp_issue'] = $reg_personal_data[18];
	$registration_data['pp_exp_date'] = $reg_personal_data[19];
	$registration_data['gender'] = $reg_personal_data[20];

	$registration_data['registration_deposit'] = $reg_info_meta_data[0]; // $_POST['registration_deposit_field'];
	$registration_data['registration_deposit_title'] = get_registration_deposit_title($reg_info_meta_data[0]);
	$registration_data['registration_status_index'] = se_get_registration_status_index($registration_id);// $reg_info_meta_data[1]; // $_POST['registration_status_field'];
	$registration_data['registration_status'] = se_get_registration_status_by_index( $registration_data['registration_status_index'] );
	$registration_data['event_id'] = $reg_info_meta_data[2]; // $reg_info_meta_data[2]; // static event id
	$registration_data['ticket_id'] = $reg_info_meta_data[3]; // '2'; // static event ticket id
	$registration_data['ticket_name'] = se_get_ticket_name( $registration_data['event_id'] , $registration_data['ticket_id']);
	$registration_data['ticket_price'] = $reg_info_meta_data[4]; // '150'; // static event ticket price

    return $registration_data;
}

function get_registration_deposit_title($deposit_index)
{
	return $deposit_index == '1' ? '50% Deposit' : 'Full Balance';
}

function se_get_registration_status_index($registration_id)
{
    global $registration_complete_index;
    global $registration_downpaid_index;

    $reg_info_meta_data = get_post_meta( $registration_id, 'se_registration_info_meta_key', true );
    $total_price = floatval($reg_info_meta_data[4]);
    $already_paid = se_get_already_paid_amount($registration_id); 

    $registration_status_index =   $reg_info_meta_data[1];

    if($already_paid >= $total_price)
        $registration_status_index = $registration_complete_index;
    else if($already_paid > 0 )
    	$registration_status_index = $registration_downpaid_index;
    else if($already_paid <= 0 )
    	$registration_status_index = '0'; // pending status index

    return $registration_status_index;
}

function se_get_registration_status($registration_id)
{
    global $se_registrationStatusArr;
    $registration_status = $se_registrationStatusArr[se_get_registration_status_index($registration_id)];
    return $registration_status;
}

function se_get_registration_status_by_index($index)
{
    global $se_registrationStatusArr;
    $registration_status = $se_registrationStatusArr[$index];
    return $registration_status;
}



function se_get_all_registrations() {
    global $wpdb;

    $registration_query = array(
        'post_type' => SE_REGISTRATION_POST_TYPE,
        'posts_per_page'    => -1,
        'post_status' => 'publish'
    );

    $registrations = new WP_Query( $registration_query );

    return $registrations->posts; 
}

function se_get_registration_ids() {

    $registration_ids = [];
    $registrations = se_get_all_registrations();

    if (is_array($registrations)) 
        foreach ($registrations as $registration)
            $registration_ids[$registration->ID] = $registration->post_title;

    return $registration_ids;
}   




function se_get_all_registrations_of_event($event_id)
{
	$selected_registrations = [];
	$registrations = se_get_all_registrations();

	if (is_array($registrations)) {
		foreach ($registrations as $registration) {

			$reg_info_meta_data = se_get_registration_meta($registration->ID);
			$evnt_id = $reg_info_meta_data[2];

			if ($evnt_id == $event_id)
				$selected_registrations[$registration->ID] = $registration;
		}
	}	

	return $selected_registrations;
}


function se_get_completed_registrations_of_event($event_id)
{
	global $registration_complete_index;
	$selected_registrations = [];

	$registrations = se_get_all_registrations_of_event($event_id);

	if (is_array($registrations)) {
		foreach ($registrations as $registration) {

			$registration_status_index = se_get_registration_status_index($registration->ID);
			
			if ($registration_status_index == $registration_complete_index)
				$selected_registrations[$registration->ID] = $registration;
		}
	}	

	return $selected_registrations;
}

function se_get_downpaid_registrations_of_event($event_id)
{
	global $registration_downpaid_index;
	$selected_registrations = [];

	$registrations = se_get_all_registrations_of_event($event_id);

	if (is_array($registrations)) {
		foreach ($registrations as $registration) {

			$registration_status_index = se_get_registration_status_index($registration->ID);
			
			if ($registration_status_index == $registration_downpaid_index)
				$selected_registrations[$registration->ID] = $registration;
		}
	}	

	return $selected_registrations;
}
function se_get_reserved_registrations_of_event($event_id)
{
    $completed_registrations = se_get_completed_registrations_of_event($event_id);
    $downpaid_registrations = se_get_downpaid_registrations_of_event($event_id);
    $reserved_registrations = array_merge($completed_registrations,$downpaid_registrations);	

	return $reserved_registrations;
}

function se_get_incompleted_registrations_of_event($event_id)
{
	global $registration_complete_index;
	$selected_registrations = [];

	$registrations = se_get_all_registrations_of_event($event_id);

	if (is_array($registrations)) {
		foreach ($registrations as $registration) {

			$registration_status_index = se_get_registration_status_index($registration->ID);
			
			if ($registration_status_index != $registration_complete_index)
				$selected_registrations[$registration->ID] = $registration;
		}
	}	

	return $selected_registrations;
}



function se_get_registration_of_transaction($transaction_id)
{
    $transaction_data = se_get_transaction_meta($transaction_id);
    $registration_id = $transaction_data[0];
    $registration = get_post($registration_id);
    return $registration; 
}