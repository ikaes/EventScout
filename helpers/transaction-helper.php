<?php 

function se_get_all_pending_transaction_of_event($event_id="")
{
	global $transaction_pending_index;

	$selectedTransactionsArr = [];

	wp_reset_query();
	$transaction_query = array(
	    'post_type' => SE_TRANSACTION_POST_TYPE,
	    'posts_per_page'    => -1,
	    'post_status' => 'publish'
	);

	$transactions = new WP_Query( $transaction_query );

	if (is_array($transactions->posts)) {
	    foreach ($transactions->posts as $transaction) {
	        
	        $transaction_data = se_get_transaction_formated_meta($transaction->ID);

			if (
				$event_id == $transaction_data['transaction_event_id']
				&& $transaction_data['transaction_status_index'] == $transaction_pending_index
			) {				
				$selectedTransactionsArr[$transaction->ID] = $transaction;
			}
	    }
	}

	wp_reset_query();

	return $selectedTransactionsArr;

}

function se_get_transactions_of_registration($reg_id) {

	$selectedTransactionsArr = [];

	wp_reset_query();
	$transaction_query = array(
	    'post_type' => SE_TRANSACTION_POST_TYPE,
	    'posts_per_page'    => -1,
	    'post_status' => 'publish'
	);

	$transactions = new WP_Query( $transaction_query );

	if (is_array($transactions->posts)) {
	    foreach ($transactions->posts as $transaction) {
	        
	        $transaction_data = se_get_transaction_meta($transaction->ID);
	        $registration_id = $transaction_data[0];

	        if ($reg_id == $registration_id) {
	        	
				$selectedTransactionsArr[$transaction->ID]['title'] = $transaction->post_title;
				$selectedTransactionsArr[$transaction->ID]['reg_id'] = $transaction_data[0]; // event_registration_id
				$selectedTransactionsArr[$transaction->ID]['price'] = $transaction_data[2]; // transaction_amount_field
				$selectedTransactionsArr[$transaction->ID]['status'] = $transaction_data[3]; // transaction_status_field
	        }
	    }
	}

	wp_reset_query();

	return $selectedTransactionsArr;
}


function se_get_formated_related_transactions($reg_id) {
	global $seTransactionStatusArr;
	$selectedTransactions = se_get_transactions_of_registration($reg_id);
	?>
	<table>
		<thead>
		    <tr>
		      <th>Name</th>
		      <th>Amount</th>
		      <th>Status</th>
		    </tr>
		</thead>
		<tbody>
			<?php foreach ($selectedTransactions as $transactionId => $transactionInfo) { ?>
				<tr class="transaction-row" data-tnx-id="<?php echo $transactionId; ?>">
					<td><?php echo '<a class="row-title" href="'.admin_url( 'post.php' ).'?post='.$transactionId.'&amp;action=edit" target="_blank">'.$transactionInfo['title'].'</a>'; ?></td>
					<td><?php echo $transactionInfo['price']; ?></td>
					<td><?php echo $seTransactionStatusArr[ $transactionInfo['status'] ]; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>		
	<?php 
}


function se_get_already_paid_amount($registration_id)
{
	
	global $transaction_complete_index;

	$related_transactions = se_get_transactions_of_registration($registration_id);


    $reg_info_meta_data = get_post_meta( $registration_id, 'se_registration_info_meta_key', true );
    $total_price = floatval($reg_info_meta_data[4]);

	$already_paid = 0;

	if($total_price) {
		foreach ($related_transactions as $transactionId => $transactionInfo) {
            // var_dump($transactionInfo);
			if ( $transactionInfo['status'] == $transaction_complete_index ) {
				
				$already_paid += floatval($transactionInfo['price']);
			}
		}
	}
	return $already_paid;
}



function se_get_transaction_meta($transaction_id)
{
    $transaction_data = get_post_meta( $transaction_id, 'transaction_info_meta_key', true ); 
    return $transaction_data;
}


function se_get_transaction_formated_meta($transaction_id)
{
	$transaction_data = [];
	$transaction_meta = se_get_transaction_meta($transaction_id);

	$transaction_data['registration_id'] = $transaction_meta[0];
	$transaction_data['payment_method'] = $transaction_meta[1];
	$transaction_data['payment_method_title'] =se_get_payment_method_title($transaction_data['payment_method']);
	$transaction_data['transaction_amount'] = $transaction_meta[2];
	$transaction_data['transaction_status_index'] = $transaction_meta[3];
	$transaction_data['transaction_status'] = se_get_transaction_status_by_index($transaction_data['transaction_status_index']);

	$transaction_data['transaction_event_id'] = $transaction_meta[4];
	$transaction_data['transaction_ticket_id'] = $transaction_meta[5];
	$transaction_data['transaction_getway_id'] = $transaction_meta[6];
 
    return $transaction_data;
}

function se_get_transaction_status_index($transaction_id)
{
    $transaction_data = se_get_transaction_meta($transaction_id); 
    return $transaction_data[3];
}

function se_get_transaction_status($transaction_id)
{
    global $seTransactionStatusArr;
    $index = se_get_transaction_status_index($transaction_id);

    return $seTransactionStatusArr[$index];
}

function se_get_transaction_status_by_index($index)
{
    global $seTransactionStatusArr;
    return $seTransactionStatusArr[$index];
}

function se_get_all_transactional_data($transaction_id)
{
	$transactional_data = [];
	$transaction = get_post($transaction_id);

	if ($transaction != null) {

		$registration = se_get_registration_of_transaction($transaction_id);
		$event = se_get_event_of_transaction($transaction_id);

		$transaction_data = se_get_transaction_formated_meta($transaction_id);
		$registration_data =  se_get_reg_formated_metadata($registration->ID);
		$event_data =  se_get_event_metadata($event->ID);

		$transactional_data = array_merge($transactional_data, $registration_data);
		$transactional_data = array_merge($transactional_data, $transaction_data);
		
		$already_paid = se_get_already_paid_amount($registration->ID);
		$transactional_data['already_paid'] = $already_paid;
		$transactional_data = array_merge($transactional_data, $event_data);

		$transactional_data['event'] = $event;
		$transactional_data['registration'] = $registration;
		$transactional_data['transaction'] = $transaction;

		return $transactional_data;
	}
	return false;
}