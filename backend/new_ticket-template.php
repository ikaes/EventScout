<?php 
function backend_event_ticket_html( $post ) {

    wp_nonce_field( 'scout_event_save_post_meta', 'scout_event_meta_box_nonce' );

    wp_enqueue_style( 'jquery-ui-custom' );
    wp_enqueue_style( 'jquery-ui-timepicker-addon' );

    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('jquery-ui-timepicker-addon'); 

    wp_enqueue_script('scout-events-manager-admin-scripts'); 

    $event_id = $post->ID;
    
    $se_start_date = get_post_meta( $event_id, 'se_start_date', true );
    $se_end_date = get_post_meta( $event_id, 'se_end_date', true );
    $se_reg_limit = get_post_meta( $event_id, 'se_reg_limit', true );
    $se_ticket_id = get_post_meta( $event_id, 'se_ticket_id', true );
    $se_total_tickets = get_post_meta( $event_id, 'se_total_tickets', true );
    $total_ticket_sold = se_count_sold_ticket($event_id);

    $tickets = get_event_tickets($event_id);
    
	if (!is_array($tickets) )
		$tickets = array(array(
			'id' => '1',
			'name' => '',
			'start_date' => '',
			'end_date' => '',
			'prices' => '0',
			'qty' => '',
		));

?>

<div id="event-and-ticket-form-content">
	<h3 class="event-tickets-datetimes-title">Event Datetimes</h3>
	<div id="se-datetime" class="event-datetime-row">
		<table id="se-datetime-table" class="datetime-edit-table">
			<tr>
				<td class="se-datetime-column date-column">
					<label for="se-datetime-start">Event Start</label>
					<input type="text" name="datetime_start_date" value="<?php echo $se_start_date ? $se_start_date : ''; ?>" id="se-datetime-start" class="ee-datepicker" data-related-field="#se-datetime-end">
				</td>
				<td class="se-datetime-column date-column">
					<label for="se-datetime-end_date">Event End</label>
					<input type="text" name="datetime_end_date" value="<?php echo $se_end_date ? $se_end_date : ''; ?>" id="se-datetime-end_date" class="ee-datepicker" data-related-field="#se-datetime-start">
				</td>
				<td class="se-datetime-column reg-limit-column">
					<label for="se-datetime_reg_limit">Reg Limit</label>
					<input type="number" name="datetime_reg_limit" value="<?php echo $se_reg_limit ? $se_reg_limit : ''; ?>" id="se-datetime_reg_limit">
				</td>
				<td class="se-datetime-column ticket-sold-column">
					<label for="se-ticket-sold">Ticket Sold</label>
					<input type="number" name="ticket-sold" value="<?php echo $total_ticket_sold; ?>" id="se-ticket-sold" readonly>
				</td>
			</tr>
		</table>
	</div>
	<div style="clear:both"></div>
	<div class="event-tickets-container">
		<h4 class="event-tickets-datetimes-title">Ticket Options</h4><br>
		<table class="add-new-ticket-table">
			<thead>
				<tr valign="top">
					<td>Ticket Name</td>
					<td>Sale Start</td>
					<td>Sell Until</td>
					<td>Accommodation Start</td>
					<td>Accommodation End</td>
					<td><span class="hidden">currency symbol</span></td>
					<td>Price</td>
					<td></td>
				</tr>
			</thead>
			<tbody id="all-ticket-rows">

				<?php  foreach ($tickets as $key => $ticket) { ?> 
				<tr valign="top" id="edit_tickets<?php echo $ticket['id'] ?>_row" class="edit-ticket-row">
					<input type="hidden" name="edit_tickets[<?php echo $ticket['id'] ?>][id]" value="<?php echo $ticket['id'] ?>">
					<td><input type="text" maxlength="145" name="edit_tickets[<?php echo $ticket['id'] ?>][name]" class="" placeholder="Ticket Title" value="<?php echo $ticket['name'] ?>"></td>
					<td><input type="text" name="edit_tickets[<?php echo $ticket['id'] ?>][start_date]" class="edit_tickets_start_date" value="<?php echo $ticket['start_date'] ?>"></td>
					<td><input type="text" name="edit_tickets[<?php echo $ticket['id'] ?>][end_date]" class="edit_tickets_end_date" value="<?php echo $ticket['end_date'] ?>"></td>
					<td><input type="text" name="edit_tickets[<?php echo $ticket['id'] ?>][acom_start_date]" class="edit_tickets_acom_start_date" value="<?php echo $ticket['acom_start_date'] ?>"></td>
					<td><input type="text" name="edit_tickets[<?php echo $ticket['id'] ?>][acom_end_date]" class="edit_tickets_acom_end_date" value="<?php echo $ticket['acom_end_date'] ?>"></td>
					<td><span class="ticket-price-info-display">$</span></td>
					<td><input type="number" style="width:100px" class="" name="edit_tickets[<?php echo $ticket['id'] ?>][prices]" value="<?php echo $ticket['prices'] ?>"></td>
					<td><span class="trash-icon dashicons dashicons-post-trash se_ticket_trash_btn" data-ticket-row='<?php echo $ticket['id'] ?>'></span></td>
				</tr>
				<?php } ?>
				
			</tbody>
		</table> <!-- end .add-new-ticket-table -->

		<input type="hidden" name="ticket_IDs" id="ticket-IDs" value="<?php echo $se_ticket_id ? $se_ticket_id : '1'; ?>">
		<input type="hidden" name="ticket_total_rows" id="ticket-total-rows" value="<?php echo $se_total_tickets ? $se_total_tickets : '1'; ?>">

		<div class="save-cancel-button-container">
			
			<button class="button-secondary se-create-ticket-btn" data-context="ticket">Create New Ticket</button>

			<script type="text/plain" id="new-ticket-row">
				<tr valign="top" id="new_edit_tickets_row" class="edit-ticket-row">
					<input type="hidden" name="new_edit_ticket_ROW[id]" value="CURRENT_TICKET_ID">
					<td><input type="text" maxlength="245" name="new_edit_ticket_ROW[name]" class="" placeholder="Ticket Title" value=""></td>
					<td><input type="text" name="new_edit_ticket_ROW[start_date]" class="edit_tickets_start_date" value=""></td>
					<td><input type="text" name="new_edit_ticket_ROW[end_date]" class="edit_tickets_end_date" value=""></td>
					<td><input type="text" name="new_edit_ticket_ROW[acom_start_date]" class="edit_tickets_acom_start_date" value=""></td>
					<td><input type="text" name="new_edit_ticket_ROW[acom_end_date]" class="edit_tickets_acom_end_date" value=""></td>
					<td><span class="ticket-price-info-display">$</span></td>
					<td><input type="number" style="width:100px" class="" name="new_edit_ticket_ROW[prices]" value="0"></td>
					<td><span class="trash-icon dashicons dashicons-post-trash se_ticket_trash_btn" data-ticket-row="CURRENT_TICKET_ID" ></span></td>
				</tr>
			</script>

		</div>
	</div>
</div>

	<?php
}