jQuery(document).ready(function (jQuery) {

	"use strict"

	// fix for the missing placeholder feature in IE < 10
	if (!placeholderIsSupported()) {

		jQuery('body')
			.on('focus.bulkmail', 'form.bulkmail-form input[placeholder]', function () {
				var el = jQuery(this);
				if (el.val() == el.attr("placeholder"))
					el.val("");
			})
			.on('blur.bulkmail', 'form.bulkmail-form input[placeholder]', function () {
				var el = jQuery(this);
				if (el.val() == "")
					el.val(el.attr("placeholder"));

			})
			.on('submit.bulkmail', 'form.bulkmail-form', function () {
				var form = jQuery(this),
					inputs = form.find('input[placeholder]');


				jQuery.each(inputs, function () {
					var el = jQuery(this);
					if (el.val() == el.attr("placeholder"))
						el.val("");
				});

			})

		jQuery('form.bulkmail-form').find('input[placeholder]').trigger('blur.bulkmail');

	}

	function placeholderIsSupported() {
		var test = document.createElement('input');
		return ('placeholder' in test);
	}

});