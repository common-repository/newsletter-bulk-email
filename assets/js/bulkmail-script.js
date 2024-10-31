window.bulkmail = window.bulkmail || {};

// block localization
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.l10n = window.bulkmail_l10n;

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end localization

// events
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	var triggertimeout,
		isEnabled = !$('#bulkmail_disabled').val(),
		events = {
			documentReady: [],
			windowLoad: [],
		},
		last;

	bulkmail.events = bulkmail.events || false;

	bulkmail.status = {
		documentReady: false,
		windowLoad: false,
		windowLoadPending: false,
	};

	//already events registered
	if (bulkmail.events) {
		for (var i in bulkmail.events) {
			bulkmail.log(i, bulkmail.events[i]);
			if (typeof bulkmail.events[i] == 'string') {
				last = bulkmail.events[i];
				events[last] = events[last] || [];
				continue;
			}
			events[last].push(bulkmail.events[i]);
		}
	}

	bulkmail.events = events;

	$(document).ready(documentReady);
	$(window).on("load", windowLoad);

	function documentReady(context) {
		context = typeof context === typeof undefined ? $ : context;
		events.documentReady.forEach(function (component) {
			component(context);
		});
		bulkmail.status.documentReady = true;
		if (bulkmail.status.windowLoadPending) {
			windowLoad(setContext());
		}
	}

	function windowLoad(context) {
		if (bulkmail.status.documentReady) {
			bulkmail.status.windowLoadPending = false;
			context = typeof context === "object" ? $ : context;
			events.windowLoad.forEach(function (component) {
				component(context);
			});
			bulkmail.status.windowLoad = true;
		} else {
			bulkmail.status.windowLoadPending = true;
		}
	}

	function debug(data, type) {
		if (console) {
			for (var i = 0; i < data.length; i++) {
				console[type](data[i]);
			}
		}
	}

	function setContext(contextSelector) {
		var context = $;
		if (typeof contextSelector !== typeof undefined) {
			return function (selector) {
				return $(contextSelector).find(selector);
			};
		}
		return context;
	}

	bulkmail.events.push = function () {

		var params = Array.prototype.slice.call(arguments),
			event = params.shift(),
			callbacks = params || null;

		bulkmail.events[event] = bulkmail.events[event] || [];

		for (var i in callbacks) {
			bulkmail.events[event].push(callbacks[i]);
		}

		return true;

	}

	bulkmail.trigger = function () {

		var params = Array.prototype.slice.call(arguments),
			triggerevent = params.shift(),
			args = params || null;

		if (bulkmail.events[triggerevent]) {
			for (var i = 0; i < bulkmail.events[triggerevent].length; i++) {
				bulkmail.events[triggerevent][i].apply(bulkmail, args);
			}
		} else {
			//events[triggerevent] = [];
		}
	}

	bulkmail.log = function () {

		debug(arguments, 'log');
	}

	bulkmail.error = function () {

		debug(arguments, 'error');
	}

	bulkmail.warning = function () {

		debug(arguments, 'warn');
	}


	return bulkmail;

}(bulkmail || {}, jQuery, window, document));


bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.util = bulkmail.util || {};

	bulkmail.util.requestAnimationFrame = window.requestAnimationFrame ||
		window.mozRequestAnimationFrame ||
		window.webkitRequestAnimationFrame ||
		window.msRequestAnimationFrame;

	bulkmail.util.ajax = function (action, data, callback, errorCallback, dataType) {

		if ($.isFunction(data)) {
			if ($.isFunction(callback)) {
				errorCallback = callback;
			}
			callback = data;
			data = {};
		}

		dataType = dataType ? dataType : "JSON";
		$.ajax({
			type: 'POST',
			url: bulkmail.ajaxurl,
			data: $.extend({
				action: 'bulkmail_' + action,
				_wpnonce: bulkmail.wpnonce
			}, data),
			success: function (data, textStatus, jqXHR) {
				callback && callback.call(this, data, textStatus, jqXHR);
			},
			error: function (jqXHR, textStatus, errorThrown) {
				var response = $.trim(jqXHR.responseText);
				if (textStatus == 'error' && !errorThrown) return;
				bulkmail.log(response, 'error');
				if ('JSON' == dataType) {
					var maybe_json = response.match(/{(.*)}$/);
					if (maybe_json && callback) {
						try {
							callback.call(this, $.parseJSON(maybe_json[0]));
						} catch (e) {
							bulkmail.log(e, 'error');
						}
						return;
					}
				}
				errorCallback && errorCallback.call(this, jqXHR, textStatus, errorThrown);
				alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\n' + bulkmail.l10n.common.check_console)

			},
			dataType: dataType
		});
	}

	bulkmail.util.rgb2hex = function (str) {
		var colors = str.match(/rgb\((\d+), ?(\d+), ?(\d+)\)/);

		function nullify(val) {
			val = parseInt(val, 10).toString(16);
			return val.length > 1 ? val : '0' + val; // 0 -> 00
		}
		return colors ? '#' + nullify(colors[1]) + nullify(colors[2]) + nullify(colors[3]) : str;

	}

	bulkmail.util.sanitize = function (string) {
		return $.trim(string).toLowerCase().replace(/ /g, '_').replace(/[^a-z0-9_-]*/g, '');
	}

	bulkmail.util.sprintf = function () {
		var a = Array.prototype.slice.call(arguments),
			str = a.shift(),
			total = a.length,
			reg;
		for (var i = 0; i < total; i++) {
			reg = new RegExp('%(' + (i + 1) + '\\$)?(s|d|f)');
			str = str.replace(reg, a[i]);
		}
		return str;
	}

	bulkmail.util.isWebkit = 'WebkitAppearance' in document.documentElement.style;
	bulkmail.util.isMozilla = (/firefox/i).test(navigator.userAgent);
	bulkmail.util.isMSIE = (/msie|trident/i).test(navigator.userAgent);
	bulkmail.util.isTouchDevice = 'ontouchstart' in document.documentElement;

	bulkmail.util.CodeMirror = null;

	bulkmail.events.push('documentReady', function () {
		bulkmail.util.CodeMirror = wp.CodeMirror || window.CodeMirror;
	});

	bulkmail.util.top = function () {
		return $('html,body').scrollTop() || document.scrollingElement.scrollTop;
	}

	bulkmail.util.scroll = function (pos, callback, speed) {
		var t;
		pos = Math.round(pos);
		if (isNaN(speed)) speed = 200;
		if (!bulkmail.util.isMSIE && bulkmail.util.top() == pos) {
			callback && callback();
			return
		}
		$('html,body').stop().animate({
			'scrollTop': pos
		}, speed, function () {
			//prevent double execution
			clearTimeout(t);
			t = setTimeout(callback, 0);
		});
	}

	bulkmail.util.jump = function (val, rel) {
		val = Math.round(val);
		if (rel) {
			window.scrollBy(0, val);
		} else {
			window.scrollTo(0, val);
		}
	}

	bulkmail.util.inViewport = function (el, offset) {
		var rect = el.getBoundingClientRect();

		if (!offset) offset = 0;

		//only need top and bottom
		return (
			rect.top + offset >= 0 &&
			rect.top - offset <= (window.innerHeight || document.documentElement.clientHeight) /*or $(window).height() */
		);
	}

	bulkmail.util.debounce = function (callback, delay) {

		return bulkmail.util.throttle(callback, delay, true);

	}

	bulkmail.util.throttle = function (callback, delay, debounce) {
		var timeout,
			last = 0;

		if (delay === undefined) delay = 250;

		function api() {
			var that = this,
				elapsed = +new Date() - last,
				args = arguments;

			function run() {
				last = +new Date();
				callback.apply(that, args);
			};

			function clear() {
				timeout = undefined;
			};

			if (debounce && !timeout) {
				run();
			}

			timeout && clearTimeout(timeout);

			if (debounce === undefined && elapsed > delay) {
				run();
			} else {
				timeout = setTimeout(debounce ? clear : run, debounce === undefined ? delay - elapsed : delay);
			}
		};

		return api;
	};

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));



bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.$ = {};
	bulkmail.dom = {};

	bulkmail.$.window = $(window);
	bulkmail.$.document = $(document);

	//open externals in a new tab
	bulkmail.$.document
		.on('click', 'a.external', function () {
			window.open(this.href);
			return false;
		})

	bulkmail.util.tb_position = function () {
		if (!window.TB_WIDTH || !window.TB_HEIGHT) return;
		$('#TB_window').css({
			marginTop: '-' + parseInt((TB_HEIGHT / 2), 10) + 'px',
			marginLeft: '-' + parseInt((TB_WIDTH / 2), 10) + 'px',
			width: TB_WIDTH + 'px'
		});
	}

	bulkmail.events.push('documentReady', function () {
		window.tb_position = bulkmail.util.tb_position;
		for (var i in bulkmail.$) {
			bulkmail.dom[i] = bulkmail.$[i][0];
		}
	});
	return bulkmail;

}(bulkmail || {}, jQuery, window, document));