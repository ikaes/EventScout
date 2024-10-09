<?php 
function se_scout_event_booking_func($atts, $content = null ) {  // example: [scout_event_booking]
    $ats = shortcode_atts( array(
        'class' => '',
    ), $atts );

    wp_enqueue_style( 'se_checkout' );
    wp_enqueue_style( 'jquery-ui-timepicker-addon');
    wp_enqueue_style( 'jquery-ui-custom');

    // wp_enqueue_script('jquery-ui-accordion');
    wp_enqueue_script('jquery-ui-timepicker-addon');
    
    wp_enqueue_script('jquery-validate');
    wp_enqueue_script('jquery-validate-additional-methods');
    // wp_enqueue_script( 'customSelect' );
    wp_enqueue_script('se_checkout');
    
    $output = [];

    $output[] = '<div class="se-event-booking ' . esc_attr($a['class']) . '">';

    $output[] = se_get_workshop_booking_layout();
    $output[] = se_get_personal_info_booking_layout();
    $output[] = se_get_passport_info_booking_layout();
    $output[] = se_get_payment_option_booking_layout();
    $output[] = se_get_order_review_booking_layout();
    $output[] = se_get_timeline_layout();

    $output[] = $content;
    $output[] = '</div>';
    
    return implode("\n", $output);
}

add_shortcode( 'scout_event_booking', 'se_scout_event_booking_func' );



function se_get_timeline_layout($value='') {

    $output[] = '<div class="checkout-steps">';
		$output[] = '<div class="se-checkout-timeline">';
		    // $output[] = '<div class="checkout-line"></div>';
		    $output[] = '<div class="step step-1 active">';
		        $output[] = '<div class="box-number">1</div>';
		        $output[] = '<div class="text">';
		            $output[] = '<em>Workshops and Accommodations</em>';
		        $output[] = '</div>';
		    $output[] = '</div>';
		    $output[] = '<div class="step step-2">';
		        $output[] = '<div class="box-number">2</div>';
		        $output[] = '<div class="text">';
		            $output[] = '<em>Personal Information</em>';
		        $output[] = '</div>';
		    $output[] = '</div>';
		    $output[] = '<div class="step step-3">';
		        $output[] = '<div class="box-number">3</div>';
		        $output[] = '<div class="text">';
		            $output[] = '<em>Passport Information</em>';
		        $output[] = '</div>';
		    $output[] = '</div>';
		    $output[] = '<div class="step step-4">';
		        $output[] = '<div class="box-number">4</div>';
		        $output[] = '<div class="text">';
		            $output[] = '<em>Payment Options</em>';
		        $output[] = '</div>';
		    $output[] = '</div>';
		    $output[] = '<div class="step step-5">';
		        $output[] = '<div class="box-number">5</div>';
		        $output[] = '<div class="text">';
		            $output[] = '<em>Order Review</em>';
		        $output[] = '</div>';
		    $output[] = '</div>';
		$output[] = '</div>';
	$output[] = '</div>';

	return implode("\n", $output);	
}

function se_get_pricing_item($title, $class='')
{
	$output[] = '<div class="'.$class.'">';
		$output[] = '<span class="pricing-title">'.$title.'</span>';
		$output[] = '<span class="pricing">';
			$output[] = '<span class="pricing-pre">$</span>';
			$output[] = '<span class="pricing-amount">0.00</span>';
		$output[] = '</span>';
	$output[] = '</div>';
	return implode('', $output);
}

