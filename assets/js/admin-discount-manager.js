/**
 * Scout Event Discount Scripts
 *
 */
(function($) {

	
	var startDateTextBox = $('#se-datetime-from');
	var endDateTextBox = $('#se-datetime-to');

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


	var discount_type_selectbox = $('#discount_type_field'),
		percentage_container = $(".discount-percentage"),
		amount_container = $(".discount-amount");

	discount_type_selectbox.on('change', function () {
		
		var discount_type = $(this).find('option:selected').val();
		
		if (discount_type == '%') {
			percentage_container.show();
			amount_container.hide();
		} else {
			percentage_container.hide();
			amount_container.show();
		}


	});

	$(document).ready(function() {
		discount_type_selectbox.trigger('change');
	}); // end of ready function 

}(jQuery));

