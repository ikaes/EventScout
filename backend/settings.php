<?php 

/**
 * summary
 */
class ScoutEventSettingsPage {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_se_event_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'se_event_register_settings' ) );        
    }

	public function add_se_event_admin_menu()
	{   
		$se_setting_slug = 'se-settings';
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null )
	    add_menu_page( 'Scout Event Settings Page', 'SE Settings', 'manage_options', $se_setting_slug, 'spf_global_settings_template', 'dashicons-media-spreadsheet', 40 );

	    // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' )
	    add_submenu_page( $se_setting_slug, 'Payment Method Settings Page', 'Payment Method', 'manage_options', 'se-payment', array($this,'se_payment_method_template') );

	    // Removing admin submenu
	    global $submenu;
	    if(is_array($submenu[$se_setting_slug]))
	    	array_shift($submenu[$se_setting_slug]);

	}

	public function se_payment_method_template() {?> 

	    <?php screen_icon(); ?>
	    <div class="se-setting-wrapper">

			<form method="post" action="options.php">
			    <?php settings_fields( 'se_payment_methods' ); ?>
				
				<div class="payment-method-selection">
					<h2>Payment Method (Active)</h2><hr>
					<?php $this->get_checkbox('se_payment_paypal','Paypal') ?>
					<?php $this->get_checkbox('se_payment_authorized_net','Authorized.net') ?>
					<?php $this->get_checkbox('se_payment_check','Check') ?>
				</div>

				<div class="paypal-settings">
					<h2>Paypal Settings</h2><hr>
					<?php $this->get_selectbox('se_payment_paypal_sendbox','Select Sendbox Mode',array('disabled'=>'Disabled','enabled'=>'Enabled'), $class= ""); ?>
					<?php $this->get_textbox('se_payment_paypal_getway_email','Gateway Account Email:') ?>
					<?php $this->get_textbox('se_payment_paypal_api_user_name','API Username:') ?>
					<?php $this->get_textbox('se_payment_paypal_api_password','API Password:') ?>
					<?php $this->get_textbox('se_payment_paypal_api_signature','API Signature:') ?>
				</div>

				<div class="authorized-net-setting">
					<h2>Authorize.net Settings</h2><hr>
					<?php $this->get_selectbox('se_payment_authorized_sendbox','Select Sendbox Mode',array('disabled'=>'Disabled','enabled'=>'Enabled'), $class= ""); ?>
					<?php $this->get_textbox('se_payment_authorized_login','Login Name:	') ?>
					<?php $this->get_textbox('se_payment_authorized_transaction_key','Transaction Key:') ?>
				</div>

				<div class="check-setting">
					<h2>Check Settings</h2><hr>
					<?php $this->get_textarea('se_payment_check_address','Cheque Payable to: ', "", "Rome Art Workshops, Inc. <br>\n 1427 25th Street #2 <br>\n Santa Monica, CA 90404"); ?>
				</div>

				<div class="processing-setting">
					<h2>Processing Fee</h2><hr>
					<?php $data=[]; for ($i = 0; $i < 51 ; $i++) { $data[$i] = $i; } ?>
					<?php $this->get_selectbox('se_payment_processing_percent','% of Processing Fee: ',$data); ?>

					<?php $this->get_textarea('se_payment_processing_notice','Processing Fee Notice:', "", "All Credit Card and PayPal transactions will have a 3% processing fee added to the total."); ?>
				</div>

				<br>
			    <?php submit_button(); ?>

			</form>
	    </div>

	    <?php
	}

	public function se_event_register_settings() {		
		register_setting('se_payment_methods', 'se_payment_paypal');
		register_setting('se_payment_methods', 'se_payment_authorized_net');
		register_setting('se_payment_methods', 'se_payment_check');

		register_setting('se_payment_methods', 'se_payment_paypal_sendbox');
		register_setting('se_payment_methods', 'se_payment_paypal_getway_email');
		register_setting('se_payment_methods', 'se_payment_paypal_api_user_name');
		register_setting('se_payment_methods', 'se_payment_paypal_api_password');
		register_setting('se_payment_methods', 'se_payment_paypal_api_signature');
		
		register_setting('se_payment_methods', 'se_payment_authorized_sendbox');
		register_setting('se_payment_methods', 'se_payment_authorized_login');
		register_setting('se_payment_methods', 'se_payment_authorized_transaction_key');

		register_setting('se_payment_methods', 'se_payment_check_address');
		register_setting('se_payment_methods', 'se_payment_processing_notice');
		register_setting('se_payment_methods', 'se_payment_processing_percent');

	}

	public function get_selectbox($option_name,$label,$optionsArray, $class= "") {
		 
		// se_get_admin_select_field($label, $optionsArray, $defaultValue = "", $name = "", $classes = "")
		se_get_admin_select_field($label, $optionsArray, get_option($option_name) , $option_name, $class);

	}

	public function get_textbox($option_name,$label, $class= "") {
		 
		$class = $class ? ' class="'.$class.'"' : ''; 
		$value = get_option($option_name) ? ' value="'.get_option($option_name).'"' : '';
		?>
		<label for="<?php echo $option_name; ?>"><?php echo $label; ?></label>
		<input  type="text" id="<?php echo $option_name; ?>" name="<?php echo $option_name; ?>" <?php echo $value.$class; ?> /><br>
		<?php
	}

	public function get_checkbox($option_name,$label, $class= "") {
		
		$checked = get_option($option_name) ? " checked='checked' " : ''; 
		$class = $class ? ' class="'.$class.'"' : ''; 
		?>
		<input  type="checkbox" id="<?php echo $option_name; ?>" name="<?php echo $option_name; ?>" value="true" <?php echo $checked.$class; ?> />
		<label for="<?php echo $option_name; ?>"><?php echo $label; ?></label><br>
		<?php
	}

	public function get_textarea($option_name, $label, $class= "", $default = "" ) {
		 
		$class = $class ? ' class="'.$class.'"' : ''; 
		$value = get_option($option_name) ? get_option($option_name) : $default;

		?>
		<div <?php echo $class; ?>>
			<label for="<?php echo $option_name; ?>"><?php echo $label; ?></label>
			<textarea id="<?php echo $option_name; ?>" name="<?php echo $option_name; ?>" style="height: 172px;width: 98%;"><?php echo $value; ?></textarea>
		</div>
		<?php
	}

} // end of ScoutEventSettingsPage class

if( is_admin() )
    $my_settings_page = new ScoutEventSettingsPage();