function se_get_event_subtotal_layout() {

	$output[] = '<div class="pricing-container text-right">';

		$output[] = se_get_pricing_item('Ticket Price:','ticket-price');
		$output[] = se_get_pricing_item('Discount:','discount-price');
		$output[] = se_get_pricing_item( se_get_payment_processing_percent().' % Processing Fee:','processing-price');
		$output[] = se_get_pricing_item('TOTAL:','subtotal-price');

	$output[] = '</div>';

	$output[] = '<div class="se_deposit_box pricing-container text-right" style="display:none;">';
	    $output[] = '<div class="text-right pay_now_container">';
	        $output[] = '<span class="pricing">Pay Now:</span>';
	        $output[] = '<span class="pricing pay_now">';
	            $output[] = '<span class="pricing-pre">$</span>';
	            $output[] = '<span class="pricing-amount">0</span>';
	        $output[] = '</span>';
	    $output[] = '</div>';
	    $output[] = '<div class="text-right pay_later_container">';
	        $output[] = '<span class="pricing">Pay Later:</span>';
	        $output[] = '<span class="pricing pay_later">';
	            $output[] = '<span class="pricing-pre">$</span>';
	            $output[] = '<span class="pricing-amount">0</span>';
	        $output[] = '</span>';
	    $output[] = '</div>';
	$output[] = '</div>';

	return implode("\n", $output);
}

function se_get_event_checkout_pagination($backClick,$nextClick,$isBackButton=true,$BackTitle='Back',$isNextButton=true,$NextTitle='Next Step', $isLoading = false) {

	$output[]= '<div class="se_row margin-top margin-bottom checkout_pagination">';
		$output[]= '<div class="se-wid50">';
			$output[]= '<div class="cc-logos">';
				$output[]= '<img src="'.SE_ASSETS_URL.'/images/cc.png" alt="">';
			$output[]= '</div>';
		$output[]= '</div>';
		$output[]= '<div class="se-wid50 text-right">';
			if($isLoading)	
				$output[]= '<img class="se-spinner" src="'.SE_ASSETS_URL.'/images/spinner30px.gif" style="display: none;">';
			if($isBackButton)	
				$output[]= '<button class="btn btn-go-back btn-wide" onclick="rawNextStep(\''.$backClick.'\', this)">'.$BackTitle.'</button>';
			if($isNextButton)	{
				if ($nextClick =="checkout") {					
					$output[]= '<button class="btn btn-wide " type="submit">'.$NextTitle.'</button>';
				} else 
				$output[]= '<button class="btn btn-wide " onclick="rawNextStep(\''.$nextClick.'\', this)">'.$NextTitle.'</button>';
			}
		$output[]= '</div>';
	$output[]= '</div>';

	return implode("\n", $output);
}

function se_get_calender_layout($value='') {

	$output[] = '<div class="margin-top calender-section se-wid50">';
		$output[] = '<span id="date_shower" ></span>';
	$output[]= '</div>'; // end of checkout-section
	return implode("\n", $output);	
}
function se_get_toggler_layout($value='') {
	$output[] = '<div class="margin-top margin-bottom toggler-section se-wid50">';
		$output[] = '<h3 class="" style="display:none;">WORKSHOPS AND ACCOMMODATIONS</h3>';
		$output[] = '<div class="event-toggler" style="display:none;">';
			$output[] = '<h3 class="toggle-title">';
				$output[] = '<span>WORKSHOP: </span>';
				$output[] = '<span class="title">SESSION 2</span>';
				$output[] = '<span class="date">JULY 8, 2018-JULY 17, 2018</span>';
			$output[] = '</h3>';

			$output[] = '<div class="toggle-content">';
				$output[] = '<div>';
					$output[] = '<span>';
						$output[] = '<strong>WORKSHOP:</strong>';
						$output[] = '<span class="title">SESSION 2</span>';
					$output[] = '</span>';
				$output[] = '</div>';
				$output[] = '<p>';
					$output[] = '<strong>DATES:</strong>';
					$output[] = '<span class="date">july 8, 2018-july 17, 2018</span>';
				$output[] = '</p>';
				$output[] = '<div class="deposit"><strong>DEPOSIT:</strong>$<span class="amount">1900</span> due upon registration</div>';
			$output[] = '</div>';
		$output[] = '</div>';

		$output[] = '<div class="ticket-toggler" style="display:none;">';
			$output[] = '<h3 class="toggle-title">';
				$output[] = '<span>ACCOMMODATION: </span>';
				$output[] = '<span class="date">JULY 8, 2018-JULY 17, 2018</span>';
			$output[] = '</h3>';
			$output[] = '<div class="toggle-content">';
				$output[] = '<div>';
					$output[] = '<span>';
						$output[] = '<strong>ACCOMMODATION:</strong>';
						$output[] = '<span class="title">Standard Shared</span>';
					$output[] = '</span>';
				$output[] = '</div>';
				$output[] = '<p>';
					$output[] = '<strong>DATES:</strong>';
					$output[] = '<span class="date">july 8, 2018-july 17, 2018</span>';
				$output[] = '</p>';
				$output[] = '<div class="deposit"><strong>DEPOSIT:</strong>$<span class="amount">1900</span> due upon registration</div>';
			$output[] = '</div>';
		$output[] = '</div>';
	$output[]= '</div>'; // end of checkout-section
	return implode("\n", $output);	
}

