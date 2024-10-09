
/**************************************
 * Date class extension
 * 
 */
    // Provide month names
    Date.prototype.getMonthName = function(){
        var month_names = [
                            'January',
                            'February',
                            'March',
                            'April',
                            'May',
                            'June',
                            'July',
                            'August',
                            'September',
                            'October',
                            'November',
                            'December'
                        ];

        return month_names[this.getMonth()];
    }

    // Provide month abbreviation
    Date.prototype.getMonthAbbr = function(){
        var month_abbrs = [
                            'Jan',
                            'Feb',
                            'Mar',
                            'Apr',
                            'May',
                            'Jun',
                            'Jul',
                            'Aug',
                            'Sep',
                            'Oct',
                            'Nov',
                            'Dec'
                        ];

        return month_abbrs[this.getMonth()];
    }

    // Provide full day of week name
    Date.prototype.getDayFull = function(){
        var days_full = [
                            'Sunday',
                            'Monday',
                            'Tuesday',
                            'Wednesday',
                            'Thursday',
                            'Friday',
                            'Saturday'
                        ];
        return days_full[this.getDay()];
    };

    // Provide full day of week name
    Date.prototype.getDayAbbr = function(){
        var days_abbr = [
                            'Sun',
                            'Mon',
                            'Tue',
                            'Wed',
                            'Thur',
                            'Fri',
                            'Sat'
                        ];
        return days_abbr[this.getDay()];
    };

    // Provide the day of year 1-365
    Date.prototype.getDayOfYear = function() {
        var onejan = new Date(this.getFullYear(),0,1);
        return Math.ceil((this - onejan) / 86400000);
    };

    // Provide the day suffix (st,nd,rd,th)
    Date.prototype.getDaySuffix = function() {
        var d = this.getDate();
        var sfx = ["th","st","nd","rd"];
        var val = d%100;

        return (sfx[(val-20)%10] || sfx[val] || sfx[0]);
    };

    // Provide Week of Year
    Date.prototype.getWeekOfYear = function() {
        var onejan = new Date(this.getFullYear(),0,1);
        return Math.ceil((((this - onejan) / 86400000) + onejan.getDay()+1)/7);
    } 

    // Provide if it is a leap year or not
    Date.prototype.isLeapYear = function(){
        var yr = this.getFullYear();

        if ((parseInt(yr)%4) == 0){
            if (parseInt(yr)%100 == 0){
                if (parseInt(yr)%400 != 0){
                    return false;
                }
                if (parseInt(yr)%400 == 0){
                    return true;
                }
            }
            if (parseInt(yr)%100 != 0){
                return true;
            }
        }
        if ((parseInt(yr)%4) != 0){
            return false;
        } 
    };

    // Provide Number of Days in a given month
    Date.prototype.getMonthDayCount = function() {
        var month_day_counts = [
                                    31,
                                    this.isLeapYear() ? 29 : 28,
                                    31,
                                    30,
                                    31,
                                    30,
                                    31,
                                    31,
                                    30,
                                    31,
                                    30,
                                    31
                                ];

        return month_day_counts[this.getMonth()];
    } 

    // format provided date into this.format format
    Date.prototype.format = function(dateFormat){
        // break apart format string into array of characters
        dateFormat = dateFormat.split("");

        var date = this.getDate(),
            month = this.getMonth(),
            hours = this.getHours(),
            minutes = this.getMinutes(),
            seconds = this.getSeconds();
        // get all date properties ( based on PHP date object functionality )
        var date_props = {
            d: date < 10 ? '0'+date : date,
            D: this.getDayAbbr(),
            j: this.getDate(),
            l: this.getDayFull(),
            S: this.getDaySuffix(),
            w: this.getDay(),
            z: this.getDayOfYear(),
            W: this.getWeekOfYear(),
            F: this.getMonthName(),
            m: month < 10 ? '0'+(month+1) : month+1,
            M: this.getMonthAbbr(),
            n: month+1,
            t: this.getMonthDayCount(),
            L: this.isLeapYear() ? '1' : '0',
            Y: this.getFullYear(),
            y: this.getFullYear()+''.substring(2,4),
            a: hours > 12 ? 'pm' : 'am',
            A: hours > 12 ? 'PM' : 'AM',
            g: hours % 12 > 0 ? hours % 12 : 12,
            G: hours > 0 ? hours : "12",
            h: hours % 12 > 0 ? hours % 12 : 12,
            H: hours,
            i: minutes < 10 ? '0' + minutes : minutes,
            s: seconds < 10 ? '0' + seconds : seconds           
        };

        // loop through format array of characters and add matching data else add the format character (:,/, etc.)
        var date_string = "";
        for(var i=0;i<dateFormat.length;i++){
            var f = dateFormat[i];
            if(f.match(/[a-zA-Z]/g)){
                date_string += date_props[f] ? date_props[f] : '';
            } else {
                date_string += f;
            }
        }

        return date_string;
    };
