<?php 
define('SE_REGISTRATION_POST_TYPE', 'se-registration');
define('SE_REGISTRATION_LNG_TAG', 'se-registration');

function se_registration_register_func() {  

    $labels = array(
        'name'               => esc_html__( 'SE Registration', SE_REGISTRATION_LNG_TAG ),
        'singular_name'      => esc_html__( 'Registration', SE_REGISTRATION_LNG_TAG ),
        'add_new'            => esc_html__( 'Add New Registration', SE_REGISTRATION_LNG_TAG ),
        'add_new_item'       => esc_html__( 'Add New Registration', SE_REGISTRATION_LNG_TAG ),
        'edit_item'          => esc_html__( 'Edit Registration', SE_REGISTRATION_LNG_TAG ),
        'new_item'           => esc_html__( 'Add New Registration', SE_REGISTRATION_LNG_TAG ),
        'view_item'          => esc_html__( 'View Registration', SE_REGISTRATION_LNG_TAG ),
        'search_items'       => esc_html__( 'Search Registrations', SE_REGISTRATION_LNG_TAG ),
        'not_found'          => esc_html__( 'No Registrations found', SE_REGISTRATION_LNG_TAG ),
        'not_found_in_trash' => esc_html__( 'No Registrations found in trash', SE_REGISTRATION_LNG_TAG )
    );
    
    $args = array(  
        'labels'          => $labels,
        'public'          => true,  
        'show_ui'         => true,  
        'capability_type' => 'post',  
        'hierarchical'    => false,  
        'menu_icon'       => 'dashicons-paperclip',
        'rewrite'         => array('slug' => SE_REGISTRATION_POST_TYPE), // Permalinks format
        'supports'        => array( 'title' ),
        'capability_type' => 'post',
        'capabilities' => array(
          'create_posts' => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
        ),
        'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
    );  
  
    register_post_type( SE_REGISTRATION_POST_TYPE , $args );  
    // remove_post_type_support( 'curated-links' , 'editor' );

}
add_action('init', 'se_registration_register_func', 1);   

function se_registration_edit_columns( $columns ) {
    $columns = array(
        "cb"          => "<input type=\"checkbox\" />",
        "title"       => esc_html__('Registration ID', SE_REGISTRATION_LNG_TAG),
        "attendees"       => esc_html__('Attendees', SE_REGISTRATION_LNG_TAG),
        "event"       => esc_html__('Event', SE_REGISTRATION_LNG_TAG),
        "ticket"       => esc_html__('Ticket', SE_REGISTRATION_LNG_TAG),
        "reg_status"       => esc_html__('Registration Status', SE_REGISTRATION_LNG_TAG),
        "date"        => esc_html__('Date', SE_REGISTRATION_LNG_TAG),
    );
    return $columns;
}

function se_registration_column_display( $columns, $post_id ) {

    $personal_info_meta_data = se_get_reg_personal_meta($post_id);


    switch ( $columns ) {
                
        case "attendees":
            echo $personal_info_meta_data[0]. " " . $personal_info_meta_data[1];        
        break;

        case "event":
            $selected_event = se_get_event_of_registration($post_id);
            echo '<a class="row-title" href="'.admin_url( 'post.php' ).'?post='.$selected_event->ID.'&amp;action=edit" target="_blank">'.$selected_event->post_title.'</a>';       
        break;

        case "ticket":
            $selected_ticket = se_get_tickets_by_registration($post_id);
            echo $selected_ticket['name'];       
        break;

        case "reg_status":
			global $se_registrationStatusArr;
            $registration_status_index = se_get_registration_status_index($post_id);

            echo esc_html__( $se_registrationStatusArr[ $registration_status_index ], SE_REGISTRATION_LNG_TAG);           
        break;        
    }
}

add_filter( 'manage_edit-'.SE_REGISTRATION_POST_TYPE.'_columns', 'se_registration_edit_columns' );
add_action( 'manage_posts_custom_column', 'se_registration_column_display', 10, 2 );


/* CONTACT META BOXES */
function se_registration_meta_boxes() {
    add_meta_box( 'se_registration_payment_info', 'Payment Information', 'se_payment_info_html', SE_REGISTRATION_POST_TYPE );
    add_meta_box( 'se_registration_reg_info', 'Registration Information', 'se_reg_info_html', SE_REGISTRATION_POST_TYPE );
    add_meta_box( 'se_registration_personal_info', 'Personal Information', 'se_personal_info_html', SE_REGISTRATION_POST_TYPE );
}
add_action( 'add_meta_boxes', 'se_registration_meta_boxes' );


