<?php 


 define('SE_TRANSACTION_POST_TYPE', 'se-transaction');
 define('SE_TRANSACTION_LNG_TAG', 'se-transaction');
 
 function se_transaction_register_func() {  
 
     $labels = array(
         'name'               => esc_html__( 'SE Transactions', SE_TRANSACTION_LNG_TAG ),
         'singular_name'      => esc_html__( 'Transaction', SE_TRANSACTION_LNG_TAG ),
         'add_new'            => esc_html__( 'Add New Transaction', SE_TRANSACTION_LNG_TAG ),
         'add_new_item'       => esc_html__( 'Add New Transaction', SE_TRANSACTION_LNG_TAG ),
         'edit_item'          => esc_html__( 'Edit Transaction', SE_TRANSACTION_LNG_TAG ),
         'new_item'           => esc_html__( 'Add New Transaction', SE_TRANSACTION_LNG_TAG ),
         'view_item'          => esc_html__( 'View Transaction', SE_TRANSACTION_LNG_TAG ),
         'search_items'       => esc_html__( 'Search Transactions', SE_TRANSACTION_LNG_TAG ),
         'not_found'          => esc_html__( 'No Transactions found', SE_TRANSACTION_LNG_TAG ),
         'not_found_in_trash' => esc_html__( 'No Transactions found in trash', SE_TRANSACTION_LNG_TAG )
     );
     
     $args = array(  
         'labels'          => $labels,
         'public'          => true,  
         'show_ui'         => true,  
         'capability_type' => 'post',  
         'hierarchical'    => false,  
         'menu_icon'       => 'dashicons-paperclip',
         'rewrite'         => array('slug' => SE_TRANSACTION_POST_TYPE), // Permalinks format
         'supports'        => array( 'title' ),
     );  
   
     register_post_type( SE_TRANSACTION_POST_TYPE , $args );  
     // remove_post_type_support( 'curated-links' , 'editor' );
 
 }
 add_action('init', 'se_transaction_register_func', 1);   
 
 
function se_transaction_edit_columns( $columns ) {
     $columns = array(
         "cb"          => "<input type=\"checkbox\" />",
         "title"       => esc_html__('Transaction ID', SE_TRANSACTION_LNG_TAG),
         "reg_id"       => esc_html__('Registration ID', SE_TRANSACTION_LNG_TAG),
         "event_id"       => esc_html__('Event', SE_TRANSACTION_LNG_TAG),
         "status"       => esc_html__('Status', SE_TRANSACTION_LNG_TAG),
         "date"        => esc_html__('Date', SE_TRANSACTION_LNG_TAG),
     );
     return $columns;
 }
 
 /* ----------------------------------------------------- */
 
