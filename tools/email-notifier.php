<?php 


function event_email_cron_schedule( $schedules ) {
	$schedules['minute'] = array(
		// 'interval' =>  60, // Every minute;
		'interval' =>  86400, // Once Daily
		'display'  => __( 'Every Day' ),
	);

	return $schedules;
}
add_filter( 'cron_schedules', 'event_email_cron_schedule' );



function email_notification_cron_task_schedule(){
	//Use wp_next_scheduled to check if the event is already scheduled
	$timestamp = wp_next_scheduled( 'se_email_notification_cron_action' );

	//If $timestamp == false schedule daily backups since it hasn't been done previously
	if( $timestamp == false ){
		//Schedule the event for right now, then to repeat daily using the hook 'wc_create_daily_backup'		 
		wp_schedule_event( time(), 'minute', 'se_email_notification_cron_action' );

	}
}
add_action('init', 'email_notification_cron_task_schedule');



/**
 * @method se_email_notification_cron_func
 * Documentation: perform the task repeatdly based on cron task
 * @return {void} nothing to return
 */
function se_email_notification_cron_func(){

	se_send_mail_to_prev_pending_transaction_of_events();
}

//Hook our function , se_email_notification_cron_action(), into the action se_email_notification_cron_func
add_action( 'se_email_notification_cron_action', 'se_email_notification_cron_func' );

