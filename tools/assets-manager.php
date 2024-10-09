<?php 
function scout_event_load_admin_script() {

    $ajax_info = array( 'ajax_url' =>  admin_url( 'admin-ajax.php' ));
    
    wp_register_style( 'jquery-ui-timepicker-addon', SE_ASSETS_URL.'/css/jquery-ui-timepicker-addon.css', false, '1.0.0' );
    wp_register_style( 'jquery-ui-custom', SE_ASSETS_URL.'/css/jquery-ui-1.10.3.custom.min.css', false, '1.0.0' );

    wp_register_style( 'se-backend', SE_ASSETS_URL.'/css/scout-event-backend.css', false, '1.0.0' );
    wp_enqueue_style( 'se-backend' );

    wp_register_script( 'jquery-ui-timepicker-addon', SE_ASSETS_URL.'/js/jquery-ui-timepicker-addon.js', array('jquery-ui-datepicker'), '', true );

    wp_register_script( 'scout-events-admin-scripts', SE_ASSETS_URL.'/js/scout-events-admin.js', array(), '', true );
    wp_register_script( 'scout-events-manager-admin-scripts', SE_ASSETS_URL.'/js/scout-events-manager-admin.js', array(), '', true );
    wp_register_script( 'admin-discount-manager', SE_ASSETS_URL.'/js/admin-discount-manager.js', array(), '', true );

    wp_localize_script( 
        'scout-events-admin-scripts', 
        'se_ajax_vars', 
        $ajax_info
    );       
}

add_action('admin_enqueue_scripts', 'scout_event_load_admin_script');


function scout_event_load_site_script() {


	$ajax_info = array( 'ajax_url' =>  admin_url( 'admin-ajax.php' ));
    $ajax_info['home_url'] = get_home_url();
	$ajax_info['porcessingPercentage'] = se_get_payment_processing_percent();

    wp_register_style( 'se_checkout', SE_ASSETS_URL.'/css/scout-event-checkout.css', false, '1.0.0' );

    wp_register_style( 'jquery-ui-timepicker-addon', SE_ASSETS_URL.'/css/jquery-ui-timepicker-addon.css', false, '1.0.0' );
    wp_register_style( 'jquery-ui-custom', SE_ASSETS_URL.'/css/jquery-ui-1.10.3.custom.min.css', false, '1.0.0' );

    // wp_register_script( 'customSelect', SE_ASSETS_URL.'/js/jquery.customSelect.js', array('jquery'), '', true );
    wp_register_script( 'jquery-validate', SE_ASSETS_URL.'/js/jquery.validate.js', array('jquery'), '', true );
    wp_register_script( 'jquery-validate-additional-methods', SE_ASSETS_URL.'/js/additional-methods.js', array('jquery','jquery-validate'), '', true );
    wp_register_script( 'jquery-ui-timepicker-addon', SE_ASSETS_URL.'/js/jquery-ui-timepicker-addon.js', array('jquery-ui-datepicker'), '', true );
    wp_register_script( 'se_checkout', SE_ASSETS_URL.'/js/scout-events-checkout.js', array('jquery'), '', true );

	wp_localize_script( 
	    'se_checkout', 
	    'se_ajax_vars', 
	    $ajax_info
	);
	
}

add_action('wp_enqueue_scripts', 'scout_event_load_site_script');


// add_action('admin_footer', 'my_admin_add_js');
// function my_admin_add_js() {
//     echo '<script type="text/javascript">jQuery(".piechart").GoogleChart();</script>';
// }


