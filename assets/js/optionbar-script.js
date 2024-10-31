// optionbar
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	var codemirror,
		codemirrorargs = {
			mode: {
				name: "htmlmixed",
				scriptTypes: [{
					matches: /\/x-handlebars-template|\/x-mustache/i,
					mode: null
				}, {
					matches: /(text|application)\/(x-)?vb(a|script)/i,
					mode: "vbscript"
				}]
			},
			tabMode: "indent",
			lineNumbers: true,
			viewportMargin: Infinity,
			autofocus: true
		};

	bulkmail.optionbar = {};

	bulkmail.optionbar.undos = [];
	bulkmail.optionbar.currentUndo = 0;

	bulkmail.optionbar.undo = function () {

		if (bulkmail.optionbar.currentUndo) {
			bulkmail.optionbar.currentUndo--;
			bulkmail.editor.setContent(bulkmail.optionbar.undos[bulkmail.optionbar.currentUndo], 100, false);
			bulkmail.$.optionbar.find('a.redo').removeClass('disabled');
			if (!bulkmail.optionbar.currentUndo) {
				$(this).addClass('disabled');
			}
		}

	};

	bulkmail.optionbar.redo = function () {
		var length = bulkmail.optionbar.undos.length;

		if (bulkmail.optionbar.currentUndo < length - 1) {
			bulkmail.optionbar.currentUndo++;
			bulkmail.editor.setContent(bulkmail.optionbar.undos[bulkmail.optionbar.currentUndo], 100, false);
			bulkmail.$.optionbar.find('a.undo').removeClass('disabled');
			if (bulkmail.optionbar.currentUndo >= length - 1) {
				$(this).addClass('disabled');
			}
		}
	}

	bulkmail.optionbar.removeModules = function () {
		if (confirm(bulkmail.l10n.campaigns.remove_all_modules)) {
			var modulecontainer = bulkmail.$.iframe.contents().find('modules');
			var modules = modulecontainer.find('module');
			modulecontainer.slideUp(
				function () {
					modules.remove();
					modulecontainer.html('').show();
					bulkmail.trigger('refresh');
					bulkmail.trigger('save');
				}
			);
		}
	}

	bulkmail.optionbar.codeView = function () {

		var structure;

		if (bulkmail.$.iframe.is(':visible')) {

			structure = bulkmail.editor.getStructure(bulkmail.editor.getFrameContent());

			bulkmail.$.optionbar.find('a.code').addClass('loading');
			bulkmail.trigger('disable');

			bulkmail.util.ajax(
				'toggle_codeview', {
					bodyattributes: structure.parts[2],
					content: structure.content,
					head: structure.head
				},
				function (response) {
					bulkmail.$.optionbar.find('a.code').addClass('active').removeClass('loading');
					bulkmail.$.html.hide();
					bulkmail.$.content.val(response.content);
					bulkmail.$.optionbar.find('a').not('a.redo, a.undo, a.code').addClass('disabled');

					codemirror = bulkmail.util.CodeMirror.fromTextArea(bulkmail.$.content.get(0), codemirrorargs);

				},
				function (jqXHR, textStatus, errorThrown) {
					bulkmail.$.optionbar.find('a.code').addClass('active').removeClass('loading');
					bulkmail.trigger('enable');
				}
			);

		} else {

			structure = bulkmail.editor.getStructure(codemirror.getValue());
			codemirror.clearHistory();

			bulkmail.$.optionbar.find('a.code').addClass('loading');
			bulkmail.trigger('disable');

			bulkmail.util.ajax(
				'toggle_codeview', {
					bodyattributes: structure.parts[2],
					content: structure.content,
					head: structure.head
				},
				function (response) {
					bulkmail.editor.setContent(response.content, 100, true, response.style);
					bulkmail.$.html.show();
					bulkmail.$.content.hide();
					$('.CodeMirror').remove();
					bulkmail.$.optionbar.find('a.code').removeClass('active').removeClass('loading');
					bulkmail.$.optionbar.find('a').not('a.redo, a.undo, a.code').removeClass('disabled');

					bulkmail.trigger('enable');

				},
				function (jqXHR, textStatus, errorThrown) {
					bulkmail.$.optionbar.find('a.code').addClass('active').removeClass('loading');
					bulkmail.trigger('enable');
				}
			);

		}
		return false;
	}

	bulkmail.optionbar.plainText = function () {

		if (bulkmail.$.iframe.is(':visible')) {

			bulkmail.$.optionbar.find('a.plaintext').addClass('active');
			bulkmail.$.html.hide();
			bulkmail.$.excerpt.show();
			bulkmail.$.plaintext.show();
			bulkmail.$.optionbar.find('a').not('a.redo, a.undo, a.plaintext, a.preview').addClass('disabled');

		} else {

			bulkmail.$.html.show();
			bulkmail.$.plaintext.hide();
			bulkmail.$.optionbar.find('a.plaintext').removeClass('active');
			bulkmail.$.optionbar.find('a').not('a.redo, a.undo, a.plaintext, a.preview').removeClass('disabled');

			bulkmail.trigger('refresh');

		}

	}

	bulkmail.optionbar.openSaveDialog = function () {

		tb_show(bulkmail.l10n.campaigns.save_template, '#TB_inline?x=1&width=480&height=320&inlineId=bulkmail_template_save', null);
		$('#new_template_name').focus().select();
	};

	bulkmail.optionbar.preview = function () {

		if (bulkmail.$.optionbar.find('a.preview').is('.loading')) {
			return false;
		}

		bulkmail.trigger('save');

		bulkmail.$.optionbar.find('a.preview').addClass('loading');
		bulkmail.util.ajax(
			'set_preview', {
				id: bulkmail.campaign_id,
				content: bulkmail.editor.getContent(),
				head: bulkmail.$.head.val(),
				issue: $('#bulkmail_autoresponder_issue').val(),
				subject: bulkmail.details.$.subject.val(),
				preheader: bulkmail.details.$.preheader.val()
			},
			function (response) {
				bulkmail.$.optionbar.find('a.preview').removeClass('loading');

				bulkmail.thickbox.$.preview.attr('src', ajaxurl + '?action=bulkmail_get_preview&hash=' + response.hash + '&_wpnonce=' + response.nonce);
				tb_show((bulkmail.$.title.val() ? bulkmail.util.sprintf(bulkmail.l10n.campaigns.preview_for, '"' + bulkmail.$.title.val() + '"') : bulkmail.l10n.campaigns.preview), '#TB_inline?hash=' + response.hash + '&_wpnonce=' + response.nonce + '&width=' + (Math.min(1200, bulkmail.$.window.width() - 50)) + '&height=' + (bulkmail.$.window.height() - 100) + '&inlineId=bulkmail_campaign_preview', null);

			},
			function (jqXHR, textStatus, errorThrown) {
				bulkmail.$.optionbar.find('a.preview').removeClass('loading');
			}
		);

	}

	bulkmail.optionbar.dfw = function (event) {

		if (event.type == 'mouseout' && !/DIV|H3/.test(event.target.nodeName)) {
			return;
		}

		if (!bulkmail.$.body.hasClass('focus-on')) {
			bulkmail.$.body.removeClass('focus-off').addClass('focus-on');
			bulkmail.$.wpbody.on('mouseleave.dfw', bulkmail.optionbar.dfw);
			bulkmail.$.optionbar.find('a.dfw').addClass('active');
			if (bulkmail.$.window.scrollTop() < containerOffset()) {
				bulkmail.util.scroll(containerOffset() - 80);
			}

		} else {
			bulkmail.$.body.removeClass('focus-on').addClass('focus-off');
			bulkmail.$.wpbody.off('mouseleave', bulkmail.optionbar.dfw);
			bulkmail.$.optionbar.find('a.dfw').removeClass('active');
		}

	}

	bulkmail.$.document
		.on('click', 'button.save-template', saveTemplate)
		.on('click', 'button.save-template-cancel', tb_remove);

	bulkmail.$.optionbar
		.on('click', 'a', false)
		.on('click', 'a.save-template', bulkmail.optionbar.openSaveDialog)
		.on('click', 'a.clear-modules', bulkmail.optionbar.removeModules)
		.on('click', 'a.preview', bulkmail.optionbar.preview)
		.on('click', 'a.undo', bulkmail.optionbar.undo)
		.on('click', 'a.redo', bulkmail.optionbar.redo)
		.on('click', 'a.code', bulkmail.optionbar.codeView)
		.on('click', 'a.plaintext', bulkmail.optionbar.plainText)
		.on('click', 'a.dfw', bulkmail.optionbar.dfw)
		.on('click', 'a.template', showFiles)
		.on('click', 'a.file', changeTemplate);

	bulkmail.$.window
		//.on('scroll.optionbar', bulkmail.util.throttle(togglefix, 100))
		.on('resize.optionbar', function () {
			bulkmail.$.window.trigger('scroll.optionbar');
		});

	bulkmail.events.push('editorLoaded', function () {
		bulkmail.optionbar.undos.push(bulkmail.editor.getFrameContent());
	});

	$('.meta-box-sortables').on("sortstop", function (event, ui) {
		bulkmail.$.window.trigger('resize.optionbar');
	});

	function containerOffset() {
		if (!bulkmail.dom.template) return 0;
		return bulkmail.$.template.offset().top;
	}


	function togglefix() {
		var scrolltop = bulkmail.util.top();

		if (scrolltop < containerOffset() || scrolltop > containerOffset() + bulkmail.$.template.height() - 120) {
			if (/fixed-optionbar/.test(bulkmail.dom.body.className)) {
				bulkmail.$.body.removeClass('fixed-optionbar');
				bulkmail.$.optionbar.width('auto');
			}
		} else {
			if (!/fixed-optionbar/.test(bulkmail.dom.body.className)) {
				bulkmail.$.body.addClass('fixed-optionbar');
				bulkmail.$.optionbar.width(bulkmail.$.template.width() - 22);
			}
		}
	}

	function showFiles(name) {
		var $this = $(this);
		$this.parent().find('ul').eq(0).slideToggle(100);
	}

	function changeTemplate() {
		window.onbeforeunload = null;
		window.location = this.href;
	}

	function saveTemplate() {

		bulkmail.trigger('disable');

		var name = $('#new_template_name').val();
		if (!name) {
			return false;
		}
		bulkmail.trigger('save');

		var loader = $('#new_template-ajax-loading').css('display', 'inline'),
			modules = $('#new_template_modules').is(':checked'),
			activemodules = $('#new_template_active_modules').is(':checked'),
			file = $('#new_template_saveas_dropdown').val(),
			overwrite = !!parseInt($('input[name="new_template_overwrite"]:checked').val(), 10),
			content = bulkmail.editor.getContent();

		bulkmail.util.ajax(
			'create_new_template', {
				name: name,
				modules: modules,
				activemodules: activemodules,
				overwrite: overwrite ? file : false,
				template: $('#bulkmail_template_name').val(),
				content: content,
				head: bulkmail.$.head.val()
			},
			function (response) {
				loader.hide();
				if (response.success) {
					// destroy wp object
					if (window.wp) {
						window.wp = null;
					}
					window.onbeforeunload = null;
					window.location = response.url;
				} else {
					alert(response.msg);
				}
			},
			function (jqXHR, textStatus, errorThrown) {
				loader.hide();
			}
		);
		return false;
	}


	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end optiobar