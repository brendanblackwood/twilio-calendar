$(document).ready(function() {
	$('.calendar-body-date').setCalendarDate();
	
	$('form[name="register"]').submit(function(e) {
		e.preventDefault();
		var $form = $(this);
		
		$.post('/web/account.php', $(this).serialize(), function(response) {
			$form.processResponse({ response: response });
		});
	});
});

function getShortMonth(month) {
	switch (month) {
		case 1:
			return 'JAN';
		case 2:
			return 'FEB';
		case 3:
			return 'MAR';
		case 4:
			return 'APR';
		case 5:
			return 'MAY';
		case 6:
			return 'JUN';
		case 7:
			return 'JUL';
		case 8:
			return 'AUG';
		case 9:
			return 'SEP';
		case 10:
			return 'OCT';
		case 11:
			return 'NOV';
		case 12:
			return 'DEC';
	}
}

$.fn.extend({
	setCalendarDate: function(options) {
		var defaults = {
			theDate: 'now'
		}
		options = $.extend(defaults, options);
		
		var dateObj = new Date(),
			theDate = dateObj.getDate(),
			theMonth = getShortMonth(dateObj.getMonth() + 1),
			theYear = dateObj.getFullYear();
		
		$(this).find('.day')
			.text(theDate);
		$(this).find('.month')
			.text(theMonth);
		$(this).find('.year')
			.text(theYear);
	},
	processResponse: function(options) {
		var response = $.parseJSON(options.response);

		if ( response.status == "fail" ) {
			var messages = response.data.messages;

			for ( var i in messages ) {
				
				$('div.error[data-type="'+i+'"]')
					.text(messages[i])
					.slideDown(300);
				$('input[name="'+i+'"]')
					.addClass('hasError');
			}
		}
		else {
			// we good to go
		}
	}
});