bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	var isMobile = $(document.body).hasClass('mobile'),
		isWPDashboard = $(document.body).hasClass('index-php'),
		$handleButtons = $('.postbox .handlediv'),
		subscribers = $('.bulkmail-mb-subscribers'),
		subscriberselect = $('#bulkmail-subscriber-range'),
		chartelement = $('#subscriber-chart-wrap'),
		canvas = $('#subscriber-chart'),
		chart,
		ctx,

		chartoptions = {
			responsive: false,
			legend: false,
			animationEasing: "easeOutExpo",
			maintainAspectRatio: false,
			tooltips: {
				backgroundColor: 'rgba(56,56,56,0.9)',
				displayColors: false,
				cornerRadius: 2,
				caretSize: 8,
				callbacks: {
					label: function (a, b) {
						return bulkmail.util.sprintf(bulkmail.l10n.dashboard.subscribers, number_format(a.yLabel));
					}
				}
			},
			scales: {
				xAxes: [{
					ticks: {
						maxTicksLimit: 20
					},
					_type: "time",
					_time: {
						format: 'MM/DD/YYYY HH:mm',
						round: 'day',
						tooltipFormat: 'll HH:mm'
					}
				}],
				yAxes: [{
					ticks: {
						callback: function (value) {
							return format(value, true);
						},
					}
				}]
			}
		};

	if (canvas.length) {
		ctx = canvas[0].getContext("2d");
	}

	subscriberselect.on('change', function () {
		drawChart();
	}).trigger('change');

	if (!isWPDashboard) {
		$('.meta-box-sortables').sortable({
			placeholder: 'sortable-placeholder',
			connectWith: '.meta-box-sortables',
			items: '.postbox',
			handle: '.hndle',
			cursor: 'move',
			delay: (isMobile ? 200 : 0),
			distance: 2,
			tolerance: 'pointer',
			forcePlaceholderSize: true,
			helper: function (event, element) {
				return element.clone()
					.find(':input')
					.attr('name', function (i, currentName) {
						return 'sort_' + parseInt(Math.random() * 100000, 10).toString() + '_' + currentName;
					})
					.end();
			},
			opacity: 0.65,
			update: function (e, ui) {
				orderMetaBoxes();
			}
		});

		$('.postbox .handlediv')
			.each(function () {
				var $el = $(this);
				$el.attr('aria-expanded', !$el.parent('.postbox').hasClass('closed'));
			})
			.on('click', function () {
				var $el = $(this);
				$el.parent('.postbox').toggleClass('closed');
				$el.attr('aria-expanded', !$el.parent('.postbox').hasClass('closed'));
			});
	}


	$(document)
		.on('verified.bulkmail', function (event, purchasecode, username, email) {

			$('.bulkmail-purchasecode').html(purchasecode);
			$('.bulkmail-username').html(username);
			$('.bulkmail-email').html(email);

			$('#bulkmail-mb-bulkmail').addClass('verified');

			$('#bulkmail-register-panel').delay(2500).fadeTo(400, 0, function () {
				$('#bulkmail-register-panel').slideUp(400);
			})
		})
		.on('click', '.order-lower-indicator', function () {
			var current = $(this).closest('.postbox'),
				sibling = current.next();
			if (sibling.length) {
				current.insertAfter(sibling);
			}
			orderMetaBoxes();
		})
		.on('click', '.order-higher-indicator', function () {
			var current = $(this).closest('.postbox'),
				sibling = current.prev();
			if (sibling.length) {
				current.insertBefore(sibling);
			}
			orderMetaBoxes();
		})
		.on('click', '.toggle-indicator', function () {
			$(this).closest('.postbox').toggleClass('closed');
			toggleMetaBoxes();
		})
		.on('click', '.hide-postbox-tog', function () {

			$('#' + $(this).val())[$(this).is(':checked') ? 'show' : 'hide']().removeClass('closed');
			toggleMetaBoxes();

		})
		.on('click', '.locked', function () {
			$('.purchasecode').focus().select();
		})
		.on('click', '.check-for-update', function () {
			var _this = $(this);
			_this.html(bulkmail.l10n.dashboard.checking);
			bulkmail.util.ajax('check_for_update', function (response) {
				_this.html(bulkmail.l10n.dashboard.check_again);
				if (response.success) {
					_this.closest('.postbox')[response.update ? 'addClass' : 'removeClass']('has-update');
					$('.update-version').html(response.version);
					$('.update-last-check').html(response.last_update);

				}
			});
			return false;
		})
		.one('click', '.load-language', function () {
			var _this = $(this);
			_this.html(bulkmail.l10n.dashboard.downloading);
			bulkmail.util.ajax('load_language', function (response) {
				if (response.success) {
					_this.html(bulkmail.l10n.dashboard.reload_page);
				}
			});
			return false;
		})
		.on('click', '.reset-license', function () {

			if (!confirm(bulkmail.l10n.dashboard.reset_license)) {
				return false;
			}
		});

	var metabox = (function (type) {

		if (!type) {
			return;
		}

		var current,
			box = $('.bulkmail-mb-' + type),
			dropdown = box.find('.bulkmail-mb-select'),
			label = box.find('.bulkmail-mb-label'),
			link = box.find('.bulkmail-mb-link'),
			linktmpl = link.attr('href');

		if (!dropdown.length) {
			return;
		}

		dropdown
			.on('change', function () {
				loadEntry($(this).val());
			})
			.trigger('change');

		$(document)
			.on('heartbeat-tick', function (e, data) {
				if (current) {
					loadEntry(current, true);
				}
			});

		box.find('.piechart').easyPieChart({
			animate: 1000,
			rotate: 180,
			barColor: bulkmail.colors.main,
			trackColor: bulkmail.colors.track,
			lineWidth: 9,
			size: 75,
			lineCap: 'butt',
			onStep: function (value) {
				this.$el.find('span').text(Math.round(value));
			},
			onStop: function (value) {
				this.$el.find('span').text(Math.round(value));
			}
		});

		function loadEntry(ID, silent) {

			if (!silent) {
				box.addClass('bulkmail-loading');
			}

			bulkmail.util.ajax('get_dashboard_data', {
				type: type,
				id: ID
			}, function (response) {

				var data = response.data;

				link
					.html(data.name)
					.removeAttr('class')
					.addClass('bulkmail-mb-link')
					.attr('href', linktmpl.replace('%d', data.ID));
				if (data.status) {
					link.addClass('status-' + data.status);
				}

				box.find('.stats-total').html(data.sent_formatted);
				box.find('.stats-open').data('easyPieChart').update(data.openrate * 100);
				box.find('.stats-clicks').data('easyPieChart').update(data.clickrate * 100);
				box.find('.stats-unsubscribes').data('easyPieChart').update(data.unsubscriberate * 100);
				box.find('.stats-bounces').data('easyPieChart').update(data.bouncerate * 100);

				current = data.ID;
				box.removeClass('bulkmail-loading');

			});
		}

	});

	var campaignmetabox = new metabox('campaigns');
	var listmetabox = new metabox('lists');

	function drawChart(sets, scale, limit, offset) {

		subscribers.addClass('bulkmail-loading');

		bulkmail.util.ajax('get_dashboard_chart', {
			range: subscriberselect.val()
		}, function (response) {

			resetChart();
			subscribers.removeClass('bulkmail-loading');

			if (!chart) {
				chart = new Chart(ctx, {
					type: 'line',
					data: response.chart,
					options: chartoptions
				});
			}

		});

	}

	function resetChart() {
		chart = null;
		if (canvas) {
			canvas.remove();
		}
		canvas = $('<canvas>').prependTo(chartelement);
		ctx = canvas[0].getContext("2d");
		canvas.attr({
			'width': chartelement.width(),
			'height': chartelement.height()
		});

	}

	function updateMetaBoxes() {
		orderMetaBoxes();
		toggleMetaBoxes();
	};

	function orderMetaBoxes() {

		var order = {};

		$.each($('.postbox-container'), function () {
			var col = $(this).data('id');

			$.each($(this).find('.postbox'), function () {
				if (!order[col]) {
					order[col] = [];
				}
				order[col].push(this.id);
			});

			if (order[col]) {
				order[col] = order[col].join(',');
			}

		});

		var data = {
			action: 'meta-box-order',
			_ajax_nonce: $('#meta-box-order-nonce').val(),
			page: 'newsletter_page_bulkmail_dashboard',
			order: order
		};

		$.post(ajaxurl, data);

	}

	function toggleMetaBoxes() {

		var hidden = $('.postbox:hidden').map(function () {
				return this.id;
			}).toArray(),
			closed = $('.postbox.closed').map(function () {
				return this.id;
			}).toArray();

		var data = {
			action: 'closed-postboxes',
			closedpostboxesnonce: $('#closedpostboxesnonce').val(),
			closed: closed.length ? closed.join(',') : '',
			hidden: hidden.length ? hidden.join(',') : '',
			page: 'newsletter_page_bulkmail_dashboard'
		};

		$.post(ajaxurl, data);

	}

	function format(value, konly) {

		if (value >= 1000000) {
			return (value / 1000).toFixed(1) + 'M';
		} else if (value >= 1000 && !konly) {
			return (value / 1000).toFixed(1) + 'K';
		}

		return !(value % 1) ? number_format(value) : '';
	}

	function number_format(number, decimals, decPoint, thousandsSep) {

		number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
		var n = !isFinite(+number) ? 0 : +number
		var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
		var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
		var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
		var s = ''
		var toFixedFix = function (n, prec) {
			var k = Math.pow(10, prec)
			return '' + (Math.round(n * k) / k)
				.toFixed(prec)
		}
		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || ''
			s[1] += new Array(prec - s[1].length + 1).join('0')
		}
		return s.join(dec)
	}


	return bulkmail;

}(bulkmail || {}, jQuery, window, document));