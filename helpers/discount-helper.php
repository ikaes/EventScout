<?php 

function se_get_discount_meta($discount_id)
{
	$discount_data = [];
	$discount_data['type'] = get_post_meta( $discount_id, 'discount_type', true );
	$discount_data['percentage'] = get_post_meta( $discount_id, 'discount_percentage', true );
	$discount_data['amount'] = get_post_meta( $discount_id, 'discount_amount', true );
	$discount_data['promo_code'] = get_post_meta( $discount_id, 'discount_promo_code', true );
	$discount_data['valid_from'] = get_post_meta( $discount_id, 'discount_valid_from', true );
	$discount_data['valid_to'] = get_post_meta( $discount_id, 'discount_valid_to', true );

    return $discount_data;
}


function se_get_current_discounts() {

	global $wpdb;
	
				// 2018-06-20 12:00 am
    $today = date('Y-m-d h:i a', strtotime('+6 hours'));

    $discount_query = array(
        'post_type' => SCOUT_DISCOUNT_POST_TYPE,
        'posts_per_page'    => -1,
        'post_status' => 'publish',
		'meta_query'        => array(
	        'relation'  => 'AND',
	        array(
	            'key'       => 'discount_valid_from',
	            'value'     => $today,
	            'compare'   => '<=', // if discount_valid_from <= $today
	        ),
	        array(
	            'key'       => 'discount_valid_to',
	            'value'     => $today,
	            'compare'   => '>=', // if discount_valid_to >= $today
	        ),
	    ),

    );

	// $discounts =query_posts( $discount_query );
	$discounts = new WP_Query( $discount_query );
	
	return $discounts->posts;
}

function calculate_discount($price, $percentage) {
	$price = floatval($price);
	$percentage = floatval(  intval($percentage) / 100  );
	$discount = number_format( ($price * $percentage) , 2, '.', '' );
	return $discount;
}

function se_check_promo_code($promo_code, $event_id, $ticket_id)
{
	$discounts = se_get_current_discounts();

	foreach ($discounts as $discount) {
		$discount_data = se_get_discount_meta($discount->ID);
		if ($promo_code == $discount_data['promo_code']) {

			$price = floatval(se_get_ticket_price($event_id, $ticket_id));

			if ($discount_data['type'] == '%')
				$discount_data['discount'] = calculate_discount($price, $discount_data['percentage']);
			else
				$discount_data['discount'] = $discount_data['amount'] ? floatval($discount_data['amount']) : 0;
			
			$discount_data['price'] = $price;
			$discount_data['price_after_discount'] = $price - $discount_data['discount'];

			return $discount_data;
		}
	}
	return false;
}


function se_apply_promo_code_ajax_func() {

	$promo_code = $_GET['promo_code'];
	$event_id = $_GET['event_id'];
	$ticket_id = $_GET['ticket_id'];

	$discount_data = se_check_promo_code($promo_code, $event_id, $ticket_id);

	echo json_encode($discount_data);

	die();
}
add_action( 'wp_ajax_se_apply_promo_code_ajax_func', 'se_apply_promo_code_ajax_func' );
add_action( 'wp_ajax_nopriv_se_apply_promo_code_ajax_func', 'se_apply_promo_code_ajax_func' );



function se_get_discount_types()
{
	$discoutn_type = [
	    '%' => 'Percentage',
	    'fixed' => 'Fixed Amount',
	];
	return $discoutn_type;
}

