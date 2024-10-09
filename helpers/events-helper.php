<?php 

function se_get_workshop_current_events() {

	global $wpdb;
	
				// 2018-06-20 12:00 am
    $today = date('Y-m-d h:i a', strtotime('+6 hours'));

    $event_query = array(
        'post_type' => SCOUT_EVENTS_POST_TYPE,
        'posts_per_page'    => -1,
        'post_status' => 'publish',
		'meta_query'        => array(
	        'relation'  => 'AND',
	        array(
	            'key'       => 'se_start_date',
	            'value'     => $today,
	            'compare'   => '>=', // if se_start_date >= $today
	        ),
	    ),

    );

	// $events =query_posts( $event_query );
	$events = new WP_Query( $event_query );
	$selected_events = [];

	if (is_array($events->posts)) {
		foreach ($events->posts as $event) {

			$registrations = se_get_reserved_registrations_of_event($event->ID);
			$number_of_complete_booking = count($registrations);

			$registration_limit = get_post_meta( $event->ID, 'se_reg_limit', true );

			if ($registration_limit > $number_of_complete_booking)
				$selected_events[$event->ID] = $event;
				
		}
	}	
	return $selected_events;
}

function se_get_prev_day_th_events($date_diff = "+30") {

	global $wpdb;

    $target_date = date('Y-m-d', strtotime( $date_diff . " day"));
    $event_query = array(
        'post_type' => SCOUT_EVENTS_POST_TYPE,
        'posts_per_page'    => -1,
        'post_status' => 'publish',
		'meta_query'        => array(
	        'relation'  => 'AND',
	        array(
	            'key'       => 'se_start_date',
	            'value'     => $target_date,
	            'compare' => 'LIKE',
	        ),
	    ),

    );
	$events = new WP_Query( $event_query );
	return $events->posts;
}




function se_get_current_events()
{
	$current_events = se_get_workshop_current_events();
	if(is_array($current_events))
        foreach ($current_events as $event)
        	$event_ids[$event->ID] = $event->post_title;

    return $event_ids; 
}

function se_get_event_of_transaction($transaction_id)
{
    $transaction_data = se_get_transaction_meta($transaction_id);
    $event_id = $transaction_data[4];
    $selected_event = get_post($event_id);
    return $selected_event;
}

function se_get_event_of_registration($registration_id)
{	
	$reg_info_meta_data = get_post_meta( $registration_id, 'se_registration_info_meta_key', true );
	$event_id = $reg_info_meta_data[2];
	$selected_event = get_post($event_id);
    return $selected_event;
}

function se_get_event_metadata($event_id)
{
	$event_data = []; 
	
	$event_data['event_start_date']  = get_post_meta( $event_id, 'se_start_date', true);    
	$event_data['event_end_date']  = get_post_meta( $event_id, 'se_end_date', true);    
	$event_data['event_reg_limit']  = get_post_meta( $event_id, 'se_reg_limit', true);    
	$event_data['ticket_IDs']  = get_post_meta( $event_id, 'se_ticket_id', true);    
	$event_data['ticket_total_rows']  = get_post_meta( $event_id, 'se_total_tickets', true);    
	$event_data['tickets'] = get_post_meta( $event_id, '_se_ticket_meta_key', $ticket_data )[0]; 

	return $event_data;

}
function isEventAvailable($event_id)
{
	$event_data = se_get_event_metadata($event_id);	
    $reserved_registrations = se_get_reserved_registrations_of_event($event_id);
   
	return ( $event_data['event_reg_limit'] > count(  $reserved_registrations ) );
}