function se_get_workshop_booking_layout($value='') {

	$events = se_get_workshop_current_events();


	$output[] = '<div class="margin-top se_row checkout-header">';
		$output[] = se_get_calender_layout();
		$output[] = se_get_toggler_layout();
	$output[] = '</div>';

	$output[] = '<div class="margin-top checkout-section workshop" style="display: block">';
	$output[] = '<form id="se_workshop" action="javascript:void(0);">';
		// $output[] = wp_nonce_field( 'se_registration_save_post_meta', 'se_registration_meta_box_nonce', true, false );
		$output[] = '<div class="se_row">';
			$output[] = '<div class="se-wid50">';
			$output[] = '<h2>WORKSHOP(S)</h2>';
			$output[] = '<div class="margin-top margin-bottom">';
				$output[] = '<select id="raw-event-select" name="raw-event-select" class="custom_select_box" required>';

					$output[] = '<option value="">Please Select a Workshop *</option>';

					foreach ($events as $event) {
						$output[] = '<option value="'.$event->ID.'">'.$event->post_title.'</option>';
					}
			
				$output[] = '</select>';
			$output[] = '</div>';
			$output[] = '</div>';


			$output[] = '<div class="accomodation se-wid50">';
			$output[] = '<h2>ACCOMMODATION</h2>';
			$output[] = '<div class="margin-top margin-bottom">';
				$output[] = '<select id="raw-ticket-select" name="raw-ticket-select" class="custom_select_box" required>';
					$output[] = '<option value="">- Select -</option>';
				$output[] = '</select>';
			$output[] = '</div>';
			$output[] = '</div>'; // end of accomodation
			//Refund policy checkbox
			// $output[] = '<div class="refundbox">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="refundbox" id="refundbox"> ';
			// 	$refundboxlabel=get_option('refund_ack_consent');
			// 	$output[] = $refundboxlabel;
			// $output[] = '</div>';
		$output[] = '</div>'; 
							
		$output[] = '<input type="hidden" name="se_ticket_price" id="se_ticket_price" value="0">';

		$output[] = se_get_event_subtotal_layout();

				// se_get_event_checkout_pagination($backClick,$nextClick,$isBackButton=true,$BackTitle='Back',$isNextButton=true,$NextTitle='Next Step', $isLoading = false)
		$output[] = se_get_event_checkout_pagination('','personal',false);

	$output[]= '</form>'; // end of form
	$output[]= '</div>'; // end of checkout-section

	// wp_reset_postdata();
	return implode("\n", $output);	
}


