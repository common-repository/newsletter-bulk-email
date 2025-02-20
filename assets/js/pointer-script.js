jQuery(document).ready(function ($) {

	"use strict"

	var pointers = [],
		init;

	$.each(BulkmailPointer.pointers, function (i, e) {

		pointers[i] = $.extend(BulkmailPointer.pointers[i], {

			pointerClass: 'wp-pointer bulkmail-tour-pointer-' + i,
			// Add 'Close' button
			buttons: function (event, t) {

				var button = jQuery('<a id="pointer-close" class="close" style="margin:10px 0 0 13px;float:left;cursor:pointer" >' + BulkmailPointer.close + '</a>');
				button.bind('click.pointer', function () {
					t.element.pointer('close');
				});
				return button;
			},
			dissmisstour: function () {
				$.post(ajaxurl, {
					tour: BulkmailPointer.tourname,
					action: 'dismiss-wp-pointer'
				});
			},
			close: function (a) {}
		});

	});

	init = function (id) {

		id = id || 0;


		if (!pointers[id]) return;

		$(pointers[id].id).pointer(pointers[id]).pointer('open');

		if (pointers[id].onactive) {
			var f = new Function(pointers[id].onactive);
			f.call(pointers[id]);
		}
		if (pointers[id].button2) {

			$('.bulkmail-tour-pointer-' + id)
				.on('click.pointer', '#pointer-primary', function () {

					if (pointers[id].onend) {
						var f = new Function(pointers[id].onend);
						f.call(pointers[id]);
					}
					$(pointers[id].id).pointer('close');
					if (pointers[id + 1]) init(id + 1);

				})
				.on('click.pointer', '#pointer-close', function () {

					// Post to admin ajax to disable pointers when user clicks "Close"
					pointers[id].dissmisstour();

				})
				.find('.wp-pointer-buttons').prepend('<a id="pointer-primary" class="button-primary right">' + pointers[id].button2 + '</a>');
		}

	};

	BulkmailPointer.type = function (el, text, speed) {

		speed = speed || 10

		var el = $(el).val('').focus().select(),
			p = 0,
			t = text.split(''),
			i = setInterval(function () {
				el.val(text.substr(0, Math.round(text.length / 100 * p)));
				p++;
				if (p >= 100) clearInterval(i);

			}, speed);

	};

	BulkmailPointer.iframe = $('#bulkmail_iframe').contents();
	$('#bulkmail_iframe').load(function () {
		BulkmailPointer.iframe = $('#bulkmail_iframe').contents();
	});



	$(window).on('load', function () {
		init();
	});

});