function se_payment_info_html( $post ) {

    wp_nonce_field( 'se_registration_save_post_meta', 'se_registration_meta_box_nonce' );

    $reg_info_meta_data = get_post_meta( $post->ID, 'se_registration_info_meta_key', true );
    $meta_data = se_get_reg_personal_meta($post->ID);

	$total_price = floatval($reg_info_meta_data[4]);
	$already_paid = se_get_already_paid_amount($post->ID);

	?>
		<div class="payment-metabox">
			<div class="total-price">
				<span class="label">Total Amount for this booking :</span>
				<span class="value">$<?php echo $total_price; ?></span>
			</div>
			<div class="already">
				<span class="label">Already Paid: </span>
				<span class="value">$<?php echo $already_paid; ?></span>
			</div>
			<div class="transaction">
				<span class="label">Related Transaction: </span>
				<span class="value">
					<?php se_get_formated_related_transactions($post->ID); ?>
				</span>
			</div>

		</div>
    
    <?php
}

function se_reg_info_html( $post ) {
	global $se_registrationStatusArr;

    wp_nonce_field( 'se_registration_save_post_meta', 'se_registration_meta_box_nonce' );

    $reg_info_meta_data = get_post_meta( $post->ID, 'se_registration_info_meta_key', true );
    $personal_info_meta_data = se_get_reg_personal_meta($post->ID);

    $event_id = $reg_info_meta_data[2];  
    $selected_ticket = se_get_tickets_by_registration($post->ID);

    $selected_event = get_post($event_id); 
    $registration_status_index = se_get_registration_status_index($post->ID);

	?>
		<div class="registration-metabox">
			<div class="registration-id">
				<span class="label">Registration ID: </span>
				<span class="value"><?php echo $post->post_title; ?></span>
			</div>
			<div class="attendee-name">
				<span class="label">Attendee: </span>
				<span class="value"><?php echo $personal_info_meta_data[0]. " " . $personal_info_meta_data[1]; ?></span>
			</div>
			<div class="event">
				<span class="label">Event</span>
				<span class="value"><?php echo $selected_event->post_title; ?></span>
			</div>
			<div class="ticket">
				<span class="label">Ticket</span>
				<span class="value"><?php echo $selected_ticket['name']; ?></span>
			</div>
			<div class="price">
				<span class="label">Price: </span>
				<span class="value">$<?php echo $reg_info_meta_data[4] ? $reg_info_meta_data[4] : '0'; ?></span>
			</div>
			<?php se_get_admin_select_field( 'Registration Status:', $se_registrationStatusArr, $registration_status_index, "registration_status_field", "registration-status"); ?>
            <?php se_get_admin_text_field('Registration Deposit', 'registration_deposit_field', $reg_info_meta_data[0], "", 'hidden', true);?>
		</div>
    
    <?php
}