function se_get_personal_info_booking_layout($value='') {

	$output[] = '<div class="margin-top checkout-section personal-info">';
	$output[] = '<form id="se_personal-info" action="javascript:void(0);">';

		$output[] = '<div class="se_row">';
			$output[] = '<div class=" se-wid50">';
				$output[] = '<h2>PERSONAL INFORMATION</h2>';
				$output[] = '<div class="margin-top margin-bottom">';

					$output[] = '<ul class="form-list margin-bottom">';
						$output[] = '<li>';
							$output[] = '<div class="se_row">';
								$output[] = '<div class="se-xl-wid50">';
									$output[] = '<input type="text" required="" placeholder="First Name*" name="se_first_name" value="">';
								$output[] = '</div>';
								$output[] = '<div class="se-xl-wid50">';
									$output[] = '<input type="text" required="" placeholder="Last Name*" name="se_last_name" value="">';
								$output[] = '</div>';
							$output[] = '</div>';
						$output[] = '</li>';
						$output[] = '<li>';
							$output[] = '<input type="email" required="" placeholder="E-mail Address*" name="se_email" value="">';
						$output[] = '</li>';
						$output[] = '<li>';
							$output[] = '<input type="text" required="" placeholder="Mailing Street Address*" name="se_street_address" value="">';
						$output[] = '</li>';
						$output[] = '<li>';
							$output[] = '<div class="se_row">';
								$output[] = '<div class="se-xl-wid50">';
									$output[] = '<input type="text" placeholder="City" name="se_city" value="">';
								$output[] = '</div>';
								$output[] = '<div class="se-xl-wid50">';
									$output[] = '<input type="text" placeholder="State/Province" name="se_state" value="">';
								$output[] = '</div>';
							$output[] = '</div>';
						$output[] = '</li>';
						$output[] = '<li>';
							$output[] = '<div class="se_row">';
								$output[] = '<div class="se-xl-wid50">';
									$output[] = '<input type="text" placeholder="Postal/Zip Code" name="se_postal" value="">';
								$output[] = '</div>';
								$output[] = '<div class="se-xl-wid50">';
									$output[] = countrySelector("", "raw-country", "raw-country","countries custom_select_box");
								$output[] = '</div>';
							$output[] = '</div>';
						$output[] = '</li>';
						$output[] = '<li>';
							$output[] = '<input type="text" required="" placeholder="Phone #" name="se_phone" value="">';
						$output[] = '</li>';
					$output[] = '</ul>';

				$output[] = '</div>';
			$output[] = '</div>';


			$output[] = '<div class="emergency se-wid50">';

				$output[] = '<h2>EMERGENCY CONTACT</h2>';
				$output[] = '<div class="margin-top margin-bottom">';
				$output[] = '<ul class="form-list">';
					$output[] = '<li><input type="text" placeholder="Primary Contact (First, Last Name)" name="se_emrgnc_contact"></li>';
					$output[] = '<li><input type="text" placeholder="Relationship of Primary Contact" name="se_emrgnc_rel_contact"></li>';
					$output[] = '<li><input type="text" placeholder="Phone #" name="se_emrgnc_phone" value=""></li>';
					$output[] = '<li><input type="email" placeholder="E-mail Address" name="se_emrgnc_email" value=""></li>';
				$output[] = '</ul>';
				$output[] = '</div>';

				$output[] = '<h2>PROMOTIONAL CODES</h2>';
				$output[] = '<div class="margin-top margin-bottom">';
				$output[] = '<ul class="form-list">';
					$output[] = '<li><input type="text" placeholder="Promotion" name="se_emrgnc_promotion" class="promotion-input" value="" /><a class="se_promo_applier" href="">Apply Code</a></li>';
				$output[] = '</ul>';
				$output[] = '</div>';

			$output[] = '</div>'; // end of emergency

		
		$output[] = '</div>'; 

		$output[] = se_get_event_subtotal_layout();

				// se_get_event_checkout_pagination($backClick,$nextClick,$isBackButton=true,$BackTitle='Back',$isNextButton=true,$NextTitle='Next Step', $isLoading = false)
		$output[] = se_get_event_checkout_pagination('workshop','passport');

	$output[]= '</form>';// end of form
	$output[]= '</div>';// end of checkout-section

	return implode("\n", $output);	
}



