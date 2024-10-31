bulkmail = (function (bulkmail, $, window, document) {
	"use strict"

	var dialog = $('#bulkmail-deactivation-dialog'),
		form = $('#bulkmail-deactivation-survey'),
		survey_extra = $('.bulkmail-survey-extra'),
		textareas = form.find('textarea');

	$('tr[data-slug="bulkmail"]').on('click', '.deactivate > a', function () {
		bulkmail.dialog.open('deactivation-dialog');
		return false;
	})

	dialog
		.on('click', '.deactivate', function () {
			form.submit();
			return false;
		})
		.on('click', '.cancel', function () {
			bulkmail.dialog.close();
			return false;
		});

	$('.bulkmail-delete-data').on('change', '[name="delete_data"]', function () {
		$('.bulkmail-delete-data').find('input').not(this).prop('checked', $(this).prop('checked')).prop('disabled', !$(this).prop('checked'));
	})

	form
		.on('submit', function () {
			if (!$('[name="bulkmail_surey_reason"]:checked').length) {
				alert(bulkmail.l10n.deactivate.select_reason);
				return false;
			}
		})
		.on('change', '[name="bulkmail_surey_reason"]', function () {
			textareas.prop('disabled', true);
			survey_extra.hide();
			$(this).parent().parent().find('.bulkmail-survey-extra').show().find('textarea').prop('disabled', false).focus();
		});

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));