function se_transaction_column_display( $columns, $post_id ) {
    	

    switch ( $columns ) {
		case "reg_id":  
    		$transaction_data = se_get_transaction_meta($post_id);
			$registration_id = $transaction_data[0];   
			$registration_ids = se_get_registration_ids();
			echo '<a class="row-title" href="'.admin_url( 'post.php' ).'?post='.$registration_id.'&amp;action=edit" target="_blank">'.$registration_ids[$registration_id].'</a>';
		break; 
        case "event_id":  
            $selected_event = se_get_event_of_transaction($post_id);
            echo '<a class="row-title" href="'.admin_url( 'post.php' ).'?post='.$selected_event->ID.'&amp;action=edit" target="_blank">'.$selected_event->post_title.'</a>';
        break;  
		case "status":
			echo se_get_transaction_status($post_id);
		break;         
    }
}
 
 add_filter( 'manage_edit-'.SE_TRANSACTION_POST_TYPE.'_columns', 'se_transaction_edit_columns' );
 add_action( 'manage_posts_custom_column', 'se_transaction_column_display', 10, 2 );
 
 
 
 
 
 /* CONTACT META BOXES */
 function se_transaction_meta_boxes() {
     add_meta_box( 'transaction_info_meta_box', 'Transaction Details', 'se_transaction_details_html', SE_TRANSACTION_POST_TYPE );
     add_meta_box( 'event_details_meta_box', 'Event Details', 'se_event_details_html', SE_TRANSACTION_POST_TYPE );
 }
 add_action( 'add_meta_boxes', 'se_transaction_meta_boxes' );
 
 function se_transaction_details_html( $post ) {

    global $seTransactionStatusArr;	
    global $transaction_complete_index;

	wp_nonce_field( 'se_transaction_save_post_meta', 'se_transaction_meta_box_nonce' );
	$transaction_data = se_get_transaction_formated_meta($post->ID);
	$status_index = se_get_transaction_status_index($post->ID);

    se_get_admin_text_field('Transaction ID', 'transaction_id_field', $post->post_title, 'transaction-id');
    se_get_admin_text_field('Gateway Tnx ID / Check no', 'transaction_getway_id_field', $transaction_data['transaction_getway_id']);

    se_get_admin_select_field('Payment Method', 
        array(
            0 => 'Select a Payment Option',
            'se_payment_method-cc' => 'Credit Card',
            'se_payment_method-paypal' => 'Paypal',
            'se_payment_method-check' => 'Check'
        ), 
        $transaction_data['payment_method'] , "payment_method_field", "payment-method");

    se_get_admin_text_field('Amount', 'transaction_amount_field', $transaction_data['transaction_amount'], 'transaction_amount', 'number') ;
    se_get_admin_select_field( 'Transaction Status:', $seTransactionStatusArr, $status_index, "transaction_status_field", "transaction-status"); ?>
    
    <?php if($status_index != $transaction_complete_index) : ?>
    <div class="transaction-mail">
        <button id="btn-transaction-payment">Send Payment Remainder</button>
        <img class="se-spinner" src="<?php echo SE_ASSETS_URL; ?>/images/spinner30px.gif" style="display: none;">
        <span class="mail-result"></span>
    </div>
    <?php endif; ?>
     <?php 
 }

  
 function se_event_details_html( $post ) {
 
    wp_enqueue_script('scout-events-admin-scripts');
    wp_nonce_field( 'se_transaction_save_post_meta', 'se_transaction_meta_box_nonce' );
    $current_events = se_get_workshop_current_events();
    $transaction_data = se_get_transaction_formated_meta($post->ID);
    $event_id = $transaction_data['transaction_event_id'];


    if(   !isset($event_id ) || empty( $event_id  )    ) {
        ?><label for="se_transaction_event_field" class="label">Event Name</label><?php 
        $output[] = '<select id="se_transaction_event_field" name="se_transaction_event_field" class="custom_select_box" required>';
            $output[] = '<option value="">Please Select a Workshop *</option>';
            if(is_array($current_events))
            foreach ($current_events as $event) 
                $output[] = '<option value="'.$event->ID.'" '.( ($event_id == $event->ID)?"selected":"").'>'.$event->post_title.'</option>';
        $output[] = '</select>';
            echo implode('', $output);
    } else {
        if(is_array($current_events))
        foreach ($current_events as $event) {
            $eventName = ($event_id == $event->ID)? $event->post_title : '';
            if( $eventName )
                break;
        }
        se_get_admin_readonly_field('Event Name:', 'se_event', [], $eventName, 'transaction-event');
    }



	$ticket_list = se_get_tickets_by_event_id($event_id);
	$ticket_list[0] = 'â€” Select â€”';
	ksort($ticket_list);

    if(   !isset($transaction_data['transaction_ticket_id']) || empty( $transaction_data['transaction_ticket_id'] )    ) {
        se_get_admin_select_field( 'Ticket:', $ticket_list, $transaction_data['transaction_ticket_id'], "se_transaction_ticket_field", "transaction-ticket");
    } else { 
        se_get_admin_readonly_field('Ticket:', 'se_transaction_ticket', $ticket_list, $ticket_list[$transaction_data['transaction_ticket_id']], 'transaction-ticket');
    }

    	$registration_ids = se_get_registration_ids(); 
    	$registration_ids[0] = 'â€” Select â€”';
    	ksort($registration_ids);

        $selected_registration = $transaction_data['registration_id'];

        if( empty($selected_registration) || !isset($registration_ids[$selected_registration]) )
            se_get_admin_select_field('Booking ID', $registration_ids, $selected_registration, 'event_registration_id_field', 'event-registration-id');
        else {
            se_get_admin_readonly_field('Booking ID', 'event_registration_id_field', $selected_registration, $registration_ids[ $selected_registration ], 'event-registration-id');
        } 
 }
 
 function se_transaction_save_post_meta_func( $post_id ) {
 
     if( ! isset( $_POST['se_transaction_meta_box_nonce'] ) )
         return;     
     if( ! wp_verify_nonce( $_POST['se_transaction_meta_box_nonce'], 'se_transaction_save_post_meta') ) 
         return;     
     if( ! current_user_can( 'edit_post', $post_id ) ) 
         return;
     
    $transaction_data = se_get_transaction_formated_meta($post_id);

	$transactionData[0] = ($_POST['event_registration_id_field']) ? $_POST['event_registration_id_field'] : $transaction_data['registration_id'] ;
	$transactionData[1] = ($_POST['payment_method_field']) ? $_POST['payment_method_field'] : $transaction_data['payment_method'] ;
	$transactionData[2] = ($_POST['transaction_amount_field']) ? $_POST['transaction_amount_field'] : $transaction_data['transaction_amount'];
	$transactionData[3] = ($_POST['transaction_status_field']) ? $_POST['transaction_status_field'] : $transaction_data['transaction_status_index'];
	$transactionData[4] = ($_POST['se_transaction_event_field']) ? $_POST['se_transaction_event_field'] : $transaction_data['transaction_event_id'];
	$transactionData[5] = ($_POST['se_transaction_ticket_field']) ? $_POST['se_transaction_ticket_field'] : $transaction_data['transaction_ticket_id'];
	$transactionData[6] = ($_POST['transaction_getway_id_field']) ? $_POST['transaction_getway_id_field'] : $transaction_data['transaction_getway_id'];

	update_post_meta( $post_id, 'transaction_info_meta_key', $transactionData );   
 
 }
 
 add_action( 'save_post', 'se_transaction_save_post_meta_func' );


