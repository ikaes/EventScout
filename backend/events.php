<?php 

function scout_events_register() {  

    $labels = array(
        'name'               => esc_html__( 'Scout Events', SCOUT_EVENTS_LNG_TAG ),
        'singular_name'      => esc_html__( 'Scout Event', SCOUT_EVENTS_LNG_TAG ),
        'add_new'            => esc_html__( 'Add New Event', SCOUT_EVENTS_LNG_TAG ),
        'add_new_item'       => esc_html__( 'Add New Event', SCOUT_EVENTS_LNG_TAG ),
        'edit_item'          => esc_html__( 'Edit Event', SCOUT_EVENTS_LNG_TAG ),
        'new_item'           => esc_html__( 'Add New Event', SCOUT_EVENTS_LNG_TAG ),
        'view_item'          => esc_html__( 'View Event', SCOUT_EVENTS_LNG_TAG ),
        'search_items'       => esc_html__( 'Search Events', SCOUT_EVENTS_LNG_TAG ),
        'not_found'          => esc_html__( 'No events found', SCOUT_EVENTS_LNG_TAG ),
        'not_found_in_trash' => esc_html__( 'No events found in trash', SCOUT_EVENTS_LNG_TAG )
    );
    
    $args = array(  
        'labels'          => $labels,
        'public'          => true,  
        'show_ui'         => true,  
        'capability_type' => 'post',  
        'hierarchical'    => false,  
        'menu_icon'       => 'dashicons-paperclip',
        'rewrite'         => array('slug' => SCOUT_EVENTS_POST_TYPE), // Permalinks format
        'supports'            => array( 'title', 'editor', 'excerpt' ),
        // 'supports'            => array( 'title', 'editor', 'scout_event_ticket', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields' ),
    );  
  
    register_post_type( SCOUT_EVENTS_POST_TYPE , $args );  
    // remove_post_type_support( 'curated-links' , 'editor' );

    register_taxonomy(
        "event-category",
        array(SCOUT_EVENTS_POST_TYPE),
        array(
            "hierarchical" => true,
            "label" => "Event Categories",
            "singular_label" => "Event Category",
            "rewrite" => true
        )
    );

}
add_action('init', 'scout_events_register', 1);   



function scout_event_edit_columns( $events_columns ) {
    $events_columns = array(
        "cb"          => "<input type=\"checkbox\" />",
        "title"       => esc_html__('Title', SCOUT_EVENTS_LNG_TAG),
        "reg_limit" => esc_html__('Registration Limit', SCOUT_EVENTS_LNG_TAG),
        "sold_ticket" => esc_html__('Tickets Sold', SCOUT_EVENTS_LNG_TAG),
        "event_start" => esc_html__('Event Start Date', SCOUT_EVENTS_LNG_TAG),
        "event_end" => esc_html__('Event End Date', SCOUT_EVENTS_LNG_TAG),
        "date"        => esc_html__('Date', SCOUT_EVENTS_LNG_TAG),
    );
    return $events_columns;
}

/* ----------------------------------------------------- */

function scout_event_column_display( $events_columns, $post_id ) {
    switch ( $events_columns ) {
                
        case "source-category":     
            if ( $category_list = get_the_term_list( $post_id, 'source-category', '', ', ', '' ) ) {
                echo $category_list; // No need to escape
            } else {
                echo esc_html__('None', SCOUT_EVENTS_LNG_TAG);
            }
        break;   
        case "reg_limit": 
            $event_metadata = se_get_event_metadata($post_id);    
            echo $event_metadata['event_reg_limit'];
        break; 
        case "sold_ticket":     
            echo se_count_sold_ticket($post_id);
        break;   
        case "event_start": 
            $event_metadata = se_get_event_metadata($post_id);    
            echo $event_metadata['event_start_date'];
        break;   
        case "event_end": 
            $event_metadata = se_get_event_metadata($post_id);    
            echo $event_metadata['event_end_date'];
        break;     
    }
}

add_filter( 'manage_edit-'.SCOUT_EVENTS_POST_TYPE.'_columns', 'scout_event_edit_columns' );
add_action( 'manage_posts_custom_column', 'scout_event_column_display', 10, 2 );





/* CONTACT META BOXES */
function scout_event_meta_boxes() {
    add_meta_box( 'scout_event_ticket', 'Event DateTime and Ticket', 'backend_event_ticket_html', SCOUT_EVENTS_POST_TYPE );
}
add_action( 'add_meta_boxes', 'scout_event_meta_boxes' );




function scout_event_save_post_meta( $post_id ) {

    if( ! isset( $_POST['scout_event_meta_box_nonce'] ) )
        return;     
    if( ! wp_verify_nonce( $_POST['scout_event_meta_box_nonce'], 'scout_event_save_post_meta') ) 
        return;     
    if( ! current_user_can( 'edit_post', $post_id ) ) 
        return;

    $ticket_IDs = $_POST['ticket_IDs'];
    $ticket_total_rows = $_POST['ticket_total_rows'];
    $ticket_data = $_POST['edit_tickets']; 
  
    update_post_meta( $post_id, 'se_start_date', $_POST['datetime_start_date'] );    
    update_post_meta( $post_id, 'se_end_date', $_POST['datetime_end_date'] );    
    update_post_meta( $post_id, 'se_reg_limit', $_POST['datetime_reg_limit'] );    
    update_post_meta( $post_id, 'se_ticket_id', $ticket_IDs );    
    update_post_meta( $post_id, 'se_total_tickets', $ticket_total_rows );    

    update_post_meta( $post_id, '_se_ticket_meta_key', $ticket_data );    
}

add_action( 'save_post', 'scout_event_save_post_meta' );