function se_get_passport_info_booking_layout($value='') {

	$output[] = '<div class="margin-top checkout-section passport-info">';
	$output[] = '<form id="se_passport-info" action="javascript:void(0);">';
		$output[] = '<div class="se_row">';
			$output[] = '<div class="se-wid50">';
				$output[] = '<h2>PASSPORT INFORMATION</h2>';
				$output[] = '<div class="margin-top margin-bottom">';

					$output[] = '<ul class="form-list margin-bottom">';
						$output[] = '<li><input type="text" placeholder="Citizenship" name="se_citizen" data-name="citizenship" value=""></li>';
						$output[] = '<li>';
							$output[] = '<input type="date" placeholder="Birthdate" name="se_birthday" data-name="birthdate" value="">';
							$output[] = '<label><small>Birthdate MM/DD/YYYY</small></label>';
						$output[] = '</li>';
						$output[] = '<li><input type="text" placeholder="Place of Birth" name="se_birth_place" data-name="place of birth" value=""></li>';
						$output[] = '<li>';
							$output[] = '<select class="select-passport custom_select_box" name="passport-status">';
								$output[] = '<option selected="" disabled="">Passport Status</option>';
								$output[] = '<option value="valid">Currently posess a valid passport</option>';
								$output[] = '<option value="applying">Currently applying for passport</option>';
								$output[] = '<option value="renew">Currently renewing passport</option>';
							$output[] = '</select>';
						$output[] = '</li>';

						$output[] = '<li><input type="text" placeholder="Passport Number" name="se_pp_name" data-name="passport-number" value=""></li>';
						$output[] = '<li>';
							$output[] = '<div class="se_row margin-bottom">';
								$output[] = '<div class="se-wid50">';
									$output[] = '<input type="date" placeholder="Issue Date" name="se_pp_issue" data-name="passport-issue" value="">';
									$output[] = '<label for="passport-issue"><small>Issue Date: MM/DD/YYYY</small></label>';
								$output[] = '</div>';
								$output[] = '<div class="se-wid50">';
									$output[] = '<input type="date" placeholder="Expiration Date" name="se_pp_exp_date" data-name="passport-expiry" value="">';
									$output[] = '<label for="passport-expiry"><small>Expiration Date: MM/DD/YYYY</small></label>';

								$output[] = '</div>';
							$output[] = '</div>';
						$output[] = '</li>';
						$output[] = '<li>';
							$output[] = '<div class="se_row">';
								$output[] = '<div class="se-wid50">';
									$output[] = '<label><input type="radio" name="se_gender" data-name="sex" value="female"> Female</label>';
								$output[] = '</div>';
								$output[] = '<div class="se-wid50">';
									$output[] = '<label><input type="radio" name="se_gender" data-name="sex" value="male" checked> Male</label>';
								$output[] = '</div>';
							$output[] = '</div>';
						$output[] = '</li>';
					$output[] = '</ul>';

				$output[] = '</div>';
			$output[] = '</div>';

		$output[] = '</div>'; 

		$output[] = se_get_event_subtotal_layout();

				// se_get_event_checkout_pagination($backClick,$nextClick,$isBackButton=true,$BackTitle='Back',$isNextButton=true,$NextTitle='Next Step', $isLoading = false)
		$output[] = se_get_event_checkout_pagination('personal','payment');

	$output[]= '</form>'; // end of checkout-section
	$output[]= '</div>'; // end of checkout-section

	return implode("\n", $output);	
}

