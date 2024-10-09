(function($) {

	var row = 1,
		ticketId = 1,
		ticketIds = [1];

	// Refresh the environment for managing tickets.
	function set_default_ticket_setting() {
		var default_ids = $("#ticket-IDs").val();
		var default_total_rows = $("#ticket-total-rows").val();

		if(default_ids != "") {
			ticketIds = default_ids.split(",").map(Number);
			ticketId = Math.max.apply(null, ticketIds); // get maximum value to set current ticket id
		}

		if(default_total_rows != "") {
			row = default_total_rows;
		}
	}

	$(".se-create-ticket-btn").on('click', function(event){

		event.preventDefault();

		set_default_ticket_setting();
		
		ticketId++;
		row++;


		var cloned_data = $("#new-ticket-row").clone().html();

		var regex = new RegExp( "new_edit_tickets_row" , "g");
		var regex2 = new RegExp( 'new_edit_ticket_ROW' , "g");
		var regex3 = new RegExp( 'CURRENT_TICKET_ID' , "g");

		
		var new_ticket_html = cloned_data.replace(regex, "edit_tickets" + ticketId + "_row" ),
			new_ticket_html = new_ticket_html.replace(regex2, "edit_tickets[" + ticketId + "]"),
			new_ticket_html = new_ticket_html.replace(regex3, ticketId);

		ticketIds.push(ticketId);

		$("#ticket-IDs").val(ticketIds);
		$("#ticket-total-rows").val(row);

		$("#all-ticket-rows").append(new_ticket_html);

	});


	// deleting tickets on click trash button
	$("#all-ticket-rows").on('click', ".se_ticket_trash_btn", function(event){

		event.preventDefault();

		var row_selector = $(this).parent("td").parent("tr");
		if (row_selector.siblings().length > 0) {

			var trash_btn_data = $(this).data();

			// removing ticket id form ticketIds
			if( ticketIds.length > 1 ) {
				var index = ticketIds.indexOf(  trash_btn_data.ticketRow  );
				if (index > -1) {
				  ticketIds.splice(index, 1);
				}
			}

			// set current existing ticketIds in input field
			$("#ticket-IDs").val(ticketIds);

			// removing the ticket row
			// $("#edit_tickets"+trash_btn_data.ticketRow+"_row").remove();
			row_selector.remove();

			// set the current number of rows in input box
			$("#ticket-total-rows").val(--row);	
		}
	});


	$(document).ready(function() {

		// initialize default environment for managing tickets.
		set_default_ticket_setting();

	}); // end of ready function 



	var startDateTextBox = $('#se-datetime-start');
	var endDateTextBox = $('#se-datetime-end_date');

	startDateTextBox.datetimepicker({ 
		numberOfMonths: 2,
		timeFormat: 'h:mm tt',
		dateFormat : 'yy-mm-dd',
		stepHour: 1,
		stepMinute: 5,
		onClose: function(dateText, inst) {
			if (endDateTextBox.val() != '') {
				var testStartDate = startDateTextBox.datetimepicker('getDate');
				var testEndDate = endDateTextBox.datetimepicker('getDate');
				if (testStartDate > testEndDate)
					endDateTextBox.datetimepicker('setDate', testStartDate);
			}
			else {
				endDateTextBox.val(dateText);
			}
		},
		onSelect: function (selectedDateTime){
			endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
		}
	});
	endDateTextBox.datetimepicker({ 
		numberOfMonths: 2,
		timeFormat: 'h:mm tt',
		dateFormat : 'yy-mm-dd',
		stepHour: 1,
		stepMinute: 5,
		onClose: function(dateText, inst) {
			if (startDateTextBox.val() != '') {
				var testStartDate = startDateTextBox.datetimepicker('getDate');
				var testEndDate = endDateTextBox.datetimepicker('getDate');
				if (testStartDate > testEndDate)
					startDateTextBox.datetimepicker('setDate', testEndDate);
			}
			else {
				startDateTextBox.val(dateText);
			}
		},
		onSelect: function (selectedDateTime){
			startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
		}
	});	

}(jQuery));






(function($) {

	function set_date_time_input(start_date_input_class,end_date_input_class) {
		$('.edit-ticket-row').each(function (index, value){
			var startDateTextBox = $(this).find(start_date_input_class);
			var endDateTextBox = $(this).find(end_date_input_class);

			startDateTextBox.datetimepicker({ 
				numberOfMonths: 2,
				timeFormat: 'h:mm tt',
				dateFormat : 'yy-mm-dd',
				stepHour: 1,
				stepMinute: 5,
				maxDate: endDateTextBox.datetimepicker('getDate'),
				onClose: function(dateText, inst) {
					if (endDateTextBox.val() != '') {
						var testStartDate = startDateTextBox.datetimepicker('getDate');
						var testEndDate = endDateTextBox.datetimepicker('getDate');
						if (testStartDate > testEndDate)
							endDateTextBox.datetimepicker('setDate', testStartDate);
					}
					else {
						endDateTextBox.val(dateText);
					}
				},
				onSelect: function (selectedDateTime){
					// console.log(`startDateTextBox selected:`);
					endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
				}
			});
			endDateTextBox.datetimepicker({ 
				numberOfMonths: 2,
				timeFormat: 'h:mm tt',
				dateFormat : 'yy-mm-dd',
				stepHour: 1,
				stepMinute: 5,
				minDate: startDateTextBox.datetimepicker('getDate'),
				onClose: function(dateText, inst) {
					if (startDateTextBox.val() != '') {
						var testStartDate = startDateTextBox.datetimepicker('getDate');
						var testEndDate = endDateTextBox.datetimepicker('getDate');
						if (testStartDate > testEndDate)
							startDateTextBox.datetimepicker('setDate', testEndDate);
					}
					else {
						startDateTextBox.val(dateText);
					}
				},
				onSelect: function (selectedDateTime){
					// console.log(`endDateTextBox selected:`);
					startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
				}
			});	
		});
		
	}
	
	$(document).ready(function() {

		set_date_time_input('.edit_tickets_start_date','.edit_tickets_end_date');
		set_date_time_input('.edit_tickets_acom_start_date','.edit_tickets_acom_end_date');

		$('.se-create-ticket-btn').on('click', function () {
			set_date_time_input('.edit_tickets_start_date','.edit_tickets_end_date');
			set_date_time_input('.edit_tickets_acom_start_date','.edit_tickets_acom_end_date');
		});

	}); // end of ready function 

}(jQuery));


