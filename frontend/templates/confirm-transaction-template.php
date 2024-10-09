<?php 

	$transaction_id = get_query_var( 'tnx' );
	$transactional_data = se_get_all_transactional_data($transaction_id);
	
if ($transactional_data) :
 ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<title>Payment Confirmation</title>
		<?php wp_head(); ?>
	</head>

	<body <?php body_class( array("confirmation-transaction-template") ); ?>>

		<header>
			<div class="top-links">
				<div class="top-logo">
					<?php echo get_custom_logo(); ?>
				</div>
			</div>
		</header>
		<div class="page-content-wrapper">


			<div class="confirmation-transaction">
				<div class="transaction-info">
					<h3>Event Ticket Booking Info</h3>
					<hr>

					<?php 

					$output[] = se_get_review_row('Name:','se_rv_name',$transactional_data['first_name'].' '. $transactional_data['last_name']);
					$output[] = se_get_review_row('Event:','se_rv_event',$transactional_data['event']->post_title);
					$output[] = se_get_review_row('Event Start:','se_rv_event_start', $transactional_data['event_start_date']);
					$output[] = se_get_review_row('Event End:','se_rv_event_end', $transactional_data['event_end_date']);
					$output[] = se_get_review_row('Ticket:','se_rv_ticket', $transactional_data['ticket_name']);
					$output[] = se_get_review_row('Ticket Price:','se_rv_ticket_price', $transactional_data['ticket_price']);
					$output[] = se_get_review_row('Payment Method:','se_rv_payment', $transactional_data['payment_method_title']);
					$output[] = se_get_review_row('Booking ID:','se_rv_registration_id', $transactional_data['registration']->post_title);
					$output[] = se_get_review_row('Booking Status:','se_rv_registration_status', $transactional_data['registration_status']);
					$output[] = se_get_review_row('Transaction ID:','se_rv_tnx_id', $transactional_data['transaction']->post_title);
					$output[] = se_get_review_row('Transaction Status:','se_rv_tnx_id', $transactional_data['transaction_status']);
					echo implode("\n", $output)
					 ?>

				</div>

			</div>


		</div><!-- #page -->
		<footer>
			<div class="copyright">
				<?php // echo SPF_COPYRIGHT; ?>
			</div> 
		</footer>

		<link rel="stylesheet" type="text/css" href="<?php echo SE_ASSETS_URL . '/css/transaction-confirmation.css'; ?>">
		<?php wp_footer();?>

	</body>
</html>




<?php else: ?>

<div>
	Sorry. We could not find the transaction you are looking for.
</div>

<?php endif ?>

<?php 


