bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.conditions = bulkmail.conditions || {};

	$.each($('.bulkmail-conditions'), function () {

		var _self = $(this),
			conditions = _self.find('.bulkmail-conditions-wrap'),
			groups = _self.find('.bulkmail-condition-group'),
			cond = _self.find('.bulkmail-condition');

		groups.eq(0).appendTo(_self.find('.bulkmail-condition-container'));

		!$.trim(conditions.html()) && conditions.empty();

		datepicker();

		_self
			.on('click', '.add-condition', function () {
				var id = groups.length,
					clone = groups.eq(0).clone();

				clone.removeAttr('id').appendTo(conditions).data('id', id).show();
				$.each(clone.find('input, select'), function () {
					var _this = $(this),
						name = _this.attr('name');
					_this.attr('name', name.replace(/\[\d+\]/, '[' + id + ']')).prop('disabled', false);
				});
				clone.find('.condition-field').val('').focus();
				datepicker();
				groups = _self.find('.bulkmail-condition-group');
				cond = _self.find('.bulkmail-condition');
			})
			.on('click', '.add-or-condition', function () {
				var cont = $(this).parent(),
					id = cont.find('.bulkmail-condition').last().data('id'),
					clone = cond.eq(0).clone();

				clone.removeAttr('id').appendTo(cont).data('id', ++id);
				$.each(clone.find('input, select'), function () {
					var _this = $(this),
						name = _this.attr('name');
					_this.attr('name', name.replace(/\[\d+\]\[\d+\]/, '[' + cont.data('id') + '][' + id + ']')).prop('disabled', false);
				});
				clone.find('.condition-field').val('').focus();
				datepicker();
				cond = _self.find('.bulkmail-condition');
			});

		conditions
			.on('click', '.remove-condition', function () {
				var c = $(this).parent();
				if (c.parent().find('.bulkmail-condition').length == 1) {
					c = c.parent();
				}
				c.slideUp(100, function () {
					$(this).remove();
					bulkmail.trigger('updateCount');
				});
			})
			.on('change', '.condition-field', function () {

				var condition = $(this).closest('.bulkmail-condition'),
					field = $(this).val(),
					operator_field, value_field;

				condition.find('div.bulkmail-conditions-value-field').removeClass('active').find('.condition-value').prop('disabled', true);
				condition.find('div.bulkmail-conditions-operator-field').removeClass('active').find('.condition-operator').prop('disabled', true);

				value_field = condition.find('div.bulkmail-conditions-value-field[data-fields*=",' + field + ',"]').addClass('active').find('.condition-value').prop('disabled', false);
				operator_field = condition.find('div.bulkmail-conditions-operator-field[data-fields*=",' + field + ',"]').addClass('active').find('.condition-operator').prop('disabled', false);

				if (!value_field.length) {
					value_field = condition.find('div.bulkmail-conditions-value-field-default').addClass('active').find('.condition-value').prop('disabled', false);
				}
				if (!operator_field.length) {
					operator_field = condition.find('div.bulkmail-conditions-operator-field-default').addClass('active').find('.condition-operator').prop('disabled', false);
				}

				if (!value_field.val()) {
					if (value_field.is('.hasDatepicker')) {
						value_field.datepicker("setDate", "yy-mm-dd");;
					}
				}

				bulkmail.trigger('updateCount');

			})
			.on('change', '.condition-operator', function () {
				bulkmail.trigger('updateCount');
			})
			.on('change', '.condition-value', function () {
				bulkmail.trigger('updateCount');
			})
			.on('click', '.bulkmail-condition-add-multiselect', function () {
				$(this).parent().clone().insertAfter($(this).parent()).find('.condition-value').select().focus();
				return false;
			})
			.on('click', '.bulkmail-condition-remove-multiselect', function () {
				$(this).parent().remove();
				bulkmail.trigger('updateCount');
				return false;
			})
			.on('change', '.bulkmail-conditions-value-field-multiselect > .condition-value', function () {
				if (0 == $(this).val() && $(this).parent().parent().find('.condition-value').size() > 1) $(this).parent().remove();
			})
			.on('click', '.bulkmail-rating > span', function (event) {
				var _this = $(this),
					_prev = _this.prevAll(),
					_all = _this.siblings();
				_all.removeClass('enabled');
				_prev.add(_this).addClass('enabled');
				_this.parent().parent().find('.condition-value').val((_prev.length + 1) / 5).trigger('change');
			})
			.find('.condition-field').prop('disabled', false).trigger('change');

		bulkmail.trigger('updateCount');

		function datepicker() {
			conditions.find('.datepicker').datepicker({
				dateFormat: 'yy-mm-dd',
				firstDay: bulkmail.l10n.conditions.start_of_week,
				showWeek: true,
				dayNames: bulkmail.l10n.conditions.day_names,
				dayNamesMin: bulkmail.l10n.conditions.day_names_min,
				monthNames: bulkmail.l10n.conditions.month_names,
				prevText: bulkmail.l10n.conditions.prev,
				nextText: bulkmail.l10n.conditions.next,
				showAnim: 'fadeIn',
			});
		}

	});

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));