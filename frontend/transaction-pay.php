<?php                                                                                                                                                                                          
/*
Example link: http://DOMAIN.COM/se-pay/?tnx=81

*/

if (!defined('TRANSACTION_PAY_LINK'))
	define('TRANSACTION_PAY_LINK', 'se-pay');

function se_pay_link_add_endpoint() {
        // register a "json" endpoint to be applied to posts and pages
        add_rewrite_endpoint( TRANSACTION_PAY_LINK, EP_PERMALINK | EP_PAGES );
        add_rewrite_tag( '%tnx%', '(.*)' );
}
add_action( 'init', 'se_pay_link_add_endpoint' );

function se_pay_link_template_redirect() {
        global $wp_query;
        $urlParams = explode('/', $_SERVER['REQUEST_URI']);

        // if this is not a request for json or it's not a singular object then bail
        // if ( ! isset( $wp_query->query_vars[TRANSACTION_PAY_LINK] ) )

        if($wp_query->query_vars['name'] != TRANSACTION_PAY_LINK)
                return;
        if (   empty( get_query_var( 'tnx' ) )   )
                return;
        
        if (   $urlParams[1]!= TRANSACTION_PAY_LINK && $urlParams[2]!= TRANSACTION_PAY_LINK  )
                return;

        // output some JSON (normally you might include a template file here)
        se_pay_transaction_template();
        exit;
}
add_action( 'template_redirect', 'se_pay_link_template_redirect' );

function se_pay_transaction_template() {
        // header( 'Content-Type: application/json' );

        // $tnx = get_query_var( 'tnx' );
        // var_dump($tnx);

        include plugin_dir_path( __FILE__ ) . 'templates/pay-transaction-template.php';

        // $post = get_queried_object();
        // echo json_encode( $post );
}

function se_pay_transaction_activate() {
        // ensure our endpoint is added before flushing rewrite rules
        se_pay_link_add_endpoint();
        // flush rewrite rules - only do this on activation as anything more frequent is bad!
        flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'se_pay_transaction_activate' );

function se_pay_transaction_deactivate() {
        // flush rules on deactivate as well so they're not left hanging around uselessly
        flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'se_pay_transaction_deactivate' );



