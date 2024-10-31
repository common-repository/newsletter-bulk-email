// block DOM
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.$ = bulkmail.$ || {};

	bulkmail.$.body = $('body');
	bulkmail.$.wpbody = $('#wpbody');
	bulkmail.$.form = $('#post');
	bulkmail.$.title = $('#title');
	bulkmail.$.iframe = $('#bulkmail_iframe');
	bulkmail.$.templateWrap = $('#template-wrap');
	bulkmail.$.template = $('#bulkmail_template');
	bulkmail.$.datafields = $('[name^="bulkmail_data"]');
	bulkmail.$.content = $('#content');
	bulkmail.$.excerpt = $('#excerpt');
	bulkmail.$.plaintext = $('#plain-text-wrap');
	bulkmail.$.html = $('#html-wrap');
	bulkmail.$.head = $('#head');
	bulkmail.$.optionbar = $('#optionbar');
	bulkmail.$.editbar = $('#editbar');

	bulkmail.campaign_id = parseInt($('#post_ID').val(), 10);
	bulkmail.user_id = parseInt($('#user-id').val(), 10);
	bulkmail.enabled = true;
	bulkmail.editable = !$('#bulkmail_disabled').val();

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end DOM

// events
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.events.push('documentReady', function () {
		if (!bulkmail.editable) {
			bulkmail.$.iframe.on('load', function () {
				bulkmail.trigger('iframeLoaded');
			});
		}
		bulkmail.$.iframe.attr('src', bulkmail.$.iframe.data('src'));
	});

	bulkmail.events.push('editorLoaded', function () {
		bulkmail.$.iframe.removeClass('loading');
	});

	bulkmail.events.push('iframeLoaded', function () {
		bulkmail.$.iframe.removeClass('loading');
		bulkmail.$.iframecontents = bulkmail.$.iframe.contents();
	});

	bulkmail.events.push('disable', function () {
		bulkmail.enabled = false;
		$('.button').prop('disabled', true);
		$('input:visible').prop('disabled', true);
	});

	bulkmail.events.push('enable', function () {
		$('.button').prop('disabled', false);
		$('input:visible, input.wp-color-picker').prop('disabled', false);
		bulkmail.enabled = true;
	});

	bulkmail.events.push('redraw', function () {
		bulkmail.trigger('refresh');
	});

	bulkmail.events.push('resize', function () {
		$('#editor-height').val(bulkmail.editor.getHeight());
	});

	bulkmail.events.push('save', function () {

		if (!bulkmail.editor || !bulkmail.editor.loaded) return;

		var content = bulkmail.editor.getFrameContent(),
			length = bulkmail.optionbar.undos.length,
			lastundo = bulkmail.optionbar.undos[length - 1];

		if (lastundo != content) {

			bulkmail.$.content.val(content);
			bulkmail.details.$.preheader.prop('readonly', !content.match('{preheader}'));
			bulkmail.optionbar.undos = bulkmail.optionbar.undos.splice(0, bulkmail.optionbar.currentUndo + 1);
			bulkmail.optionbar.undos.push(content);

			if (length >= bulkmail.l10n.campaigns.undosteps) bulkmail.optionbar.undos.shift();
			bulkmail.optionbar.currentUndo = bulkmail.optionbar.undos.length - 1;

			if (bulkmail.optionbar.currentUndo) bulkmail.$.optionbar.find('a.undo').removeClass('disabled');
			bulkmail.$.optionbar.find('a.redo').addClass('disabled');

			if (wp && wp.autosave) wp.autosave.local.save();
		}

	});

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end events

// block general
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	var resizeTimout;

	if (bulkmail.util.isMSIE) bulkmail.$.body.addClass('ie');
	if (bulkmail.util.isTouchDevice) bulkmail.$.body.addClass('touch');

	bulkmail.$.window
		.on('resize.bulkmail', doResize);

	bulkmail.$.document
		.on('change', 'input[name=screen_columns]', function () {
			bulkmail.$.window.trigger('resize');
		})
		.on('click', '.restore-backup', function (e, data) {
			var data = wp.autosave.local.getSavedPostData();
			bulkmail.editor.setContent(data.content);
			bulkmail.$.title.val(data.post_title);
			return false;
		})
		.on('submit', 'form#post', function () {
			if (!bulkmail.enabled) return false;
			bulkmail.trigger('save');
		})
		.on('change', '.dynamic_embed_options_taxonomy', function () {
			var $this = $(this),
				val = $this.val();
			$this.parent().find('.button').remove();
			if (val != -1) {
				if ($this.parent().find('select').length < $this.find('option').length - 1)
					$(' <a class="button button-small add_embed_options_taxonomy">' + bulkmail.l10n.campaigns.add + '</a>').insertAfter($this);
			} else {
				$this.parent().html('').append($this);
			}

			return false;
		})
		.on('click', '.add_embed_options_taxonomy', function () {
			var $this = $(this),
				el = $this.prev().clone();

			el.insertBefore($this).val('-1');
			$('<span> ' + bulkmail.l10n.campaigns.or + ' </span>').insertBefore(el);
			$this.remove();

			return false;
		});

	// overwrite autosave function since we don't need it
	!bulkmail.editable && bulkmail.events.push('documentReady', function () {
		bulkmail.$.window.off('beforeunload.edit-post');
	});

	function doResize() {
		clearTimeout(resizeTimout);
		resizeTimout = setTimeout(function () {
			bulkmail.trigger('refresh');
		}, 250);
	}

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end general


// block utils
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.util = bulkmail.util || {};

	bulkmail.util.isTinyMCE = null;

	bulkmail.events.push('documentReady', function () {
		bulkmail.util.isTinyMCE = typeof tinymce == 'object';
	});

	bulkmail.util.getRealDimensions = function (el, callback) {
		el = el.eq(0);
		if (el.is('img') && el.attr('src')) {
			var image = new Image(),
				factor;
			image.onload = function () {
				factor = ((image.width / el.width()).toFixed(1) || 1);
				if (callback) callback.call(this, image.width, image.height, isFinite(factor) ? parseFloat(factor) : 1)
			}
			image.src = el.attr('src');
		};
	}

	bulkmail.util.tempMsg = function (message, type, el, callback) {

		var msg = $('<div class="' + (type) + '"><p>' + message + '</p></div>').hide().prependTo(el).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
			msg.remove();
			callback && callback();
		});

		return msg;
	}

	bulkmail.util.selectRange = function (input, startPos, endPos) {
		if (document.selection && document.selection.createRange) {
			input.focus();
			input.select();
			var range = document.selection.createRange();
			range.collapse(true);
			range.moveEnd("character", endPos);
			range.moveStart("character", startPos);
			range.select();
		} else {
			input.selectionStart = startPos;
			input.selectionEnd = endPos;
		}
		return true;
	}

	bulkmail.util.changeColor = function (color_from, color_to, element, original) {
		if (!color_from) color_from = color_to;
		if (!color_to) return false;
		color_from = color_from.toUpperCase();
		color_to = color_to.toUpperCase();
		if (color_from == color_to) return false;
		var raw = bulkmail.editor.getContent(),
			reg = new RegExp(color_from, 'gi');

		if (element)
			element.data('value', color_to);

		$('#bulkmail-color-' + color_from.substr(1)).attr('id', 'bulkmail-color-' + color_to.substr(1));

		if (reg.test(raw)) {
			bulkmail.editor.setContent(raw.replace(reg, color_to), 0);
		}

		if (original) {
			//bulkmail.editor.colors.map[original] = color_to;
		}

	}

	bulkmail.util.replace = function (str, match, repl) {
		if (match === repl)
			return str;
		do {
			str = str.replace(match, repl);
		} while (match && str.indexOf(match) !== -1);
		return str;
	}

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end utils


