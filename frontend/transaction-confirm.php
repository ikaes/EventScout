<?php                                                                                                                                                                                          
/*
Example link: http://DOMAIN.COM/se-confirm/?tnx=81

*/

if (!defined('TRANSACTION_CONFIRM_LINK'))
	define('TRANSACTION_CONFIRM_LINK', 'se-confirm');

function se_confirm_link_add_endpoint() {
        // register a "json" endpoint to be applied to posts and pages
        add_rewrite_endpoint( TRANSACTION_CONFIRM_LINK, EP_PERMALINK | EP_PAGES );
        add_rewrite_tag( '%tnx%', '(.*)' );
}
add_action( 'init', 'se_confirm_link_add_endpoint' );

function se_confirm_link_template_redirect() {
        global $wp_query;
        $urlParams = explode('/', $_SERVER['REQUEST_URI']);
        // var_dump($urlParams);

        // if this is not a request for json or it's not a singular object then bail
        // if ( ! isset( $wp_query->query_vars[TRANSACTION_CONFIRM_LINK] ) )

        if($wp_query->query_vars['name'] != TRANSACTION_CONFIRM_LINK)
                return;
        if (   empty( get_query_var( 'tnx' ) )   )
                return;
        
        if (   $urlParams[1]!= TRANSACTION_CONFIRM_LINK && $urlParams[2]!= TRANSACTION_CONFIRM_LINK  )
                return;


        // output some JSON (normally you might include a template file here)
        se_confirm_transaction_template();
        exit;
}
add_action( 'template_redirect', 'se_confirm_link_template_redirect' );

function se_confirm_transaction_template() {
        // header( 'Content-Type: application/json' );

        // $tnx = get_query_var( 'tnx' );
        // var_dump($tnx);

        include plugin_dir_path( __FILE__ ) . 'templates/confirm-transaction-template.php';

        // $post = get_queried_object();
        // echo json_encode( $post );
}

function se_confirm_transaction_activate() {
        // ensure our endpoint is added before flushing rewrite rules
        se_confirm_link_add_endpoint();
        // flush rewrite rules - only do this on activation as anything more frequent is bad!
        flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'se_confirm_transaction_activate' );

function se_confirm_transaction_deactivate() {
        // flush rules on deactivate as well so they're not left hanging around uselessly
        flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'se_confirm_transaction_deactivate' );



