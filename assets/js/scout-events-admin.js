
/**
 * Scout Event Transaction Scripts
 *
 */
(function($) {

	var event_selector = $("#se_transaction_event_field"), // select box
		ticket_selector = $('#se_transaction_ticket_field'), // select box
		btn_transaction_payment = $('#btn-transaction-payment'),
		mail_result = $('.transaction-mail .mail-result'),
		loading_spinner = $('.transaction-mail .se-spinner');

	$(document).ready(function() {

		event_selector.change(function(){
			var selected_event = $(this).find('option:selected', this);
			var selected_event_id = selected_event.attr('value');
			var selected_event_text = selected_event.text();
			var current = $(this);

		    $.ajax({
		        type: 'GET',
		        url: se_ajax_vars.ajax_url, // the localized name of your file
		        data: {
		            action: 'se_get_tickets',
		            event_id: selected_event_id
		        },
		        beforeSend: function() {
		            ticket_selector.removeClass('loaded').addClass('loading').html("<option value=\"\">Please wait...</option>");
		        },
		        success: function(result) {
		        	$(".se_event_name").text(selected_event_text);
		            ticket_selector.removeClass('loading').addClass('loaded').html(result);
		        }

		    });

		});

		btn_transaction_payment.on('click', function(e) {
			e.preventDefault();
		    $.ajax({
		        type: 'GET',
		        url: se_ajax_vars.ajax_url, // the localized name of your file
		        data: {
		            action: 'se_send_transaction_mail_ajax_func',
		            data: $('#post_ID').val()
		        },
		        beforeSend: function() {
		            loading_spinner.css({'display': 'block'});
		            btn_transaction_payment.css({'display': 'none'});
		            mail_result.css({'display': 'none'});
		        },
		        success: function(result) {
		            loading_spinner.css({'display': 'none'});
		            mail_result.css({'display': 'block'}).html("<p>"+result+"</p>")
		        }

		    });
		});

	}); // end of ready function 

}(jQuery));