// block thickbox
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.thickbox = bulkmail.thickbox || {};

	bulkmail.thickbox.$ = {};
	bulkmail.thickbox.$.preview = $('.bulkmail-preview-iframe');

	bulkmail.thickbox.$.preview
		.on('load', function () {
			var $this = $(this),
				contents = $this.contents(),
				body = contents.find('body');

			body.on('click', 'a', function () {
				var href = $(this).attr('href');
				if (href && href != '#') window.open(href);
				return false;
			});
		});

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end thickbox



// block modules
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	var metabox = $('#bulkmail_template'),
		selector = $('#module-selector'),
		search = $('#module-search'),
		module_thumbs = selector.find('li'),
		toggle = $('a.toggle-modules');

	bulkmail.modules = bulkmail.modules || {};
	bulkmail.modules.showSelector = !!parseInt(window.getUserSetting('bulkmailshowmodules', 1), 10);
	bulkmail.modules.dragging = false
	bulkmail.modules.selected = false;

	function colorSwap(html) {
		for (var c_from in bulkmail.editor.colors.map) {
			html = bulkmail.util.replace(html, c_from, bulkmail.editor.colors.map[c_from]);
		}
		return html;
	}

	function addmodule() {
		var module = selector.data('current'),
			html = $(this).parent().find('script').html();
		insert(colorSwap(html), (module && module.is('module') ? module : false), true, true);
	}

	function up() {
		var module = $(this).parent().parent().parent().addClass('ui-sortable-fly-over'),
			prev = module.prev('module').addClass('ui-sortable-fly-under'),
			pos = bulkmail.util.top() - prev.height();

		module.css({
			'transform': 'translateY(-' + prev.height() + 'px)'
		});
		prev.css({
			'transform': 'translateY(' + module.height() + 'px)'
		});

		bulkmail.util.scroll(pos, function () {
			module.insertBefore(prev.css({
				'transform': ''
			}).removeClass('ui-sortable-fly-under')).css({
				'transform': ''
			}).removeClass('ui-sortable-fly-over');
			bulkmail.trigger('refresh');
			bulkmail.trigger('save');
		}, 250);
	}

	function down() {
		var module = $(this).parent().parent().parent().addClass('ui-sortable-fly-over'),
			next = module.next('module').addClass('ui-sortable-fly-under'),
			pos = bulkmail.util.top() + next.height();
		module.css({
			'transform': 'translateY(' + next.height() + 'px)'
		});
		next.css({
			'transform': 'translateY(-' + module.height() + 'px)'
		});
		bulkmail.util.scroll(pos, function () {
			module.insertAfter(next.css({
				'transform': ''
			}).removeClass('ui-sortable-fly-under')).css({
				'transform': ''
			}).removeClass('ui-sortable-fly-over');
			bulkmail.trigger('refresh');
			bulkmail.trigger('save');
		}, 250);
	}

	function duplicate() {
		var module = $(this).parent().parent().parent(),
			clone = module.clone().removeAttr('selected').hide();

		insert(clone, module, false, true);
	}

	function auto() {
		var module = $(this).parent().parent().parent();
		bulkmail.editbar.open({
			element: module,
			name: module.attr('label'),
			type: 'auto',
			offset: module.offset()
		});
	}

	function changeName() {
		var _this = $(this),
			value = _this.val(),
			module = _this.parent().parent();

		if (!value) {
			value = _this.attr('placeholder');
			_this.val(value);
		}

		module.attr('label', value);
	}

	function remove() {
		var module = $(this).parent().parent().parent();
		module.fadeTo(25, 0, function () {
			module.slideUp(100, function () {
				module.remove();
				bulkmail.trigger('refresh');
				if (!bulkmail.editor.$.modules.length) bulkmail.editor.$.container.html('');
				bulkmail.trigger('save');
			});
		});
	}

	function insert(html_or_clone, element, before, scroll) {

		var clone;

		if (typeof html_or_clone == 'string') {
			clone = $(html_or_clone);
		} else if (html_or_clone instanceof jQuery) {
			clone = $(html_or_clone);
			clone.find('single, multi, buttons').removeAttr('contenteditable spellcheck id dir style class');
		} else {
			return false;
		}

		if (!element && !bulkmail.editor.$.container.length) return false;

		if (element) {
			(before ? clone.hide().insertBefore(element) : clone.hide().insertAfter(element))
		} else {
			if ('footer' == bulkmail.editor.$.modules.last().attr('type')) {
				clone.hide().insertBefore(bulkmail.editor.$.modules.last());
			} else {
				clone.hide().appendTo(bulkmail.editor.$.container);
			}
		}

		bulkmail.editor.updateElements();
		bulkmail.editor.moduleButtons();

		clone.slideDown(100, function () {
			clone.css('display', 'block');
			bulkmail.trigger('refresh');
			bulkmail.trigger('save');
		});

		if (scroll) {
			var offset = clone.offset().top + bulkmail.$.template.offset().top - (bulkmail.$.window.height() / 2);
			bulkmail.util.scroll(offset);
		}

	}

	function codeView() {
		var module = $(this).parent().parent().parent();
		bulkmail.editbar.open({
			element: module,
			name: module.attr('label'),
			type: 'codeview',
			offset: module.offset()
		});
	}

	function toggleModules() {
		bulkmail.$.templateWrap.toggleClass('show-modules');
		bulkmail.modules.showSelector = !bulkmail.modules.showSelector;
		window.setUserSetting('bulkmailshowmodules', bulkmail.modules.showSelector ? 1 : 0);
		setTimeout(function () {
			bulkmail.trigger('resize');
		}, 200);
	}

	function searchModules() {
		module_thumbs.hide();
		selector.find("li:contains('" + $(this).val() + "')").show();
	}

	function initFrame() {

		var currentmodule,
			pre_dropzone = $('<dropzone></dropzone>'),
			post_dropzone = pre_dropzone.clone(),
			dropzones = pre_dropzone.add(post_dropzone);

		bulkmail.editor.$.document
			.off('.bulkmail')
			.on('click.bulkmail', 'button.up', up)
			.on('click.bulkmail', 'button.down', down)
			.on('click.bulkmail', 'button.auto', auto)
			.on('click.bulkmail', 'button.duplicate', duplicate)
			.on('click.bulkmail', 'button.remove', remove)
			.on('click.bulkmail', 'button.codeview', codeView)
			.on('change.bulkmail', 'input.modulelabel', changeName);

		selector
			.off('.bulkmail')
			.on('dragstart.bulkmail', 'li', function (startevent) {

				//required for Firefox
				startevent.originalEvent.dataTransfer.setData('Text', this.id);

				bulkmail.modules.dragging = true;

				bulkmail.editor.$.body.addClass('drag-active');

				bulkmail.editor.$.container
					.on('dragenter.bulkmail', function (event) {
						var selectedmodule = $(event.target).closest('module');
						if (!selectedmodule.length || currentmodule && currentmodule[0] === selectedmodule[0]) return;
						currentmodule = selectedmodule;
						post_dropzone.appendTo(currentmodule);
						pre_dropzone.prependTo(currentmodule);
						setTimeout(function () {
							post_dropzone.addClass('visible');
							pre_dropzone.addClass('visible');
							bulkmail.editor.$.modules.removeClass('drag-up drag-down');
							selectedmodule.prevAll('module').addClass('drag-up');
							selectedmodule.nextAll('module').addClass('drag-down')
						}, 1);
					})
					.on('dragover.bulkmail', function (event) {
						event.preventDefault();
					})
					.on('drop.bulkmail', function (event) {
						var html = $(startevent.target).find('script').html();
						insert(colorSwap(html), bulkmail.editor.$.modules.length ? (currentmodule && currentmodule[0] === bulkmail.editor.$.container ? false : currentmodule) : false, pre_dropzone[0] === event.target, false, true);
						event.preventDefault();
					});

				dropzones
					.on('dragenter.bulkmail', function (event) {
						$(this).addClass('drag-over');
					})
					.on('dragleave.bulkmail', function (event) {
						$(this).removeClass('drag-over');
					});

			})
			.on('dragend.bulkmail', 'li', function (event) {
				currentmodule = null;
				bulkmail.editor.$.body.removeClass('drag-active');
				dropzones.removeClass('visible drag-over').remove();
				bulkmail.editor.$.modules.removeClass('drag-up drag-down');

				bulkmail.editor.$.container
					.off('dragenter.bulkmail')
					.off('dragover.bulkmail')
					.off('drop.bulkmail');

				dropzones
					.off('dragenter.bulkmail')
					.off('dragleave.bulkmail');

				bulkmail.modules.dragging = false;

			});
	}

	$('.meta-box-sortables').on("sortstop", function (event, ui) {
		if (ui.item[0] === bulkmail.dom.template) {
			bulkmail.editor.$.body.addClass('reload-page');
		}
	});

	bulkmail.$.template
		.on('click', 'a.toggle-modules', toggleModules)
		.on('keydown', 'a.addmodule', function (event) {
			if (13 == event.which) {
				addmodule.call(this);
			}
		})
		.on('click', 'a.addmodule', addmodule)
		.on('click', '#module-search-remove', function () {
			search.val('').focus().trigger('keyup');
			return false;
		});

	search
		.on('keyup', searchModules)
		.on('focus', function () {
			search.select();
		});


	bulkmail.events.push('editorLoaded', initFrame);

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end modules