function se_get_credit_cart_layout($value='') {

	$output[]= '<div class="form-group owner">';
	    $output[]= '<label for="owner">Owner</label>';
	    $output[]= '<input type="text" class="form-control" id="owner" name="owner" required>';
	$output[]= '</div>';
	$output[]= '<div class="form-group CVV">';
	    $output[]= '<label for="cvv">CVV</label>';
	    $output[]= '<input type="text" class="form-control" id="cvv" name="cvv" required>';
	$output[]= '</div>';
	$output[]= '<div class="form-group" id="card-number-field">';
	    $output[]= '<label for="cardNumber">Card Number</label>';
	    $output[]= '<input type="text" class="form-control" name="cardNumber" id="cardNumber" required>';
	$output[]= '</div>';
	$output[]= '<div class="form-group" id="expiration-date">';
	    $output[]= '<label>Expiration Date</label>';
	    $output[]= '<select id="exp_month" name="exp_month" required>';
	        $output[]= '<option value="01">January</option>';
	        $output[]= '<option value="02">February </option>';
	        $output[]= '<option value="03">March</option>';
	        $output[]= '<option value="04">April</option>';
	        $output[]= '<option value="05">May</option>';
	        $output[]= '<option value="06">June</option>';
	        $output[]= '<option value="07">July</option>';
	        $output[]= '<option value="08">August</option>';
	        $output[]= '<option value="09">September</option>';
	        $output[]= '<option value="10">October</option>';
	        $output[]= '<option value="11">November</option>';
	        $output[]= '<option value="12">December</option>';
	    $output[]= '</select>';
	    $output[]= '<select id="exp_year" name="exp_year" required>';
	    	for ($i = 0; $i <= 10; $i++) {	    		
	        	$output[]= '<option value="'. ( intval(date("Y")) + $i ) .'"> '. ( intval(date("Y")) + $i ) .'</option>';
	    	}
	    $output[]= '</select>';
	$output[]= '</div>';

	return implode("\n", $output);	
}

function se_get_payment_option_booking_layout($isback=true) {

	$output[] = '<div class="margin-top checkout-section payment-options">';
	$output[] = '<form id="se_payment-options" action="javascript:void(0);" target="_blank">';
		$output[] = '<div class="se_row">';
			$output[] = '<div class="se-wid50">';

				$output[] = '<h2>PAYMENT OPTIONS</h2>';

				$output[] = '<div class="margin-top margin-bottom">';

					$output[] = '<select name="payment-method" id="se-payment-method" class="custom_select_box" required >';
						$output[] = '<option value="0" disabled="" selected="">Select a Payment Option</option>';
						if (get_option('se_payment_authorized_net'))
						$output[] = '<option value="se_payment_method-cc">Credit Card</option>';
						if (get_option('se_payment_paypal'))
						$output[] = '<option value="se_payment_method-paypal">Paypal</option>';
						if (get_option('se_payment_check'))
						$output[] = '<option value="se_payment_method-check">Check</option>';
					$output[] = '</select>';

					$output[] = '<ul class="form-list" id="se_payment_method-check" style="display: none;">';
					    $output[] = '<li><p><em>If paying by personal check or money order, please make your checks payable to:</em></p></li>';
					    $output[] = '<li><p>'. se_get_payment_check_address() .'</p></li>';
					$output[] = '</ul>';

					$output[] = '<ul class="form-list" id="se_payment_method-paypal" style="display: none;">';
					    $output[] = '<li>';
					    	$output[] = 'If paying by paypal you will be redirected to Paypal to Complete your purchase.';
					    	$output[] = '<img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;">';
					    $output[] = '</li>';
					$output[] = '</ul>';

					$output[] = '<div class="form-list" id="se_payment_method-cc" style="display: none;">';
					    $output[] = '<p>You will enter your credit card details on the last step.</p>';
					    $output[] = se_get_credit_cart_layout();
					    
					$output[] = '</div>';

					$output[] = '<div class="clearfix"></div>';

					$output[] = '<div>';
					    $output[] = '<small>'. se_get_payment_processing_notice() .'<br>Note: 50% balance amounts will be shown for Pay By Check orders an the end of Registration process.</small>';
					$output[] = '</div>';
					// Refund Checkbox
					$output[] = '<div class="refundbox"><br><input type="checkbox" name="refundbox" id="refundbox"> ';
							$refundboxlabel=get_option('refund_ack_consent');
							$output[] = $refundboxlabel;
					$output[] = '</div>';
				$output[] = '</div>';

			$output[] = '</div>';


			$output[] = '<div class="se-wid50">';
				$output[] = '<h2>&nbsp;</h2>';
				$output[] = '<div class="margin-top margin-bottom">';
					$output[] = '<select name="se_event_deposit" id="se_event_deposit" class="custom_select_box">';
						$output[] = '<option value="0" selected="">Pay the Full Balance.</option>';
						$output[] = '<option value="1">Make a 50% Deposit.</option>';
					$output[] = '</select>';
				$output[] = '</div>';
			$output[] = '</div>';
		$output[] = '</div>';

		$output[] = se_get_event_subtotal_layout();

			// se_get_event_checkout_pagination($backClick,$nextClick,$isBackButton=true,$BackTitle='Back',$isNextButton=true,$NextTitle='Next Step', $isLoading = false)
		$output[] = se_get_event_checkout_pagination('passport','order',$isback,'Back',true,'Next Step',true);

		//$output[] = se_get_event_checkout_pagination('passport','order');

	$output[]= '</form>'; // end of form
	$output[]= '</div>'; // end of checkout-section

	return implode("\n", $output);	
}


