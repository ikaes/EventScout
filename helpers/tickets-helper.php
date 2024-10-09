<?php 


function get_event_tickets($event_id)
{
    $tickets = get_post_meta( $event_id, '_se_ticket_meta_key', true );
    return $tickets;
}

function se_get_tickets_by_event_id($event_id) {
    $ticket_list = [];
    if (!empty($event_id)) {

        $tickets = get_event_tickets($event_id); 

        if (is_array($tickets))
            foreach ($tickets as $key => $ticket) 
                $ticket_list[ $ticket["id"] ] = $ticket["name"];

    }
    return $ticket_list;
}

function se_get_ticket_name($event_id, $ticket_id) {
    $ticket_name = '';
    if (!empty($event_id)) {

        $tickets = get_event_tickets($event_id); 

        if (is_array($tickets))
            foreach ($tickets as $key => $ticket) 
                if ($ticket["id"] == $ticket_id)
                    $ticket_name = $ticket["name"];

    }
    return $ticket_name;
}



function se_count_reserved_tickets($event_id)
{
    $reserved_tickets = [];
    $tickets = get_event_tickets($event_id);

    $reserved_registrations = se_get_reserved_registrations_of_event($event_id);
    
    foreach ($tickets as $key => $ticket)
        $reserved_tickets[$key] = 0;

    foreach ($reserved_registrations as $registration) {

        $reg_info_meta_data = se_get_registration_meta($registration->ID);
        $ticket_id = $reg_info_meta_data[3];

        foreach ($tickets as $key => $ticket) {

            if ($ticket["id"] == $ticket_id) {
                $reserved_tickets[ $ticket_id ] =  $reserved_tickets[ $ticket_id ] + 1;
            }
        }
    }

    return $reserved_tickets;    
}

function se_count_sold_ticket($event_id) {
    $reserved_registrations = se_get_reserved_registrations_of_event($event_id);
    return count($reserved_registrations);
}

function se_get_available_tickets($event_id)
{
    $filterd_tickets = [];
    $tickets = get_event_tickets($event_id);

    // $currentDateTime = date('Y-m-d H:i:s');
    // echo $currentDateTime;

    $today = strtotime( date("Y-m-d h:i a", strtotime('+6 hours')) );
    
    if(isEventAvailable($event_id))
    foreach ($tickets as $key => $ticket) {

        if ( $today > strtotime($ticket['start_date'])
            && $today < strtotime($ticket['end_date'])
        ) {
            $filterd_tickets[$key] = $ticket;
        }
    }

    return $filterd_tickets;
}


function isTicketAvailable($event_id, $ticket_id)
{
    $available_tickets = se_get_available_tickets($event_id);

    $eventStatus = isEventAvailable($event_id);

    return 0;
}


function se_get_ticket($event_id, $ticket_id) {

    $selected_ticket = [];

    $tickets = get_event_tickets($event_id);    
    
    foreach ($tickets as $key => $ticket) {
        if ($ticket["id"] == $ticket_id) {
            $selected_ticket = $tickets[$key];
        }
    }
    
    return $selected_ticket;

}


function se_get_ticket_price($event_id, $ticket_id) {

    $selected_ticket = se_get_ticket($event_id, $ticket_id);
    
    return $selected_ticket['prices'];
}


function se_get_tickets_by_registration($registration_id)
{
    $reg_info_meta_data = get_post_meta( $registration_id, 'se_registration_info_meta_key', true );

    $event_id = $reg_info_meta_data[2];
    $ticket_id = $reg_info_meta_data[3];
    
    $tickets = get_event_tickets($event_id);    
    
    $selected_ticket['name'] = '';
    if (is_array($tickets))
    foreach ($tickets as $key => $ticket) {
        if ($ticket["id"] == $ticket_id) {
            $selected_ticket = $tickets[$key];
        }
    }
    return $selected_ticket;
}


function se_get_tickets($isEcho = '') {

    $event_id = $_GET['event_id'];
    
    $output = [];

    $output[] = "<option value=\"\" selected> — Select — </option>";

    if ($event_id != "") {

        $event_data = get_post_meta( $event_id, '_scout_event_meta_key', true );
        $tickets = se_get_available_tickets($event_id);
        
        foreach ($tickets as $ticket) {
            $output[] = "<option value=\"{$ticket['id']}\">{$ticket['name']}</option>";
            
        }
    }   
    if($isEcho == ''){
        echo implode('', $output);
    }
    else 
        return implode('', $output);

    die();
}
add_action( 'wp_ajax_se_get_tickets', 'se_get_tickets' );
add_action( 'wp_ajax_nopriv_se_get_tickets', 'se_get_tickets' );




function se_get_tickets_and_calender_langth() {

    $ticketsHTML = se_get_tickets('no');
    $event_id = $_GET['event_id'];
    $eventData = se_get_event_metadata($event_id);

    echo json_encode(  array($ticketsHTML, $eventData)  );

    die();
}
add_action( 'wp_ajax_se_get_tickets_and_calender_langth', 'se_get_tickets_and_calender_langth' );
add_action( 'wp_ajax_nopriv_se_get_tickets_and_calender_langth', 'se_get_tickets_and_calender_langth' );




function se_get_event_and_ticket_price() {

    $event_id = $_GET['event_id'];
    $ticket_id = $_GET['ticket_id'];

    $eventData = se_get_event_metadata($event_id);
    $ticketData = se_get_ticket($event_id, $ticket_id);

    echo json_encode(  array($ticketData, $eventData)  );

    die();
}
add_action( 'wp_ajax_se_get_event_and_ticket_price', 'se_get_event_and_ticket_price' );
add_action( 'wp_ajax_nopriv_se_get_event_and_ticket_price', 'se_get_event_and_ticket_price' );