// block Details
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	var googledata = {
		unknown_cities: [],
		geodata: null,
		map: null,
		options: {
			legend: false,
			region: 'world',
			resolution: 'countries',
			datalessRegionColor: '#ffffff',
			enableRegionInteractivity: true,
			colors: ['#d7f1fc', bulkmail.colors.main],
			backgroundColor: {
				fill: 'none',
				stroke: null,
				strokeWidth: 0
			},
		}

	};

	bulkmail.$.details = $('#bulkmail_details .inside');

	bulkmail.details = bulkmail.details || {};

	bulkmail.details.$ = {};
	bulkmail.details.$.subject = $('#bulkmail_subject');
	bulkmail.details.$.preheader = $('#bulkmail_preheader');
	bulkmail.details.$.from = $('#bulkmail_from');
	bulkmail.details.$.from_name = $('#bulkmail_from-name');
	bulkmail.details.$.replyto = $('#bulkmail_reply-to');

	bulkmail.$.title.on('change', function () {
		if (!bulkmail.details.$.subject.val()) bulkmail.details.$.subject.val($(this).val());
	});

	bulkmail.$.details
		.on('click', '.default-value', function () {
			var _this = $(this);
			$('#' + _this.data('for')).val(_this.data('value'));
		})
		.on('click', '#show_recipients', function () {
			var $this = $(this),
				list = $('#recipients-list'),
				loader = $('#recipients-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			bulkmail.util.ajax('get_recipients', {
				id: bulkmail.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_clicks', function () {
			var $this = $(this),
				list = $('#clicks-list'),
				loader = $('#clicks-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			bulkmail.util.ajax('get_clicks', {
				id: bulkmail.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_environment', function () {
			var $this = $(this),
				list = $('#environment-list'),
				loader = $('#environment-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			bulkmail.util.ajax('get_environment', {
				id: bulkmail.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_geolocation', function () {
			var $this = $(this),
				list = $('#geolocation-list'),
				loader = $('#geolocation-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			bulkmail.util.ajax('get_geolocation', {
				id: bulkmail.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();

				googledata.geodata = response.geodata;
				googledata.unknown_cities = response.unknown_cities;

				list.html(response.html).slideDown(100, function () {

					google.load('visualization', '1.0', {
						packages: ['geochart', 'corechart'],
						mapsApiKey: bulkmail.l10n.google ? bulkmail.l10n.google.key : null,
						callback: function () {
							var hash;

							googledata.map = new google.visualization.GeoChart(document.getElementById('countries_map'));
							google.countrydata = google.visualization.arrayToDataTable(response.countrydata);

							if (location.hash && (hash = location.hash.match(/region=([A-Z]{2})/))) {
								regionClick(hash[1]);
							} else {
								drawMap(google.countrydata);
							}

							google.visualization.events.addListener(googledata.map, 'regionClick', regionClick);

						}
					});

					$('a.zoomout').on('click', function () {
						showWorld();
						return false;
					});

					$('#countries_table').find('tbody').find('tr').on('click', function () {
						var code = $(this).data('code');
						(code == 'unknown' || !code) ?
						showWorld(): regionClick(code);

						return false;
					});

				});

			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_errors', function () {
			var $this = $(this),
				list = $('#error-list'),
				loader = $('#error-ajax-loading');

			if (!list.is(':hidden')) {
				$this.removeClass('open');
				list.slideUp(100);
				return false;
			}
			loader.css('display', 'inline');

			bulkmail.util.ajax('get_errors', {
				id: bulkmail.campaign_id
			}, function (response) {
				$this.addClass('open');
				loader.hide();
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', '#show_countries', function () {
			$('#countries_wrap').toggle();
			return false;
		})
		.on('click', '.load-more-receivers', function () {
			var $this = $(this),
				page = $this.data('page'),
				types = $this.data('types'),
				orderby = $this.data('orderby'),
				order = $this.data('order'),
				loader = $this.next().css('display', 'inline');

			bulkmail.util.ajax('get_recipients_page', {
				id: bulkmail.campaign_id,
				types: types,
				page: page,
				orderby: orderby,
				order: order
			}, function (response) {
				loader.hide();
				if (response.success) {
					$this.parent().parent().replaceWith(response.html);
				}
			}, function (jqXHR, textStatus, errorThrown) {
				detailbox.removeClass('loading');
			});

			return false;
		})
		.on('click', '.recipients-limit', function (event) {
			if (event.altKey) {
				$('input.recipients-limit').prop('checked', false);
				$(this).prop('checked', true);
			}
		})
		.on('change', '.recipients-limit, select.recipients-order', function (event) {

			var list = $('#recipients-list'),
				loader = $('#recipients-ajax-loading'),
				types = $('input.recipients-limit:checked').map(function () {
					return this.value
				}).get(),
				orderby = $('select.recipients-order').val(),
				order = $('a.recipients-order').hasClass('asc') ? 'ASC' : 'DESC';

			loader.css('display', 'inline');
			$('input.recipients-limit').prop('disabled', true);

			bulkmail.util.ajax('get_recipients', {
				id: bulkmail.campaign_id,
				types: types.join(','),
				orderby: orderby,
				order: order
			}, function (response) {
				loader.hide();
				$('input.recipients-limit').prop('disabled', false);
				list.html(response.html).slideDown(100);
			}, function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			})
			return false;
		})
		.on('click', 'a.recipients-order', function () {
			$(this).toggleClass('asc');
			$('select.recipients-order').trigger('change');
		})
		.on('click', '.show-receiver-detail', function () {
			var $this = $(this),
				id = $this.data('id'),
				detailbox = $('#receiver-detail-' + id).show();

			$this.parent().addClass('loading').parent().addClass('expanded');

			bulkmail.util.ajax('get_recipient_detail', {
				id: id,
				campaignid: bulkmail.campaign_id
			}, function (response) {
				$this.parent().removeClass('loading');
				if (response.success) {
					detailbox.find('div.receiver-detail-body').html(response.html).slideDown(100);
				}
			}, function (jqXHR, textStatus, errorThrown) {
				detailbox.removeClass('loading');
			});

			return false;
		})
		.on('click', '#stats label', function () {
			$('#recipients-list')
				.find('input').prop('checked', false)
				.filter('input.' + $(this).attr('class')).prop('checked', true)
				.trigger('change');
		});

	$.easyPieChart && bulkmail.$.details.find('.piechart').easyPieChart({
		animate: 2000,
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

	function showWorld() {
		var options = {
			'region': 'world',
			'displayMode': 'region',
			'resolution': 'countries',
			'colors': ['#D7E4E9', bulkmail.colors.main]
		};

		drawMap(google.countrydata, options);

		$('#countries_table').find('tr').removeClass('wp-ui-highlight');
		$('#mapinfo').hide();

		location.hash = '#region=';

	}

	function regionClick(event) {

		var options = {},
			region = event.region ? event.region : event,
			d, data;

		if (region.match(/-/)) return false;

		options['region'] = region;

		googledata.unknown_cities[region] ?
			$('#mapinfo').show().html(bulkmail.util.sprintf(bulkmail.l10n.campaigns.unknown_locations, googledata.unknown_cities[region])) :
			$('#mapinfo').hide();

		d = googledata.geodata[region] ? googledata.geodata[region] : [];

		options['resolution'] = 'provinces';
		options['displayMode'] = 'markers';
		options['dataMode'] = 'markers';
		options['colors'] = ['#4EBEE9', bulkmail.colors.main];

		data = new google.visualization.DataTable()
		data.addColumn('number', 'Lat');
		data.addColumn('number', 'Long');
		data.addColumn('string', 'tooltip');
		data.addColumn('number', 'Value');
		data.addColumn({
			type: 'string',
			role: 'tooltip'
		});

		data.addRows(d);

		$('#countries_table').find('tr').removeClass('wp-ui-highlight');
		$('#country-row-' + region).addClass('wp-ui-highlight');

		location.hash = '#region=' + region
		drawMap(data, options);

	}

	function drawMap(data, options) {
		options = $.extend(googledata.options, options);
		googledata.map.draw(data, options);
		$('a.zoomout').css({
			'visibility': (options['region'] != 'world' ? 'visible' : 'hidden')
		});
	}

	function regTo3dig(region) {
		var regioncode = region;
		$.each(regions, function (code, regions) {
			if ($.inArray(region, regions) != -1) regioncode = code;
		})
		return regioncode;
	}

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end Details


// block Template
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.clickmap = bulkmail.clickmap || {};

	bulkmail.clickmap.$ = {};
	bulkmail.clickmap.$.popup = $('#clickmap-stats');

	bulkmail.$.template
		.on('click', 'a.getplaintext', function () {
			var oldval = bulkmail.$.excerpt.val();
			bulkmail.$.excerpt.val(bulkmail.l10n.campaigns.loading);
			bulkmail.util.ajax('get_plaintext', {
				html: bulkmail.editor.getContent()
			}, function (response) {
				bulkmail.$.excerpt.val(response);
			}, function (jqXHR, textStatus, errorThrown) {
				bulkmail.$.excerpt.val(oldval);
			}, 'HTML');
		})
		.on('change', '#plaintext', function () {
			var checked = $(this).is(':checked');
			bulkmail.$.excerpt.prop('disabled', checked)[checked ? 'addClass' : 'removeClass']('disabled');
		})
		.on('mouseenter', 'a.clickbadge', function () {
			var _this = $(this),
				_position = _this.position(),
				p = _this.data('p'),
				link = _this.data('link'),
				clicks = _this.data('clicks'),
				total = _this.data('total');

			bulkmail.clickmap.$.popup.find('.piechart').data('easyPieChart').update(p);
			bulkmail.clickmap.$.popup.find('.link').html(link);
			bulkmail.clickmap.$.popup.find('.clicks').html(clicks);
			bulkmail.clickmap.$.popup.find('.total').html(total);
			bulkmail.clickmap.$.popup.stop().fadeIn(100).css({
				top: _position.top - 85,
				left: _position.left - (bulkmail.clickmap.$.popup.width() / 2 - _this.width() / 2)
			});

		})
		.on('mouseleave', 'a.clickbadge', function () {
			bulkmail.clickmap.$.popup.stop().fadeOut(400);
		});

	bulkmail.clickmap.updateBadges = function (stats) {
		bulkmail.$.templateWrap.find('.clickbadge').remove();
		var stats = stats || $('#bulkmail_click_stats').data('stats'),
			total = parseInt(stats.total, 10);

		if (!total) return;

		$.each(stats.clicks, function (href, countobjects) {

			$.each(countobjects, function (index, counts) {

				var link = bulkmail.$.iframe.contents().find('a[href="' + href.replace('&amp;', '&') + '"]').eq(index);

				if (link.length) {
					link.css('display', 'inline-block');

					var offset = link.offset(),
						top = offset.top,
						left = offset.left + 5,
						percentage = (counts.clicks / total) * 100,
						v = (percentage < 1 ? '&lsaquo;1' : Math.round(percentage)) + '%',
						badge = $('<a class="clickbadge ' + (percentage < 40 ? 'clickbadge-outside' : '') + '" data-p="' + percentage + '" data-link="' + href + '" data-clicks="' + counts.clicks + '" data-total="' + counts.total + '"><span style="width:' + (Math.max(0, percentage - 2)) + '%">' + (percentage < 40 ? '&nbsp;' : v) + '</span>' + (percentage < 40 ? ' ' + v : '') + '</a>')
						.css({
							top: top,
							left: left
						}).appendTo(bulkmail.$.templateWrap);

				}

			});
		});
	}

	bulkmail.editable && window.EmojiButton && bulkmail.events.push('documentReady', function () {
		$('.emoji-selector')
			.on('click', 'button', function () {
				var input = document.querySelector('#' + $(this).data('input')),
					picker = new EmojiButton({
						emojiVersion: '3.0',
						showVariants: false,
						zIndex: 1000,
					});

				picker.togglePicker(this);
				picker.on('emoji', function (emoji) {
					var caretPos = input.selectionStart;
					input.value = input.value.substring(0, caretPos) + emoji + input.value.substring(caretPos);
					setTimeout(function () {
						input.focus();
						input.setSelectionRange(caretPos + 1, caretPos + 1);
					}, 10);
				});
				return false;
			});
	});

	!bulkmail.editable && bulkmail.events.push('documentReady', function () {
		$.easyPieChart && bulkmail.clickmap.$.popup.find('.piechart').easyPieChart({
			animate: 2000,
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
	})

	!bulkmail.editable && bulkmail.events.push('iframeLoaded', function () {
		bulkmail.$.iframe.height(Math.max(500, bulkmail.dom.iframe.contentWindow.document.body.scrollHeight));
		bulkmail.clickmap.updateBadges();
		bulkmail.$.iframecontents && bulkmail.$.iframecontents.on('click', 'a', function () {
			window.open(this.href);
			return false;
		});

	});

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end Template


// block Submit
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.$.submit = $('#bulkmail_submitdiv .inside');

	bulkmail.submit = bulkmail.submit || {};

	bulkmail.$.submit
		.on('change', '#use_pwd', function () {
			$('#password-wrap').slideToggle(200).find('input').focus().select();
			$('#post_password').prop('disabled', !$(this).is(':checked'));
		})
		.on('click', '.sendnow-button', function () {
			if (!confirm(bulkmail.l10n.campaigns.send_now)) return false;
		});

	bulkmail.submit.$ = {};

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end Submit


// block Delivery
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.$.delivery = $('#bulkmail_delivery .inside');

	bulkmail.delivery = bulkmail.delivery || {};
	bulkmail.delivery.$ = {};

	bulkmail.$.delivery
		.on('change', 'input.timezone', function () {
			$('.active_wrap').toggleClass('timezone-enabled');
		})
		.on('change', 'input.autoresponder-timezone', function () {
			$('.autoresponderfield-bulkmail_autoresponder_timebased').toggleClass('timezone-enabled');
		})
		.on('change', 'input.userexactdate', function () {
			$(this).parent().parent().parent().find('span').addClass('disabled');
			$(this).parent().find('span').removeClass('disabled');
		})
		.on('change', '#autoresponder-post_type', function () {
			var cats = $('#autoresponder-taxonomies');
			cats.find('select').prop('disabled', true);
			bulkmail.util.ajax('get_post_term_dropdown', {
				labels: false,
				names: true,
				posttype: $(this).val()
			}, function (response) {
				if (response.success) {
					cats.html(response.html);
				}
			}, function (jqXHR, textStatus, errorThrown) {

				loader(false);

			});
		})
		.on('click', '.category-tabs a', function () {
			var _this = $(this),
				href = _this.attr('href');

			bulkmail.$.delivery.find('.tabs-panel').hide();
			bulkmail.$.delivery.find('.tabs').removeClass('tabs');
			_this.parent().addClass('tabs');
			$(href).show();
			$('#bulkmail_is_autoresponder').val((href == '#autoresponder') ? 1 : '');
			return false;
		})
		.on('click', '.bulkmail_sendtest', function () {
			var $this = $(this);

			loader(true);
			$this.prop('disabled', true);
			bulkmail.trigger('save');

			bulkmail.util.ajax('send_test', {
				formdata: bulkmail.$.form.serialize(),
				to: $('#bulkmail_testmail').val() ? $('#bulkmail_testmail').val() : $('#bulkmail_testmail').attr('placeholder'),
				content: bulkmail.$.content.val(),
				head: bulkmail.$.head.val(),
				plaintext: bulkmail.$.excerpt.val()

			}, function (response) {

				loader(false);
				$this.prop('disabled', false);
				bulkmail.util.tempMsg(response.msg, (!response.success ? 'error' : 'updated'), $this.parent());
			}, function (jqXHR, textStatus, errorThrown) {

				loader(false);
				$this.prop('disabled', false);
				bulkmail.util.tempMsg(rtextStatus + ' ' + jqXHR.status + ': ' + errorThrown, 'error', $this.parent());

			})
		})
		.on('change', '#bulkmail_data_active', function () {
			$(this).is(':checked') ?
				$('.active_wrap').addClass('disabled') :
				$('.active_wrap').removeClass('disabled');

			$('.deliverydate, .deliverytime').prop('disabled', !$(this).is(':checked'));

		})
		.on('change', '#bulkmail_data_autoresponder_active', function () {
			$(this).is(':checked') ?
				$('.autoresponder_active_wrap').addClass('disabled') :
				$('.autoresponder_active_wrap').removeClass('disabled');

		})
		.on('click', '.bulkmail_spamscore', function () {
			var $this = $(this),
				progress = $('#spam_score_progress').removeClass('spam-score').slideDown(200),
				progressbar = progress.find('.bar');

			loader(true);
			$this.prop('disabled', true);
			$('.score').html('');
			bulkmail.trigger('save');
			progressbar.css('width', '20%');

			bulkmail.util.ajax('send_test', {
				spamtest: true,
				formdata: bulkmail.$.form.serialize(),
				to: $('#bulkmail_testmail').val() ? $('#bulkmail_testmail').val() : $('#bulkmail_testmail').attr('placeholder'),
				content: bulkmail.$.content.val(),
				head: bulkmail.$.head.val(),
				plaintext: bulkmail.$.excerpt.val()

			}, function (response) {

				if (response.success) {
					progressbar.css('width', '40%');
					checkSpamScore(response.id, 1);
				} else {
					loader(false);
					progress.slideUp(200);
					bulkmail.util.tempMsg(response.msg, 'error', $this.parent());
				}
			}, function (jqXHR, textStatus, errorThrown) {
				loader(false);
				$this.prop('disabled', false);
				bulkmail.util.tempMsg(rtextStatus + ' ' + jqXHR.status + ': ' + errorThrown, 'error', $this.parent());

			})

		})
		.on('blur', 'input.deliverytime', function () {
			bulkmail.$.document.unbind('.bulkmail_deliverytime');
		})
		.on('focus, click', 'input.deliverytime', function (event) {
			var $this = $(this),
				input = $(this)[0],
				l = $this.offset().left,
				c = 0,
				startPos = 0,
				endPos = 2;

			if (event.clientX - l > 23) {
				c = 1,
					startPos = 3,
					endPos = 5;
			}
			bulkmail.$.document.unbind('.bulkmail_deliverytime')
				.on('keypress.bulkmail_deliverytime', function (event) {
					if (event.keyCode == 9) {
						return (c = !c) ? !bulkmail.util.selectRange(input, 3, 5) : (event.shiftKey) ? !bulkmail.util.selectRange(input, 0, 2) : true;
					}
				})
				.on('keyup.bulkmail_deliverytime', function (event) {
					if ($this.val().length == 1) {
						$this.val($this.val() + ':00');
						bulkmail.util.selectRange(input, 1, 1);
					}
					if (document.activeElement.selectionStart == 2) {
						if ($this.val().substr(0, 2) > 23) {
							$this.trigger('change');
							return false;
						}
						bulkmail.util.selectRange(input, 3, 5);
					}
				});
			bulkmail.util.selectRange(input, startPos, endPos);

		})
		.on('change', 'input.deliverytime', function () {
			var $this = $(this),
				val = $this.val(),
				time;
			$this.addClass('inactive');
			if (!/^\d+:\d+$/.test(val)) {

				if (val.length == 1) {
					val = "0" + val + ":00";
				} else if (val.length == 2) {
					val = val + ":00";
				} else if (val.length == 3) {
					val = val.substr(0, 2) + ":" + val.substr(2, 3) + "0";
				} else if (val.length == 4) {
					val = val.substr(0, 2) + ":" + val.substr(2, 4);
				}
			}
			time = val.split(':');

			if (!/\d\d:\d\d$/.test(val) && val != "" || time[0] > 23 || time[1] > 59) {
				$this.val('00:00').focus();
				bulkmail.util.selectRange($this[0], 0, 2);
			} else {
				$this.val(val);
			}
		})
		.on('change', '#bulkmail_autoresponder_action', function () {
			$('#autoresponder_wrap').removeAttr('class').addClass('autoresponder-' + $(this).val());
		})
		.on('change', '#time_extra', function () {
			$('#autoresponderfield-bulkmail_timebased_advanced').slideToggle();
		})
		.on('click', '.bulkmail_autoresponder_timebased-end-schedule', function () {
			$(this).is(':checked') ?
				$('.bulkmail_autoresponder_timebased-end-schedule-field').slideDown() :
				$('.bulkmail_autoresponder_timebased-end-schedule-field').slideUp();
		})
		.on('change', '.bulkmail-action-hooks', function () {
			var val = $(this).val();
			$('.bulkmail-action-hook').val(val);
			if (!val) {
				$('.bulkmail-action-hook').focus();
			}
		})
		.on('change', '.bulkmail-action-hook', function () {
			var val = $(this).val();
			if (!$(".bulkmail-action-hooks option[value='" + val + "']").length) {
				$('.bulkmail-action-hooks').append('<option>' + val + '</option>');
			}
			$('.bulkmail-action-hooks').val(val);
		})
		.on('click', '.bulkmail-total', function () {
			bulkmail.trigger('updateCount');
		})
		.on('change', '#list_extra', function () {
			if ($(this).is(':checked')) {
				$('#bulkmail_list_advanced').slideDown();
			} else {
				$('#bulkmail_list_advanced').slideUp();
			}
			$('#list-checkboxes').find('input.list').eq(0).trigger('change');
		})
		.on('focus', 'input.datepicker', function () {
			$(this).removeClass('inactive').trigger('click');
		})
		.on('blur', 'input.datepicker', function () {
			$('.deliverydate').html($(this).val());
			$(this).addClass('inactive');
		});

	$.datepicker && bulkmail.$.delivery
		.find('input.datepicker').datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: new Date(),
			firstDay: bulkmail.l10n.campaigns.start_of_week,
			showWeek: true,
			dayNames: bulkmail.l10n.campaigns.day_names,
			dayNamesMin: bulkmail.l10n.campaigns.day_names_min,
			monthNames: bulkmail.l10n.campaigns.month_names,
			prevText: bulkmail.l10n.campaigns.prev,
			nextText: bulkmail.l10n.campaigns.next,
			showAnim: 'fadeIn',
			onClose: function () {
				var date = $(this).datepicker('getDate');
				$('.deliverydate').html($(this).val());
			}
		});

	$.datepicker && $('input.datepicker.nolimit').datepicker("option", "minDate", null);

	bulkmail.$.delivery.find('input.datepicker').not('.hasDatepicker').prop('readonly', false);

	bulkmail.events.push('documentReady', function () {
		//switch to autoresponder if referer is right or post_status is set
		if (/post_status=autoresponder/.test($('#referredby').val()) || /post_status=autoresponder/.test(location.search)) {
			bulkmail.$.delivery.find('a[href="#autoresponder"]').click();
		}

	});

	(function () {

		var t, x, h, m, l, usertime = new Date(),
			elements = $('.time'),
			deliverytime = $('.deliverytime').eq(0),
			activecheck = $('#bulkmail_data_active'),
			servertime = parseInt(elements.data('timestamp'), 10) * 1000,
			seconds = false,
			offset = servertime - usertime.getTime() + (usertime.getTimezoneOffset() * 60000);

		var delay = (seconds) ? 1000 : 20000;

		function set() {
			t = new Date();

			usertime = t.getTime();
			t.setTime(usertime + offset);
			h = t.getHours();
			m = t.getMinutes();

			if (bulkmail.enabled && x && m != x[1] && !activecheck.is(':checked')) {
				deliverytime.val(zero(h) + ':' + zero(m));
			}
			x = [];
			x.push(t.getHours());
			x.push(t.getMinutes());
			if (seconds) x.push(t.getSeconds());
			l = x.length;
			for (var i = 0; i < l; i++) {
				x[i] = zero(x[i]);
			};
			elements.html(x.join('<span class="blink">:</span>'));
			setTimeout(function () {
				set();
			}, delay);
		}

		function zero(value) {
			if (value < 10) {
				value = '0' + value;
			}
			return value;
		}

		set();

	})();


	function loader(show) {
		if (null == show || true === show) {
			$('#delivery-ajax-loading').css('display', 'inline');
		} else {
			$('#delivery-ajax-loading').hide();
		}
	}

	function checkSpamScore(id, round) {

		var $button = $('.bulkmail_spamscore'),
			progress = $('#spam_score_progress'),
			progressbar = progress.find('.bar');

		bulkmail.util.ajax('check_spam_score', {
			ID: id,
		}, function (response) {

			if (response.score) {
				loader(false);
				$button.prop('disabled', false);
				progress.addClass('spam-score');
				progressbar.css('width', (parseFloat(response.score) * 10) + '%');

				$('.score').html('<strong>' + bulkmail.util.sprintf(bulkmail.l10n.campaigns.yourscore, response.score) + '</strong>:<br>' + bulkmail.l10n.campaigns.yourscores[Math.floor((response.score / 10) * bulkmail.l10n.campaigns.yourscores.length)]);
			} else {

				if (round <= 5 && !response.abort) {
					var percentage = (round * 10) + 50;
					progressbar.css('width', (percentage) + '%');
					setTimeout(function () {
						checkSpamScore(id, ++round);
					}, round * 400);
				} else {

					loader(false);
					$button.prop('disabled', false);
					progressbar.css('width', '100%');
					progress.slideUp(200);
					bulkmail.util.tempMsg(response.msg, 'error', $button.parent(), function () {
						progressbar.css('width', 0);
					});

				}

			}
		}, function (jqXHR, textStatus, errorThrown) {
			loader(false);
			$this.prop('disabled', false);
			bulkmail.util.tempMsg(rtextStatus + ' ' + jqXHR.status + ': ' + errorThrown, 'error', $this.parent());
			var msg = $('<div class="error"><p>' + textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '</p></div>').hide().prependTo($this.parent()).slideDown(200).delay(200).fadeIn().delay(3000).fadeTo(200, 0).delay(200).slideUp(200, function () {
				msg.remove();
			});
		})
	}
	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end Delivery


// block Receivers
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	var updateCountTimout;

	bulkmail.$.receivers = $('#bulkmail_receivers .inside');

	bulkmail.receivers = bulkmail.receivers || {};

	bulkmail.receivers.$ = {};
	bulkmail.receivers.$.conditions = $('.bulkmail-conditions-thickbox');
	bulkmail.receivers.$.conditionsOutput = $('#bulkmail_conditions_render');
	bulkmail.receivers.$.total = $('.bulkmail-total');

	bulkmail.$.receivers
		.on('change', 'input.list', function () {
			bulkmail.trigger('updateCount');
		})
		.on('change', '#all_lists', function () {
			$('#list-checkboxes').find('input.list').prop('checked', $(this).is(':checked'));
			bulkmail.trigger('updateCount');
		})
		.on('change', '#ignore_lists', function () {
			var checked = $(this).is(':checked');
			$('#list-checkboxes').each(function () {
				(checked) ?
				$(this).slideUp(200): $(this).slideDown(200);
			});
			bulkmail.trigger('updateCount');
		})
		.on('click', '.edit-conditions', function () {
			tb_show(bulkmail.l10n.campaigns.edit_conditions, '#TB_inline?x=1&width=720&height=520&inlineId=receivers-dialog', null);
			return false;
		})
		.on('click', '.remove-conditions', function () {
			if (confirm(bulkmail.l10n.campaigns.remove_conditions)) {
				$('#receivers-dialog').find('.bulkmail-conditions-wrap').empty();
				bulkmail.trigger('updateCount');
			}
			return false;
		})
		.on('click', '.bulkmail-total', function () {
			bulkmail.trigger('updateCount');
		})
		.on('click', '.create-new-list', function () {
			var $this = $(this).hide();
			$('.create-new-list-wrap').slideDown();
			$('.create-list-type').trigger('change');
			return false;
		})
		.on('click', '.create-list', function () {
			var $this = $(this),
				listtype = $('.create-list-type'),
				name = '';
			if (listtype.val() == -1) return false;

			if (name = prompt(bulkmail.l10n.campaigns.enter_list_name, bulkmail.util.sprintf(bulkmail.l10n.campaigns.create_list, listtype.find(':selected').text(), $('#title').val()))) {

				loader(true);

				bulkmail.util.ajax('create_list', {
					name: name,
					listtype: listtype.val(),
					id: bulkmail.campaign_id
				}, function (response) {
					loader(false);
					bulkmail.util.tempMsg(response.msg, (!response.success ? 'error' : 'updated'), $('.create-new-list-wrap'));
				}, function (jqXHR, textStatus, errorThrown) {
					loader(false);
					bulkmail.util.tempMsg(rtextStatus + ' ' + jqXHR.status + ': ' + errorThrown, 'error', $('.create-new-list-wrap'));
				});
			}

			return false;
		})
		.on('change', '.create-list-type', function () {
			var listtype = $(this);

			if (listtype.val() == -1) return false;
			listtype.prop('disabled', true);

			loader(true);

			bulkmail.util.ajax('get_create_list_count', {
				listtype: listtype.val(),
				id: bulkmail.campaign_id
			}, function (response) {
				listtype.prop('disabled', false);
				loader(false, response.count);

			}, function (jqXHR, textStatus, errorThrown) {
				listtype.prop('disabled', false);
				loader(false, '');
			});
		})
		.on('click', '.bulkmail-total', function () {
			$('.create-list-type').trigger('change');
		});


	bulkmail.receivers.$.conditions
		.on('click', '.close-conditions', tb_remove);


	bulkmail.editable && bulkmail.events.push('documentReady', function () {
		bulkmail.trigger('updateCount');
	})

	bulkmail.editable && bulkmail.events.push('updateCount', function () {

		clearTimeout(updateCountTimout);
		updateCountTimout = setTimeout(function () {
			var lists = [],
				conditions = [],
				inputs = $('#list-checkboxes').find('input, select'),
				listinputs = $('#list-checkboxes').find('input.list'),
				extra = $('#list_extra'),
				data = {},
				groups = $('.bulkmail-conditions-wrap > .bulkmail-condition-group'),
				i = 0;

			$.each(listinputs, function () {
				var id = $(this).val();
				if ($(this).is(':checked')) lists.push(id);
			});

			data.id = bulkmail.campaign_id;
			data.lists = lists;
			data.ignore_lists = $('#ignore_lists').is(':checked');

			$.each(groups, function () {
				var c = $(this).find('.bulkmail-condition');
				$.each(c, function () {
					var _this = $(this),
						value,
						field = _this.find('.condition-field').val(),
						operator = _this.find('.bulkmail-conditions-operator-field.active').find('.condition-operator').val();

					if (!operator || !field) return;

					value = _this.find('.bulkmail-conditions-value-field.active').find('.condition-value').map(function () {
						return $(this).val();
					}).toArray();
					if (value.length == 1) {
						value = value[0];
					}
					if (!conditions[i]) {
						conditions[i] = [];
					}

					conditions[i].push({
						field: field,
						operator: operator,
						value: value,
					});
				});
				i++;
			});

			data.operator = $('select.bulkmail-list-operator').val();
			data.conditions = conditions;

			loader(true);

			bulkmail.trigger('disable');

			bulkmail.util.ajax('get_totals', data, function (response) {
				bulkmail.trigger('enable');
				loader(false, response.totalformatted);
				bulkmail.receivers.$.conditionsOutput.html(response.conditions);
			}, function (jqXHR, textStatus, errorThrown) {
				bulkmail.trigger('enable');
				loader(false, '?');
			});
		}, 10);

	})

	function loader(show, html) {
		if (null == show || true === show) {
			bulkmail.receivers.$.total.addClass('loading');
		} else {
			bulkmail.receivers.$.total.removeClass('loading');
		}
		if (null != html) {
			bulkmail.receivers.$.total.html(html);
		}
	}

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end Receivers


// block Options
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.$.options = $('#bulkmail_options .inside');

	bulkmail.options = bulkmail.options || {};

	bulkmail.options.$ = {};
	bulkmail.options.$.colorInputs = bulkmail.$.options.find('input.color');

	bulkmail.$.options
		.on('click', '.wp-color-result', function () {
			$(this).closest('li.bulkmail-color').addClass('open');
		})
		.on('click', 'a.default-value', function () {
			var el = $(this).prev().find('input'),
				color = el.data('default');

			el.wpColorPicker('color', color);
			return false;
		})
		.on('click', 'ul.colorschema', function () {
			var colorfields = bulkmail.$.options.find('input.color'),
				li = $(this).find('li.colorschema-field');

			bulkmail.trigger('disable');

			$.each(li, function (i) {
				var color = li.eq(i).data('hex');
				colorfields.eq(i).wpColorPicker('color', color);
			});

			bulkmail.trigger('enable');

		})
		.on('click', 'a.savecolorschema', function () {
			var colors = $.map(bulkmail.$.options.find('.color'), function (e) {
				return $(e).val();
			});

			loader(true);

			bulkmail.util.ajax('save_color_schema', {
				template: $('#bulkmail_template_name').val(),
				colors: colors
			}, function (response) {
				loader(false);
				if (response.success) {
					$('.colorschema').last().after($(response.html).hide().fadeIn());
				}
			}, function (jqXHR, textStatus, errorThrown) {
				loader(false);
			})

		})
		.on('click', '.colorschema-delete', function () {

			if (confirm(bulkmail.l10n.campaigns.delete_colorschema)) {

				var schema = $(this).parent().parent();

				loader(true);

				bulkmail.util.ajax('delete_color_schema', {
					template: $('#bulkmail_template_name').val(),
					hash: schema.data('hash')
				}, function (response) {
					loader(false);
					if (response.success) {
						schema.fadeOut(100, function () {
							schema.remove()
						});
					}
				}, function (jqXHR, textStatus, errorThrown) {
					loader(false);
				});

			}

			return false;

		})
		.on('click', '.colorschema-delete-all', function () {

			if (confirm(bulkmail.l10n.campaigns.delete_colorschema_all)) {

				var schema = $('.colorschema.custom');

				loader(true);

				bulkmail.util.ajax('delete_color_schema_all', {
					template: $('#bulkmail_template_name').val(),
				}, function (response) {
					loader(false);
					if (response.success) {
						schema.fadeOut(100, function () {
							schema.remove()
						});
					}
				}, function (jqXHR, textStatus, errorThrown) {
					loader(false);
				});

			}

			return false;

		})
		.on('change', '#bulkmail_version', function () {
			var val = $(this).val();
			_changeElements(val);
		})
		.on('change', 'input.color', function (event, ui) {
			var _this = $(this);
			var _val = Color(_this.val()).toString().toUpperCase();
			var _from = _this.data('value');
			var original = _this.data('default-color');

			_this.val(_val);

			bulkmail.util.changeColor(_from, _val, _this, original);
		});

	$.wp.wpColorPicker && bulkmail.options.$.colorInputs.wpColorPicker({
		color: true,
		width: 250,
		mode: 'hsl',
		palettes: $('.colors').data('original'),
		change: function (event, ui) {
			$(this).val(ui.color.toString()).trigger('change');
		},
		clear: function (event, ui) {}
	});

	function loader(show) {
		if (null == show || true === show) {
			$('#colorschema-ajax-loading').css('display', 'inline');
		} else {
			$('#colorschema-ajax-loading').hide();
		}
	}


	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end Options


// block Attachments
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.$.attachments = $('#bulkmail_attachments .inside');

	bulkmail.attachments = bulkmail.attachments || {};

	bulkmail.attachments.$ = {};

	bulkmail.$.attachments
		.on('click', '.delete-attachment', function (event) {
			event.preventDefault();
			$(this).parent().remove();
		})
		.on('click', '.add-attachment', function (event) {
			event.preventDefault();
			if (!wp.media.frames.bulkmail_attachments) {
				wp.media.frames.bulkmail_attachments = wp.media({
					title: bulkmail.l10n.campaigns.add_attachment,
					button: {
						text: bulkmail.l10n.campaigns.add_attachment,
					},
					multiple: false
				});
				wp.media.frames.bulkmail_attachments.on('select', function () {
					var attachment = wp.media.frames.bulkmail_attachments.state().get('selection').first().toJSON(),
						el = $('.bulkmail-attachment').eq(0).clone();
					el.find('img').attr('src', attachment.icon);
					el.find('.bulkmail-attachment-label').html(attachment.filename);
					el.find('input').attr('name', 'bulkmail_data[attachments][]').val(attachment.id);
					el.appendTo('.bulkmail-attachments');

				});
			}
			wp.media.frames.bulkmail_attachments.open();
		});

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end Attachments



// block heartbeat
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.events.push('documentReady', function () {
		if (typeof wp != 'undefined' && wp.heartbeat) wp.heartbeat.interval('fast');
	})
	bulkmail.$.document
		.on('heartbeat-send', function (e, data) {

			if (!bulkmail.editor) return;

			if (bulkmail.editable) {
				if (data && data['wp_autosave']) {
					data['wp_autosave']['content'] = bulkmail.editor.getContent();
					data['wp_autosave']['excerpt'] = bulkmail.$.excerpt.val();
					data['bulkmaildata'] = bulkmail.$.datafields.serialize();
				}
			} else {
				if (data['wp_autosave'])
					delete data['wp_autosave'];

				data['bulkmail'] = {
					page: 'edit',
					id: bulkmail.campaign_id
				};
			}

		})
		.on('heartbeat-tick', function (e, data) {

			if (bulkmail.editable || !data.bulkmail || !data.bulkmail[bulkmail.campaign_id]) return;

			var _data = data.bulkmail[bulkmail.campaign_id],
				stats = $('#stats').find('.verybold'),
				charts = $('#stats').find('.piechart'),
				progress = $('.progress'),
				p = (_data.sent / _data.total * 100);

			$('.hb-sent').html(_data.sent_f);
			$('.hb-deleted').html(_data.deleted_f);
			$('.hb-opens').html(_data.opens_f);
			$('.hb-clicks').html(_data.clicks_f);
			$('.hb-clicks_total').html(_data.clicks_total_f);
			$('.hb-unsubs').html(_data.unsubs_f);
			$('.hb-bounces').html(_data.bounces_f);
			$('.hb-geo_location').html(_data.geo_location);

			$.each(_data.environment, function (type) {
				$('.hb-' + type).html((this.percentage * 100).toFixed(2) + '%');
			});

			if ($('#stats_opens').length) $('#stats_opens').data('easyPieChart').update(Math.round(_data.open_rate));
			if ($('#stats_clicks').length) $('#stats_clicks').data('easyPieChart').update(Math.round(_data.click_rate));
			if ($('#stats_unsubscribes').length) $('#stats_unsubscribes').data('easyPieChart').update(Math.round(_data.unsub_rate));
			if ($('#stats_bounces').length) $('#stats_bounces').data('easyPieChart').update(Math.round(_data.bounce_rate));

			progress.find('.bar').width(p + '%');
			progress.find('span').eq(1).html(_data.sent_formatted);
			progress.find('span').eq(2).html(_data.sent_formatted);
			progress.find('var').html(Math.round(p) + '%');

			bulkmail.clickmap.updateBadges(_data.clickbadges);

			if (_data.status != $('#original_post_status').val() && !$('#bulkmail_status_changed_info').length) {

				$('<div id="bulkmail_status_changed_info" class="error inline"><p>' + bulkmail.util.sprintf(bulkmail.l10n.campaigns.statuschanged, '<a href="post.php?post=' + bulkmail.campaign_id + '&action=edit">' + bulkmail.l10n.campaigns.click_here + '</a></p></div>'))
					.hide()
					.prependTo('#postbox-container-2')
					.slideDown(200);
			}

		});


	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end heartbeat