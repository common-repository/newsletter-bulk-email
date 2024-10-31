bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.dialog = bulkmail.dialog || {};

	var current,
		dialog;

	bulkmail.$.document
		.on('click', '.notification-dialog-dismiss', function (event) {
			event.stopPropagation();
			cancel();
		})
		.on('click', '.notification-dialog-background', function (event) {
			event.stopPropagation();
			cancel();
		})
		.on('click', '.notification-dialog-submit', function (event) {
			event.stopPropagation();
			submit();
		});

	function cancel() {
		close();
	}

	function submit() {
		close();
	}

	function close() {
		dialog.addClass('hidden');
		bulkmail.$.document
			.off('keyup.bulkmail_dialog');
		current = null;
	}

	function open(id) {
		dialog = $('.bulkmail-' + id);
		current = id;
		dialog.removeClass('hidden');
		bulkmail.$.document
			.on('keyup.bulkmail_dialog', function (event) {
				if (event.which == 27) {
					cancel();
				}
			});
	}

	bulkmail.dialog.current = current;
	bulkmail.dialog.close = close;
	bulkmail.dialog.open = open;

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));