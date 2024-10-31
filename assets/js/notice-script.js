bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.notices = bulkmail.notices || {};

	bulkmail.notices.$ = $('.bulkmail-notice');

	bulkmail.$.document
		.on('click', '.bulkmail-notice .notice-dismiss, .bulkmail-notice .dismiss', function (event) {

			event.preventDefault();

			var el = $(this).closest('.bulkmail-notice'),
				id = el.data('id'),
				type = !event.altKey ? 'notice_dismiss' : 'notice_dismiss_all';

			if (event.altKey) el = bulkmail.notices.$;

			if (id) {
				bulkmail.util.ajax(type, {
					id: id
				});
				el.fadeTo(100, 0, function () {
					el.slideUp(100, function () {
						el.remove();
					});
				})
			}
		});

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));