function se_personal_info_html( $post ) {

    wp_nonce_field( 'se_registration_save_post_meta', 'se_registration_meta_box_nonce' );

    $meta_data = se_get_reg_personal_meta($post->ID);
    ?>
	<div class="personal-metabox">
		<h3>General Information</h3><hr>
		<div class="customer-name">
			<label for="customer_first_name_field">First Name: </label>
			<input type="text" id="customer_first_name_field" name="customer_first_name_field" value="<?php echo esc_attr( $meta_data[0] ); ?>" />
			<label for="customer_last_name_field">Last Name: </label>
			<input type="text" id="customer_last_name_field" name="customer_last_name_field" value="<?php echo esc_attr( $meta_data[1] ); ?>" /><br>
		</div>

		<?php se_get_admin_text_field('Email:', 'customer_email_field', $meta_data[2], 'customer-email') ?>
		<?php se_get_admin_text_field('Street Address:', 'customer_street_address_field', $meta_data[3], 'customer-street_address') ?>
		<?php se_get_admin_text_field('City:', 'customer_city_field', $meta_data[4], 'customer-city') ?>
		<?php se_get_admin_text_field('State:', 'customer_state_field', $meta_data[5], 'customer-state') ?>
		<?php se_get_admin_text_field('Postal:', 'customer_postal_field', $meta_data[6], 'customer-postal') ?>

		<div class="customer-country">
			<label for="customer_country_field">country: </label>
			<?php echo countrySelector($meta_data[7], "customer_country_field", "customer_country_field","customer_country_field"); ?>
		</div>
		<?php se_get_admin_text_field('phone:', 'customer_phone_field', $meta_data[8], 'customer-phone') ?>
		<?php se_get_admin_text_field('Emergency Contact:', 'customer_emrgnc_contact_field', $meta_data[9], 'customer-emrgnc_contact') ?>
		<?php se_get_admin_text_field('Relationship of Contact:', 'customer_emrgnc_rel_contact_field', $meta_data[10], 'customer-emrgnc_rel_contact') ?>
		<?php se_get_admin_text_field('Emergency Phone:', 'customer_emrgnc_phone_field', $meta_data[11], 'customer-emrgnc_phone') ?>
		<?php se_get_admin_text_field('Emergency Email:', 'customer_emrgnc_email_field', $meta_data[12], 'customer-emrgnc_email') ?>
		<?php se_get_admin_text_field('Promotion:', 'customer_emrgnc_promotion_field', $meta_data[13], 'customer-emrgnc_promotion') ?>
		<br><br>

		<h3>Passport Information</h3><hr>		
		<?php se_get_admin_text_field('Citizen:', 'citizen_field', $meta_data[14], 'passport-citizen') ?>
		<?php se_get_admin_text_field('Birthday:', 'birthday_field', $meta_data[15], 'passport-birthday') ?>
		<?php se_get_admin_text_field('Birth Place:', 'birth_place_field', $meta_data[16], 'passport-birth_place') ?>
		<?php se_get_admin_text_field('Passport Name:', 'pp_name_field', $meta_data[17], 'passport-pp_name') ?>
		<?php se_get_admin_text_field('Passport Issue:', 'pp_issue_field', $meta_data[18], 'passport-pp_issue') ?>
		<?php se_get_admin_text_field('Passport Expire Date:', 'pp_exp_date_field', $meta_data[19], 'passport-pp_exp_date') ?>
		<?php se_get_admin_text_field('Gender:', 'gender_field', $meta_data[20], 'passport-gender') ?>
	</div>
    <?php
}


function se_registration_save_post_meta_func( $post_id ) {

    if( ! isset( $_POST['se_registration_meta_box_nonce'] ) )
        return;     
    if( ! wp_verify_nonce( $_POST['se_registration_meta_box_nonce'], 'se_registration_save_post_meta') ) 
        return;     
    if( ! current_user_can( 'edit_post', $post_id ) ) 
        return;

    $meta_data[0] = $_POST['customer_first_name_field'];
    $meta_data[1] = $_POST['customer_last_name_field'];
    $meta_data[2] = $_POST['customer_email_field'];
    $meta_data[3] = $_POST['customer_street_address_field'];
    $meta_data[4] = $_POST['customer_city_field'];
    $meta_data[5] = $_POST['customer_state_field'];
    $meta_data[6] = $_POST['customer_postal_field'];
    $meta_data[7] = $_POST['customer_country_field'];
    $meta_data[8] = $_POST['customer_phone_field'];
    $meta_data[9] = $_POST['customer_emrgnc_contact_field'];
    $meta_data[10] = $_POST['customer_emrgnc_rel_contact_field'];
    $meta_data[11] = $_POST['customer_emrgnc_phone_field'];
    $meta_data[12] = $_POST['customer_emrgnc_email_field'];
    $meta_data[13] = $_POST['customer_emrgnc_promotion_field'];
    $meta_data[14] = $_POST['citizen_field'];
    $meta_data[15] = $_POST['birthday_field'];
    $meta_data[16] = $_POST['birth_place_field'];
    $meta_data[17] = $_POST['pp_name_field'];
    $meta_data[18] = $_POST['pp_issue_field'];
    $meta_data[19] = $_POST['pp_exp_date_field'];
    $meta_data[20] = $_POST['gender_field'];

    update_post_meta( $post_id, 'se_reg_personal_info_meta_key', $meta_data );   

    $reg_info_meta_data = se_get_registration_meta($post_id);

    $reg_info_meta_data[0] = $_POST['registration_deposit_field']; // se_event_deposit | 50% discount?
    $reg_info_meta_data[1] = $_POST['registration_status_field'];
    // $reg_info_meta_data[2] = $reg_info_meta_data[2]; // static event id
    // $reg_info_meta_data[3] = '2'; // static event ticket id
    // $reg_info_meta_data[4] = '150'; // static event ticket price id

    update_post_meta( $post_id, 'se_registration_info_meta_key', $reg_info_meta_data );    
}

add_action( 'save_post', 'se_registration_save_post_meta_func' );