function se_get_review_row($label,$class,$defaultValue='')
{
	$output[] = '<div class="review-item '.$class.'">';
		$output[] = '<span class="se-review-label">'.$label.'</span>';
		$output[] = '<span class="se-review-value">'.$defaultValue.'</span>';
	$output[] = '</div>';
	return implode("\n", $output);
}

function se_get_order_review_booking_layout($value='') {

	$output[] = '<div class="margin-top checkout-section order-review">';
	$output[] = '<form id="se_order-review" action="'.SE_URL.'/paypal/payments.php" method="post">';

		$output[] = '<h2>Event Ticket Booking Information</h2>';

		$output[] = '<div class="margin-top margin-bottom">';

			$output[] = se_get_review_row('Name:','se_rv_name','');
			$output[] = se_get_review_row('Event:','se_rv_event','');
			$output[] = se_get_review_row('Event Start:','se_rv_event_start','');
			$output[] = se_get_review_row('Event End:','se_rv_event_end','');
			$output[] = se_get_review_row('Ticket:','se_rv_ticket','');
			$output[] = se_get_review_row('Ticket Price:','se_rv_ticket_price','');
			$output[] = se_get_review_row('Payment Method:','se_rv_payment','');
			$output[] = se_get_review_row('Payment Type:','se_rv_payment_type','Full Balance');
			$output[] = se_get_review_row('Booking ID:','se_rv_registration_id','');
			$output[] = se_get_review_row('Booking Status:','se_rv_registration_status','');
			$output[] = se_get_review_row('Transaction ID:','se_rv_tnx_id','');
			$output[] = se_get_review_row('Transaction ID2:','se_rv_tnx_id2','');


			$output[] = '<input type="hidden" name="cmd" value="_xclick" />';
			$output[] = '<input type="hidden" name="no_note" value="1" />';
			$output[] = '<input type="hidden" name="lc" value="US" />';
			$output[] = '<input type="hidden" name="currency_code" value="USD" />';
			$output[] = '<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest" />';
			
			$output[] = '<input type="hidden" name="first_name" id="first_name" value=""  />';
			$output[] = '<input type="hidden" name="last_name" id="last_name" value=""  />';
			$output[] = '<input type="hidden" name="payer_email" id="payer_email" value=""  />';
			$output[] = '<input type="hidden" name="item_number" id="item_number" value="" / >';
			$output[] = '<input type="hidden" name="wp_tnx_id" id="wp_tnx_id" value="" / >';

		$output[] = '</div>';

		$output[] = '<div class="se_row">';
			$output[] = '<div class="se-wid50">';

			$output[] = '</div>';
		$output[] = '</div>';

		$output[] = se_get_event_subtotal_layout();

			// se_get_event_checkout_pagination($backClick,$nextClick,$isBackButton=true,$BackTitle='Back',$isNextButton=true,$NextTitle='Next Step', $isLoading = false)
		$output[] = se_get_event_checkout_pagination('','confirm',false,'Back',true,'Checkout');

	$output[]= '</form>'; // end of form
	$output[]= '</div>'; // end of checkout-section

	return implode("\n", $output);	
}














