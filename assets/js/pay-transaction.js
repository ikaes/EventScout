(function($) {

	var payment_method_selector = $('#se-payment-method');
	var checkout_button = $('#se-payment-checkout');
	var payment_form = $('#se_payment-options');

	function getParameterByName(name, url) {
	    if (!url) url = window.location.href;
	    name = name.replace(/[\[\]]/g, '\\$&');
	    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
	        results = regex.exec(url);
	    if (!results) return null;
	    if (!results[2]) return '';
	    return decodeURIComponent(results[2].replace(/\+/g, ' '));
	}

	$(document).ready(function() {
		
		payment_method_selector.change(function(){

			var method = $(this).find("option:selected").val();


			// Return today's date and time
			var currentTime = new Date()

			// returns the year (four digits)
			var currentYear = currentTime.getFullYear()

			$(".payment-options .form-list").hide();

			console.log(`method`,method);
			
			if(method == 'se_payment_method-cc') {

				$(".se_payment_name").text("Credit Card");
				$(".payment-options #se_payment_method-cc").show();
				// $("#se_payment-options").valid();
				console.log('false way');
				payment_form.validate({
					rules: {
						owner: {
							required: true,
						},
						cardNumber: {
							required: true,
							creditcard: true
						},
						exp_month: {
						    required: true,
						    range: [1, 12],
						},
						exp_year: {
						    required: true,
						    range: [currentYear, currentYear+20],
						},
						cvv: {
						    required: true,
						    minlength: 3,
						    maxlength: 4,
						    digits: true
						},
					},
					messages: {
						owner: {
							required: "Please enter owner Name"
						}
					}
				});
			
			} else if(method == 'se_payment_method-paypal') {
				
				$(".se_payment_name").text("Paypal");
				$(".payment-options #se_payment_method-paypal").show();
				
			} else if(method == 'se_payment_method-check') {

				$(".se_payment_name").text("Check");
				$(".payment-options #se_payment_method-check").show();

			}

		});

		payment_method_selector.trigger('change');

		checkout_button.on('click', function(e) {
		    
		    var method = payment_method_selector.find("option:selected").val();
		    console.log(`method`,method);

		    if(method == 'se_payment_method-cc') {

		    	if ( payment_form.valid() ) {
					$.ajax({
						type: 'GET',
						url: se_ajax_vars.ajax_url, // the localized name of your file
						data: {
							action: 'se_pay_transaction_by_credit_card_ajax_func',
							form_data: payment_form.serializeArray()
						},
						beforeSend: function() {
							checkout_button.hide();
							checkout_button.find("+ .se-spinner").show();
						},
						success: function(result) {

							data = JSON.parse(result);
							if(data.error != null )
								alert(data.error);
							else {
								
								var transaction_id = getParameterByName('tnx');
								
								$.ajax({
									type: 'GET',
									url: se_ajax_vars.ajax_url, // the localized name of your file
									data: {
										action: 'se_send_transaction_successfull_mail_ajax_func',
										transaction_id: transaction_id
									},
									success: function(msg) {
										checkout_button.find("+ .se-spinner").hide();
										// checkout_button.show();
										window.location.href = se_ajax_vars.confirm_url;
									}

								});
							}
						}

					});
		    	} else {
		    		alert("Fill Up with valid information");
		    	}
		    
		    } else if(method == 'se_payment_method-paypal') {
		    	
		    	payment_form[0].submit();
		    	
		    } else if(method == 'se_payment_method-check') {


		    }
		});


	}); // document ready


}(jQuery));
