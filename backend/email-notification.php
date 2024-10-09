<?php 

/**
 * summary
 */
class ScoutEventEmailNotificationSettingsPage {
	public $default_body_due_msg = <<<EOT
Dear,<br>
[tab][se_fname] [se_lname] you have a pending payment with us for the booking of [se_event]. The Amount you owe us on transaction: [se_tnxid] is $[se_amount].<br>
[tab]Your booking id is [se_bkid]<br>
[tab]Please go to the following link to pay: [se_paylink]<br><br>
[tab]Thank You.<br><br>
EOT;

	public $default_body_success_msg = <<<EOT
Dear,<br>
[tab][se_fname] [se_lname] you have sucessfully paid of transaction: [se_tnxid]  for the booking of [se_event].<br>
[tab]Your booking id is [se_bkid]<br>
[tab]Thank You.<br><br>
EOT;

	public $default_due_msg_subject = "Due Payment Reminder for event Booking";
	public $default_success_msg_subject = "Transaction Successfully Paid for event Booking";
	
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_se_event_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'se_e_notify_register_settings' ) );        
    }

	public function add_se_event_admin_menu()
	{   
		$se_setting_slug = 'se-settings';
	    
	    // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' )
	    add_submenu_page( $se_setting_slug, 'Email Notification Page', 'Email Notification', 'manage_options', 'se-email-settings', array($this,'se_email_notification_template') );


	}

	public function se_email_notification_template() {?> 

	    <?php screen_icon(); ?>
	    <div class="se-email-notification-wrapper">

			<form method="post" action="options.php">
			    <?php settings_fields( 'se_email_settings' ); ?>
				
				<div class="email-template-selection">
					<h2>Email Notification</h2><hr>
					<?php $this->get_textbox('se_email_subject','Subject', $this->default_due_msg_subject) ?>
					<?php $this->get_textarea('se_email_body','Due Mail Message', $this->default_body_due_msg) ?>
					
					<?php $this->get_textbox('se_success_email_subject','Paid Success Subject', $this->default_success_msg_subject) ?>
					<?php $this->get_textarea('se_success_email_body','Paid Success Message', $this->default_body_success_msg) ?>
				
				</div>


				<br>
				<div class="notes">
					<p><em>Usable Shortcode in email template:</em></p>
					<ol>
						<li>First Name: [se_fname]</li>
						<li>Last Name: [se_lname]</li>
						<li>Event Name: [se_event]</li>
						<li>Booking/Registration ID: [se_bkid]</li>
						<li>Payment Link: [se_paylink]</li>
						<li>Payment Amount: [se_amount]</li>
						<li>Transaction ID: [se_tnxid]</li>
						<li>Tab: [tab]</li>
					</ol>
				</div>
			    <?php  submit_button(); ?>

			</form>
	    </div>

	    <?php
	}

	public function se_e_notify_register_settings() {

		register_setting('se_email_settings', 'se_email_subject');
		register_setting('se_email_settings', 'se_email_body');
		register_setting('se_email_settings', 'se_success_email_subject');
		register_setting('se_email_settings', 'se_success_email_body');
	}

	public function get_textbox($option_name,$label, $default_value="", $class= "" ) {

		$value = get_option($option_name) ? get_option($option_name) : $default_value;
		se_get_admin_text_field($label, $option_name, $value, $class);		 
	}
	public function get_textarea($option_name,$label, $default_value="", $class= "" ) {
		 
		$value = get_option($option_name) ? get_option($option_name) : $default_value;
		se_get_admin_textarea_field($label, $option_name, $value, $class );	
	}

} // end of ScoutEventEmailNotificationSettingsPage class

if( is_admin() )
    $email_notification_settings_page = new ScoutEventEmailNotificationSettingsPage();