/*
 *
 * END - Date class extension
 * 
 ************************************/

var SEEventCheckoutModule = (function($) {

	window.form_data = {};
	
	var 
		deposit_select = $('#se_event_deposit'), // select box
		deposit_container = $('.se_deposit_box'), // div deposit info container

		eventToggler = $(".event-toggler"),
		
		priceTicket = $(".ticket-price"),
		priceDiscount = $(".discount-price"),
		priceProcessing = $(".processing-price"),
		priceSubtotal = $(".subtotal-price"),

		TicketAmount = priceTicket.find(".pricing-amount"),
		DiscountAmount = priceDiscount.find(".pricing-amount"),
		ProcessingAmount = priceProcessing.find(".pricing-amount"),
		SubtotalAmount = priceSubtotal.find(".pricing-amount"),
		
		discountCost = 0,
		ticketCost = 0,
		processingCost = 0,
		totalCost = 0,
		porcessingPercentage = se_ajax_vars.porcessingPercentage,

		workshop_form = $("#se_workshop"),

		payment_method_selector = $('#se-payment-method'), // select box
		paymentMethod = payment_method_selector.find("option:selected").val(),

		promo_button = $(".se_promo_applier"),
		promotion_input = $(".promotion-input"),
		isDiscoutApplied = false,

		selectEvent = $("#raw-event-select"), // select box
		selectTicket = $('#raw-ticket-select'), // select box

		totalPricebox = $("#se_ticket_price"), // hidden input box
		accomodationToggler = $(".ticket-toggler"),
		togglerSection = $(".toggler-section"),
		a= 1;

	function getProcesCost(totalPrice) {
		var totalPrice = roundToTwo(totalPrice);
		if (porcessingPercentage > 0) {
			porcessingPercentage = roundToTwo(porcessingPercentage);
			var cost = (totalPrice * porcessingPercentage) / 100;
		} else {
			var cost = '0';
		}

		return roundToTwo(cost);
	}

	function updateAndShowProcessingCost() {
		processingCost = getProcesCost( totalCost );
		priceProcessing.find(".pricing-amount").text( processingCost );
		priceProcessing.show();

		processingCost = roundToTwo(processingCost);
		ticketCost = roundToTwo(ticketCost);
		discountCost = roundToTwo(discountCost);

		var FinalTotalCost = roundToTwo( (processingCost + ticketCost) - discountCost );

		updateAndShowTotalPrice(FinalTotalCost);
	}
	function removeAndHideProcessingCost() {
		processingCost = 0;
		priceProcessing.find(".pricing-amount").text( processingCost );
		priceProcessing.hide();

		ticketCost = roundToTwo(ticketCost);
		discountCost = roundToTwo(discountCost);

		var FinalTotalCost = roundToTwo( ticketCost - discountCost );

		updateAndShowTotalPrice(FinalTotalCost);
	}

	function updateAndShowDiscount(disctCost) {
		discountCost = disctCost;
		DiscountAmount.text(discountCost);
		priceDiscount.show();

		removeAndHideProcessingCost();
		payment_method_selector.val("0").trigger('change');
	}

	function resetAndHideDiscoutPrice() {
		discountCost = 0;
		DiscountAmount.text(discountCost);
		priceDiscount.hide();
	}

	function updateAndShowTicketPrice(price) {
		ticketCost = price;
		TicketAmount.text(ticketCost);
		priceTicket.show();
	}
	function resetAndHideTicketPrice() {
		ticketCost = 0;
		TicketAmount.text('0');
		priceTicket.hide();
	}
		
	function setTotalPrice(price) {
		totalCost = price;
		if( window.form_data.worksheet_info != undefined)
			window.form_data.worksheet_info[2].value = totalCost;
		refresh_deposit_data();
	}

	function updateAndShowTotalPrice(price) {
		setTotalPrice(price);
		SubtotalAmount.text( price );
		priceSubtotal.show();
	}

	function resetAndHideTotalPrice() {
		setTotalPrice(0);
		SubtotalAmount.text( '0' );
		priceSubtotal.hide();
	}


	function set_ticket_price(price) {
		ticketCost = price;
		TicketAmount.text( price );
	}

	function getTotalPrice() { return roundToTwo( totalCost ); }

	function showAndUpdateEventToggler(title, date_start, date_end) {
		// updating toggler texts
		eventToggler.find(".title").text(title);

		var short_start_date = date_start.split(" ")[0];
		var short_end_date = date_end.split(" ")[0];

		var startDate = parseDate(short_start_date); 
		var endDate = parseDate(short_end_date); 
		
		var SDate = startDate.format("F dS, Y"); // June 10th, 2019
		var EDate = endDate.format("F dS, Y"); // June 10th, 2019

		eventToggler.find(".toggle-title .date").text(SDate + ' - ' + EDate);
		eventToggler.find(".toggle-content .date").text(date_start + ' - ' + date_end);
		eventToggler.slideDown();
		
		togglerSection.find(">h3").slideDown();			
	}

	function showAndUpdateAccommodationToggler(title, date_start, date_end, TicketPrice) {
		// updating toggler texts
		accomodationToggler.find(".title").text(title);

		var short_start_date = date_start.split(" ")[0];
		var short_end_date = date_end.split(" ")[0];

		if(short_start_date != "" || short_end_date) {

			var startDate = parseDate(short_start_date); 
			var endDate = parseDate(short_end_date); 



			var SDate = startDate.format("F dS, Y"); // June 10th, 2019
			var EDate = endDate.format("F dS, Y"); // June 10th, 2019

			accomodationToggler.find(".toggle-title .date").text(SDate + ' - ' + EDate);
			accomodationToggler.find(".toggle-content .date").text(date_start + ' - ' + date_end);
			accomodationToggler.slideDown();
			
			togglerSection.find(".deposit").show().find('.amount').text(TicketPrice);
			togglerSection.find(">h3").slideDown();	
		} else {
			accomodationToggler.slideUp();
			togglerSection.find(".deposit").hide();
		}

	}


	function render_deposit_price_box() {
		refresh_deposit_data();
		deposit_container.show(); 
	}

	function hide_deposit_price_box() { deposit_container.hide() }

	function refresh_deposit_data() {

		var pay_now_placeholder = $(".pay_now .pricing-amount"),
			pay_later_placeholder = $(".pay_later .pricing-amount");

		deposit_price =  roundToTwo( getTotalPrice() / 2 );

		pay_now_placeholder.text( deposit_price );
		pay_later_placeholder.text( deposit_price );
	}

	function roundToTwo(num) {    
	    return +(Math.round(num + "e+2")  + "e-2");
	}
	// return integer value of date
	function parseDateInt(date) {

		var parsed = parseCalenderDate(date);
		parsed.setHours("0");		
		return parsed;
	}

	// return date object or wrong input date
	function parseCalenderDate(date) {

		var parsed = Date.parse(date);
		if (!isNaN(parsed)) {
			var dateObj = new Date(parsed);
			return dateObj;
		}

		var changed_date = date.replace(/-/g, '/').replace(/[a-z]+/gi, ' ');
		parsed = Date.parse(changed_date);
		
		if (!isNaN(parsed)) {
			var dateObj = new Date(parsed);
			return dateObj;
		}
		console.log('wront input date: ' + date);

		return date;
	}
	// return date object or wrong input date
	function parseDate(date) {

		var parsed = Date.parse(date);
		if (!isNaN(parsed)) {
			var dateObj = new Date(parsed);

			dateObj.setMinutes(dateObj.getMinutes() + dateObj.getTimezoneOffset());
			// console.log(`dateObj1a`,dateObj);
			return dateObj;
		}

		var changed_date = date.replace(/-/g, '/').replace(/[a-z]+/gi, ' ');
		parsed = Date.parse(changed_date);
		
		if (!isNaN(parsed)) {
			var dateObj = new Date(parsed);
			dateObj.setMinutes(dateObj.getMinutes() + dateObj.getTimezoneOffset());
			return dateObj;
		}
		console.log('wront input date: ' + date);

		return date;
	}

	$(document).ready(function() {

		/* ============================================
		* Default Calender scripts
		* ============================================
		*/

		window.date_shower = $('#date_shower');
		window.date_shower.datepicker({
			dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		});
		window.removeHighlightDatePicker = function() {

			window.date_shower.datepicker('option', 'minDate', null );
			window.date_shower.datepicker('option', 'maxDate', null );

			window.date_shower.datepicker('option', 'beforeShowDay',
				function( d ) {	
					return [true, ''];
			    }
			);
		}
		window.highlightDatePickerSingleRange = function(dateA, dateB) {


			window.date_shower.datepicker('option', 'minDate', parseCalenderDate(dateA) );
			window.date_shower.datepicker('option', 'maxDate', parseCalenderDate(dateB) );

			var a = parseDateInt(dateA);
			var b = parseDateInt(dateB);

			window.date_shower.datepicker('option', 'beforeShowDay',
				function( d ) {

					d = d.setHours("0");					
					var tdClass = "";
					if (a <= d && d <= b)
						tdClass = "workshop";
						
					return [true, tdClass];
			    }
			);
		}
		window.highlightDatePickerDoubleRange = function(dateA, dateB, dateX, dateY) {

			window.date_shower.datepicker('option', 'minDate', parseCalenderDate(dateA) );
			window.date_shower.datepicker('option', 'maxDate', parseCalenderDate(dateB) );

			// accomodation date range
			var a = parseDateInt(dateA);
			var b = parseDateInt(dateB);
			
			// workshop date range
			var x = parseDateInt(dateX);
			var y = parseDateInt(dateY);

			window.date_shower.datepicker('option', 'beforeShowDay',
				function( d ) {				

					d = d.setHours("0");
					var tdClass = "";

					if( (x <= d && d <= y) && (a <= d && d <= b) )
						tdClass = "accomodation workshop";
					else if (x <= d && d <= y)
						tdClass = "workshop";
					else if (a <= d && d <= b)
						tdClass = "accomodation";
						
					return [true, tdClass];
			    }
			);
		}

		var toggler = $(".toggler-section");
		toggler.on("click", ".toggle-title", function() {
		    $(this).toggleClass("active").next().slideToggle();
		 });

		toggler.find('.toggle-content').hide();
		toggler.find('.toggle-title.active').next().slideToggle();


		/* ============================================
		* Session Workshop Select
		* ============================================
		*/

		selectEvent.change(function(){
			var selected_event = $(this).find('option:selected', this);
			var selected_event_id = selected_event.attr('value');
			var selected_event_text = selected_event.text();
			var current = $(this);

			$.ajax({
				type: 'GET',
				url: se_ajax_vars.ajax_url, // the localized name of your file
				data: {
					action: 'se_get_tickets_and_calender_langth',
					event_id: selected_event_id
				},
				beforeSend: function() {
					removeAndHideProcessingCost();
					resetAndHideDiscoutPrice();
					resetAndHideTicketPrice();
					resetAndHideTotalPrice();
					set_ticket_price(0);
					accomodationToggler.slideUp();
					selectTicket.removeClass('loaded').addClass('loading').html("<option value=\"\">Please wait...</option>");
				},
				success: function(result) {
					var data = JSON.parse( result );

					// updating review summer
					$(".se_rv_event .se-review-value").text(selected_event_text);
					
					// updating tickets options
					selectTicket.removeClass('loading').addClass('loaded').html(data[0]);


					// checking if event is found
					if( data[1].event_start_date ) {

						// updating calender
						window.highlightDatePickerSingleRange(data[1].event_start_date, data[1].event_end_date);

						// updating toggler texts
						showAndUpdateEventToggler(selected_event_text, data[1].event_start_date, data[1].event_end_date);

					} else {
						togglerSection.find(">h3").slideUp();
						eventToggler.slideUp();

						// window.date_shower.datepicker( "refresh" );
						window.removeHighlightDatePicker();

					}



				}

			});

		});


		// ============================================
		// Event Ticket Select
		// ============================================

		selectTicket.change(function(){

			var selected_event_id = selectEvent.find('option:selected', this).attr('value'),
				selected_ticket = $(this).find('option:selected', this),
				selected_ticket_id = selected_ticket.attr('value'),
				selected_ticket_text = selected_ticket.text();

			var current = $(this);

			// if (selected_ticket_id && selected_ticket_id != 0) {
			$.ajax({
				type: 'GET',
				url: se_ajax_vars.ajax_url, // the localized name of your file
				data: {
					action: 'se_get_event_and_ticket_price',
					event_id: selected_event_id,
					ticket_id: selected_ticket_id
				},
				beforeSend: function() {
					workshop_form.find(".btn.btn-wide").prop("disabled",true);
				},
				success: function(result) {
					// enabling next button
					workshop_form.find(".btn.btn-wide").prop("disabled",false);
					
					var data = JSON.parse( result );

					var EventStartDate = data[1].event_start_date;
					var EventEndDate = data[1].event_end_date;

					var TicketStartDate = data[0].start_date;
					var TicketEndDate = data[0].end_date;
					var AccommodationStartDate = data[0].acom_start_date;
					var AccommodationEndDate = data[0].acom_end_date;
					var TicketPrice = data[0].prices;
					var TicketTitle = data[0].name;

					resetAndHideDiscoutPrice();
					removeAndHideProcessingCost();
					payment_method_selector.val("0").trigger('change');
					priceDiscount.hide();


					if( AccommodationStartDate != undefined && AccommodationStartDate != "" ) {
						// updating checkout calender
						window.highlightDatePickerDoubleRange(AccommodationStartDate, AccommodationEndDate, EventStartDate, EventEndDate);

					} else  {
						// updating calender
						window.highlightDatePickerSingleRange(EventStartDate,EventEndDate);
					}

					// if there any available ticket found
					if( TicketTitle != undefined) {

						// updating ticket price
						updateAndShowTicketPrice(TicketPrice);
						updateAndShowTotalPrice(TicketPrice);

						priceTicket.show();
						priceSubtotal.show();
						
						// updating review summery 
						$(".se_rv_ticket .se-review-value").text(selected_ticket_text);

						// updating toggler texts
						showAndUpdateAccommodationToggler(TicketTitle,AccommodationStartDate, AccommodationEndDate,TicketPrice );
						
					} else {
						set_ticket_price(0);
						accomodationToggler.slideUp();
						togglerSection.find(".deposit").hide();

						// updating ticket price
						resetAndHideTicketPrice();
						resetAndHideTotalPrice();
					}
				}

			});
		});

		
		payment_method_selector.change(function(){

			var method = $(this).find("option:selected").val();


			// Return today's date and time
			var currentTime = new Date()

			// returns the year (four digits)
			var currentYear = currentTime.getFullYear()

			$(".payment-options .form-list").hide();
			
			if(method == 'se_payment_method-cc') {

				$(".se_rv_payment .se-review-value").text("Credit Card");
				$(".payment-options #se_payment_method-cc").show();

				priceProcessing.show();
				updateAndShowProcessingCost();

				// $("#se_payment-options").valid();
				$("#se_payment-options").validate({
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

				priceProcessing.show();
				updateAndShowProcessingCost();
				
				$(".se_rv_payment .se-review-value").text("Paypal");
				$(".payment-options #se_payment_method-paypal").show();
				
			} else if(method == 'se_payment_method-check') {
				priceProcessing.hide();
				removeAndHideProcessingCost();

				$(".se_rv_payment .se-review-value").text("Check");
				$(".payment-options #se_payment_method-check").show();

			}

		});


		
		deposit_select.change(function(){

			var deposit = $(this).find("option:selected").val();
			
			if(deposit == '1') {

				$(".se_rv_payment_type  .se-review-value").text("50% Deposit");
				render_deposit_price_box();
			
			} else {

				$(".se_rv_payment_type  .se-review-value").text("Full Balance");
				hide_deposit_price_box();				
			}

		});


		// rest promotion when content chenged
		var ticketPriceResettingHandler = null;
		// promotion_input.on('change keyup paste', function () {
		promotion_input.on('change', function () {
			isDiscoutApplied = false;

			clearTimeout(ticketPriceResettingHandler);
			ticketPriceResettingHandler = setTimeout(function() {
				selectTicket.trigger('change');
				alert('You just entered A New discount Code, please click ‘Apply Code’ link to get discount');
			}, 500); // end of timing function 

		});

		promo_button.on('click', function(e) {

			e.preventDefault();

			var selected_event_id = selectEvent.find('option:selected', this).attr('value'),
				selected_ticket = selectTicket.find('option:selected', this),
				selected_ticket_id = selected_ticket.attr('value');

			$.ajax({
				type: 'GET',
				url: se_ajax_vars.ajax_url, // the localized name of your file
				data: {
					action: 'se_apply_promo_code_ajax_func',
					promo_code: promotion_input.val(),
					event_id: selected_event_id,
					ticket_id: selected_ticket_id
				},
				beforeSend: function() {
					//workshop_form.find(".btn.btn-wide").prop("disabled",true);
				},
				success: function(result) {
					promo_data = JSON.parse(result);

					if (promo_data) {
						isDiscoutApplied = true;

						updateAndShowDiscount(promo_data.discount);
						updateAndShowTotalPrice(promo_data.price_after_discount);
						refresh_deposit_data();						
						alert('The discount Applied. You have got discount: $'+discountCost+' .');

					} else {
						refresh_deposit_data();
						alert('The discount code you entered is invalid.');
					}
				}

			});
		});


	}); // document ready

	function hide_checkout_footer() {
		$("#se_order-review .pricing-container").hide();
		$("#se_order-review .se_deposit_box").hide();
		$("#se_order-review .checkout_pagination").hide();
	}

	return{
		rawNextStep: function(opt, ths) {

			var this_button = $(ths);
				this_section = this_button.parents('.checkout-section'),
				payment_method_selector = $('#se-payment-method'), // select box
				paymentMethod = payment_method_selector.find("option:selected").val(),
				checkOutStep = $('.checkout-steps .step'),
				passportSection = $('.checkout-section.passport-info'),
				this_from = this_section.find('form');

			// Return today's date and time
			var currentTime = new Date()

			// returns the year (four digits)
			var currentYear = currentTime.getFullYear();

			$.validator.setDefaults({
				debug: true,
				// success: "valid"
			});

			if (this_from[0].id == 'se_workshop') {

				window.form_data.worksheet_info =  this_from.serializeArray();

			} else if (this_from[0].id == 'se_personal-info') {

				window.form_data.personal_info =  this_from.serializeArray();

			} else if (this_from[0].id == 'se_passport-info') {

				window.form_data.passport_info =  this_from.serializeArray();

			} else if (this_from[0].id == 'se_payment-options') {

				window.form_data.payment_info =  this_from.serializeArray();

				if($("#se_payment_method-cc").is(":visible")) {
					this_from.validate({
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
								minlength: 4,
								maxlength: 4,
								range: [currentYear, currentYear+10],
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
				}

			} else if (this_from[0].id == 'se_order-review') {

				window.form_data.order =  this_from.serializeArray();

			}

			if( !this_button.hasClass('btn-go-back') && !this_from.valid() ) {

				alert('Please Fillout Missing Data');

			} else {

				if ( opt == 'workshop') {
					this_section.fadeOut();
					checkOutStep.removeClass('active');
					$('.checkout-section.workshop').fadeIn();
					$('.checkout-steps .step-1').addClass('active');

				} else if ( opt == 'personal') {
					
					this_section.fadeOut();
					checkOutStep.removeClass('active');
					$('.checkout-section.personal-info').fadeIn();
					$('.checkout-steps .step-2').addClass('active');

				} else if ( opt == 'passport') {

					if (promotion_input.val() == '') {

						// this_section.fadeOut();
						// checkOutStep.removeClass('active');
						// passportSection.fadeIn();
						// $('.checkout-steps .step-3').addClass('active');
						this_section.fadeOut();
						checkOutStep.removeClass('active');
						$('.checkout-section.payment-options').fadeIn();
						$('.checkout-steps .step-4').addClass('active');
						opt == 'payment';
						
					} else {
						if (isDiscoutApplied) {

							// this_section.fadeOut();
							// checkOutStep.removeClass('active');
							// passportSection.fadeIn();
							// $('.checkout-steps .step-3').addClass('active');
							this_section.fadeOut();
							checkOutStep.removeClass('active');
							$('.checkout-section.payment-options').fadeIn();
							$('.checkout-steps .step-4').addClass('active');
							opt == 'payment';

						} else {
							alert('Please apply the discount code you entered.');
						}
					}

				} else if ( opt == 'payment') {
					this_section.fadeOut();
					checkOutStep.removeClass('active');
					$('.checkout-section.payment-options').fadeIn();
					$('.checkout-steps .step-4').addClass('active');

				} else if ( opt == 'order') {

					$.ajax({
						type: 'POST',
						url: se_ajax_vars.ajax_url,
						data: {
							action: 'se_request_order_ajax_func',
							form_data: window.form_data
						},
						beforeSend: function () {
							this_from.find('.se-spinner').show();
							this_from.find('.checkout_pagination .btn').hide();

							if(paymentMethod == 'se_payment_method-cc') {

								isIncludeProcessCost = true;
								window.form_data.worksheet_info[2].value = getTotalPrice();


							} else if(paymentMethod == 'se_payment_method-paypal') {
								isIncludeProcessCost = false;

							} else if(paymentMethod == 'se_payment_method-check') {
								isIncludeProcessCost = false;

							}

							
						},
						success: function (result) {

							data = JSON.parse(result);

							this_from.find('.checkout_pagination .btn').fadeIn();
							this_from.find('.se-spinner').hide();
							
							if (data.error == undefined) {
							
								this_section.fadeOut();
								checkOutStep.removeClass('active');
								$('.checkout-section.order-review').fadeIn();
								$('.checkout-steps .step-5').addClass('active');

								$(".se_rv_name .se-review-value").text(data.first_name + ' ' + data.last_name);
								$(".se_rv_event_start .se-review-value").text(data.event_start_date);
								$(".se_rv_event_end .se-review-value").text(data.event_end_date);
								$(".se_rv_ticket_price .se-review-value").text( '$' + getTotalPrice() );
								$(".se_rv_registration_id .se-review-value").text(data.registration.post_title);
								$(".se_rv_registration_status .se-review-value").text(data.registration_status);
								$(".se_rv_tnx_id .se-review-value").text(data.transaction.post_title + '('+data.transaction_status+')');
								
								if(paymentMethod == 'se_payment_method-cc') {
									
									if( data.tnx_error == undefined )
										send_transaction_successful_msg(data.transaction.ID);

									hide_checkout_footer();

								} else if(paymentMethod == 'se_payment_method-paypal') {

									$("#first_name").val(data.first_name);
									$("#last_name").val(data.last_name);
									$("#payer_email").val(data.email);
									$("#item_number").val(data.transaction.post_title);	
									$("#wp_tnx_id").val(data.transaction.ID);				

								} else if(paymentMethod == 'se_payment_method-check') {

									send_transaction_mail(data.transaction.ID);									
									hide_checkout_footer();

								}

								if (data.transaction2 != undefined) {
									$(".se_rv_tnx_id2 .se-review-value").text(data.transaction2.post_title+  '('+data.transaction_status2+')');
									send_transaction_mail(data.transaction2.ID);
								} else {
									$(".se_rv_tnx_id2").hide();
								}



							} else {
								alert(data.error);
							}

						},
						error: function (XMLHttpRequest) {
							console.log(XMLHttpRequest);
						}
					});

				} else if ( opt == 'confirm') {
					$( "#se_order-review" )[0].submit();

				}

			}
		}
	};


	function send_transaction_mail(transaction_id) {
		$.ajax({
			type: 'GET',
			url: se_ajax_vars.ajax_url, // the localized name of your file
			data: {
				action: 'se_send_transaction_mail_ajax_func',
				data: transaction_id
			},
			beforeSend: function() {
			},
			success: function(msg) {
				console.log(msg);
			}

		});
	}

	function send_transaction_successful_msg(transaction_id) {
		$.ajax({
			type: 'GET',
			url: se_ajax_vars.ajax_url, // the localized name of your file
			data: {
				action: 'se_send_transaction_successfull_mail_ajax_func',
				transaction_id: transaction_id
			},
			beforeSend: function() {
			},
			success: function(msg) {
				console.log(msg);
			}

		});
	}

}(jQuery));


// function rawNextStep(opt, ths) {
// 		SEEventCheckoutModule.rawNextStep(opt, ths);
// }

function rawNextStep(opt, ths) {
	var	this_section = $(ths).parents('.checkout-section');
			var	this_from = this_section.find('form');
				if (this_from[0].id == 'se_payment-options'){
					// first check if refund agreed
					if (document.getElementById('refundbox').checked) {
						SEEventCheckoutModule.rawNextStep(opt, ths);
					} // not agreed
					else { alert('Please agree to our refund policy before you proceed.'); return false;}
					
				} else { //other steps move forward
					SEEventCheckoutModule.rawNextStep(opt, ths);
				}
	
}





