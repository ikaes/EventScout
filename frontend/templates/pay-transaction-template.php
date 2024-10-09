<?php 

$transaction_id = get_query_var( 'tnx' );
$transactional_data = se_get_all_transactional_data($transaction_id);
$ticket_list = se_get_tickets_by_event_id($transactional_data['event']->ID);

// var_dump($transactional_data);
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<title>Pay Transaction</title>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class( array("pay-transaction-template") ); ?>>

		<header>
			<div class="top-links">
				<div class="top-logo">
					<?php echo get_custom_logo(); ?>
				</div>
			</div>
		</header>
		<div class="page-content-wrapper">

			<div class="pay-transaction">
				<?php if (1 == 5) : //if( !isEventAvailable( $transactional_data['event']->ID ) ) : ?>

					<div>Sorry. The Event is not available at this moment.</div>

				<?php elseif ($transactional_data) : ?>

					<div class="transaction-info">
						<h3>Payment</h3>
						<hr>

						<div class="event">
							<span class="title">Event:</span>
							<span class="value"><?php echo $transactional_data['event']->post_title; ?></span>
						</div>
						<div class="ticket">
							<span class="title">Ticket:</span>
							<span class="value"><?php echo $ticket_list[ $transactional_data['ticket_id'] ]; ?></span>
						</div>
						<div class="reg-id">
							<span class="title">Booking ID:</span>
							<span class="value"><?php echo $transactional_data['registration']->post_title; ?></span>
						</div>
						<div class="name">
							<span class="title">Name:</span>
							<span class="value"><?php echo $transactional_data['first_name'].' '. $transactional_data['last_name']; ?></span>
						</div>
						<div class="ticket-price">
							<span class="title">Ticket Price:</span>
							<span class="value"><?php echo $transactional_data['ticket_price']; ?></span>
						</div>
						<div class="already-paid">
							<span class="title">Already Paid:</span>
							<span class="value"><?php echo $transactional_data['already_paid']; ?></span>
						</div>
						<div class="pay-amount">
							<span class="title">Amount to pay in this transaction:</span>
							<span class="value"><?php echo $transactional_data['transaction_amount']; ?></span>
						</div>
					</div>
					<?php if(  isTicketAvailable( $transactional_data['event']->ID, $transactional_data['ticket_id'] )  ): ?>
						<hr>
						<p>sorry not ticket available</p>

					<?php elseif ($transactional_data['transaction_status'] != 'complete' ): ?>
					<div class="payment-method">
						<div class="margin-top checkout-section payment-options">
							<form id="se_payment-options" action="<?php echo SE_URL; ?>/paypal/payments.php" method="post">
								<div class="se_row">
									<div class="se-wid50">
										<h2>PAYMENT OPTIONS</h2>
										<div class="margin-top margin-bottom">
											<select name="payment-method" id="se-payment-method" class="custom_select_box" required="">
												<option value="0" disabled>Select a Payment Option</option>

												<?php if (get_option('se_payment_authorized_net')): ?>
												<option value="se_payment_method-cc" <?php selected($transactional_data['payment_method'], 'se_payment_method-cc'); ?>>Credit Card</option>
												<?php endif; ?>

												<?php if (get_option('se_payment_paypal')): ?>
												<option value="se_payment_method-paypal" <?php selected($transactional_data['payment_method'], 'se_payment_method-paypal'); ?>>Paypal</option>
												<?php endif; ?>
											</select>
											<ul class="form-list" id="se_payment_method-check" style="display: none;">
												<li>
													<p><em>If paying by personal check or money order, please make your checks payable to:</em></p>
												</li>
												<li>
													<?php echo se_get_payment_check_address(); ?>
												</li>
											</ul>
											<ul class="form-list" id="se_payment_method-paypal" style="display: none;">
												<li>
													If paying by paypal you will be redirected to Paypal to Complete your purchase.
													<img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;">
												</li>


												<input type="hidden" name="cmd" value="_xclick" />
												<input type="hidden" name="no_note" value="1" />
												<input type="hidden" name="lc" value="US" />
												<input type="hidden" name="currency_code" value="USD" />
												<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest" />
												

											</ul>
											<div class="form-list" id="se_payment_method-cc" style="display: none;">
												<p>You will enter your credit card details on the last step.</p>
												<div class="form-group owner">
													<label for="owner">Owner</label>
													<input type="text" class="form-control" id="owner" name="owner" required="">
												</div>
												<div class="form-group CVV">
													<label for="cvv">CVV</label>
													<input type="text" class="form-control" id="cvv" name="cvv" required="">
												</div>
												<div class="form-group" id="card-number-field">
													<label for="cardNumber">Card Number</label>
													<input type="text" class="form-control" name="cardNumber" id="cardNumber" required="">
												</div>
												<div class="form-group" id="expiration-date">
													<label>Expiration Date</label>
													<select id="exp_month" name="exp_month" required="">
														<option value="01">January</option>
														<option value="02">February </option>
														<option value="03">March</option>
														<option value="04">April</option>
														<option value="05">May</option>
														<option value="06">June</option>
														<option value="07">July</option>
														<option value="08">August</option>
														<option value="09">September</option>
														<option value="10">October</option>
														<option value="11">November</option>
														<option value="12">December</option>
													</select>
													<select id="exp_year" name="exp_year" required="">
														<?php for ($i = 0; $i <= 10; $i++) {?>				
															<option value="<?php echo ( intval(date('Y')) + $i ); ?>"> <?php echo ( intval(date('Y')) + $i ); ?></option>';
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="clearfix"></div>
											<div>
												<small>All Credit Card and PayPal transactions will have a <?php echo se_get_payment_processing_percent(); ?>% processing fee added to the total.<br>Note: 50% balance amounts will be shown for Pay By Check orders an the end of Registration process.</small>
											</div>
										</div>
									</div>
								</div>


								<input type="hidden" name="first_name" id="first_name" value="<?php echo $transactional_data['first_name']; ?>"  />
								<input type="hidden" name="last_name" id="last_name" value="<?php echo $transactional_data['last_name']; ?>"  />
								<input type="hidden" name="payer_email" id="payer_email" value="<?php echo $transactional_data['email']; ?>"  />
								<input type="hidden" name="item_name" id="item_name" value="<?php echo $transactional_data['event']->post_title .' - '. $transactional_data['ticket_name']; ?>" / >
								<input type="hidden" name="item_number" id="item_number" value="<?php echo $transactional_data['registration']->post_title; ?>" / >
								<input type="hidden" name="wp_tnx_id" id="wp_tnx_id" value="<?php echo $transactional_data['transaction']->ID; ?>" / >
							</form>

							<div class="se_row margin-top margin-bottom">
								<button class="btn btn-wide " id="se-payment-checkout">Checkout</button>
								<img class="se-spinner" src="<?php echo SE_ASSETS_URL; ?>/images/spinner30px.gif" style="display: none;">
							</div>
						</div>
					</div>
					<?php else: ?>
						<hr>
						<p>You have already paid for this transaction.</p>
					<?php endif; ?>

				<?php else: ?>

					<div>Sorry. We could not find the transaction you are looking for.</div>

				<?php endif ?>

			</div>

		</div><!-- #page -->
		<footer>
			<div class="copyright">
				<?php // echo SPF_COPYRIGHT; ?>
			</div> 
		</footer>

		<link rel="stylesheet" type="text/css" href="<?php echo SE_ASSETS_URL . '/css/transaction-pay.css'; ?>">
		<?php wp_footer();?>

		<script type="text/javascript" src="<?php echo SE_ASSETS_URL.'/js/jquery.validate.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo SE_ASSETS_URL.'/js/additional-methods.js'; ?>"></script>
		<script type='text/javascript'>
			var se_ajax_vars = {
				"ajax_url":"<?php echo admin_url( 'admin-ajax.php' ); ?>",
				"confirm_url":"<?php echo esc_url( home_url( '/' ) ).'se-confirm/?tnx='.$transactional_data['transaction']->ID; ?>"
			};
		</script>
		<script type="text/javascript" src="<?php echo SE_ASSETS_URL.'/js/pay-transaction.js'; ?>"></script>

	</body>
</html>


<?php 




