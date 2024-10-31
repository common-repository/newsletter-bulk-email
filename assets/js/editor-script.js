// not in an iframe
if (parent.window === window) {

	var campaign_id;
	if (campaign_id = location.search.match(/id=(\d+)/i)[1]) {
		window.location = location.protocol + '//' + location.host + location.pathname.replace('admin-ajax.php', 'post.php') + '?post=' + campaign_id + '&action=edit';
	}
}

document.getElementsByTagName("html")[0].className += ' bulkmail-loading';
window.bulkmail = parent.window.bulkmail || {};

// block
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	bulkmail.editor = bulkmail.editor || {};

	bulkmail.editor.loaded = false;

	bulkmail.editor.$ = bulkmail.editor.$ || {};
	bulkmail.editor.$.html = $('html');
	bulkmail.editor.$.body = $('body');

	$(window).on('load', function () {
		bulkmail.editor.$.html.removeClass('bulkmail-loading');
		bulkmail.editor.$.body = $('body');
		bulkmail.editor.$.body
			.on('click', 'a', function (event) {
				event.preventDefault();
			})
			.on('click', function (event) {
				bulkmail.modules.selected && bulkmail.modules.selected.removeAttr('selected');
			})
			.on('click', 'module', function (event) {
				if ('MODULE' == event.target.nodeName) {
					var module = $(this);
					event.stopPropagation();
					if (bulkmail.modules.selected[0] != module[0]) {
						bulkmail.trigger('selectModule', module);
					}
				}
			})
			.on('click', 'button.addbutton', function () {
				var data = $(this).data(),
					element = decodeURIComponent(data.element.data('tmpl')) || '<a href="" editable label="Button"></a>';

				bulkmail.editbar.open({
					type: 'btn',
					offset: data.offset,
					element: $(element).attr('tmpbutton', '').appendTo(data.element),
					name: data.name
				});
				return false;
			})
			.on('click', 'button.addrepeater', function () {
				var data = $(this).data();

				if ('TH' == data.element[0].nodeName || 'TD' == data.element[0].nodeName) {
					var table = data.element.closest('table'),
						index = data.element.prevAll().length;
					for (var i = table[0].rows.length - 1; i >= 0; i--) {
						$(table[0].rows[i].cells[index]).clone().insertAfter(table[0].rows[i].cells[index]);
					}
				} else {
					data.element.clone().insertAfter(data.element);
				}

				bulkmail.trigger('save');
				bulkmail.trigger('refresh');

				return false;
			})
			.on('click', 'button.removerepeater', function () {
				var data = $(this).data();

				if ('TH' == data.element[0].nodeName || 'TD' == data.element[0].nodeName) {
					var table = data.element.closest('table'),
						index = data.element.prevAll().length;
					for (var i = table[0].rows.length - 1; i >= 0; i--) {
						$(table[0].rows[i].cells[index]).remove();
					}
				} else {
					data.element.remove();
				}

				bulkmail.trigger('save');
				bulkmail.trigger('refresh');

				return false;
			});

		if (!bulkmail.editor.loaded) {
			bulkmail.editor.loaded = true;
			bulkmail.trigger('editorLoaded');
		}

	});

	bulkmail.editor.getFrameContent = function () {

		if (!bulkmail.editor.$.body.length) {
			return false;
		}

		var body = bulkmail.editor.$.body[0],
			clone, content, bodyattributes, attrcount, s = '';

		clone = $('<div>' + body.innerHTML + '</div>');

		clone.find('.mce-tinymce, .mce-widget, .mce-toolbar-grp, .mce-container, .screen-reader-text, .ui-helper-hidden-accessible, .wplink-autocomplete, modulebuttons, bulkmail, #bulkmail-editorimage-upload-button, button').remove();

		clone.find('single, multi, module, modules, buttons').removeAttr('contenteditable spellcheck id dir style class selected');
		content = $.trim(clone.html().replace(/\u200c/g, '&zwnj;').replace(/\u200d/g, '&zwj;'));

		bodyattributes = body.attributes || [];
		attrcount = bodyattributes.length;

		if (attrcount) {
			while (attrcount--) {
				s = ' ' + bodyattributes[attrcount].name + '="' + $.trim(bodyattributes[attrcount].value) + '"' + s;
			}
		}
		s = $.trim(
			s
			.replace(/(webkit |wp\-editor|mceContentBody|position: relative;|cursor: auto;|modal-open| spellcheck="(true|false)")/g, '')
			.replace(/(class="(\s*)"|style="(\s*)")/g, '')
		);

		return bulkmail.$.head.val() + "\n<body" + (s ? ' ' + s : '') + ">\n" + content + "\n</body>\n</html>";
	}

	bulkmail.editor.cleanup = function () {

		// remove some third party elements
		bulkmail.editor.$.document.find('#a11y-speak-assertive, #a11y-speak-polite, #droplr-chrome-extension-is-installed').remove();

	}

	bulkmail.editor.getStructure = function (html) {
		var parts = html.match(/([^]*)<body([^>]*)?>([^]*)<\/body>([^]*)/m);

		return {
			parts: parts ? parts : ['', '', '', '<multi>' + html + '</multi>'],
			content: parts ? parts[3] : '<multi>' + html + '</multi>',
			head: parts ? $.trim(parts[1]) : '',
			bodyattributes: parts ? $('<div' + (parts[2] || '') + '></div>')[0].attributes : ''
		};
	}

	bulkmail.editor.getContent = function () {
		return bulkmail.$.content.val() || bulkmail.editor.getFrameContent();
	}

	bulkmail.editor.setContent = function (content, delay, saveit, extrastyle) {

		var structure = bulkmail.editor.getStructure(content),
			attrcount = structure.bodyattributes.length,
			head = bulkmail.editor.$.document.find('head'),
			headstyles = head.find('link');

		bulkmail.$.head.val(structure.head);
		if (!extrastyle) {
			extrastyle = '';
		}
		head[0].innerHTML = structure.head.replace(/([^]*)<head([^>]*)?>([^]*)<\/head>([^]*)/m, '$3' + extrastyle);
		head.append(headstyles);

		bulkmail.editor.$.body[0].innerHTML = structure.content;

		if (attrcount) {
			while (attrcount--) {
				bulkmail.editor.$.body[0].setAttribute(structure.bodyattributes[attrcount].name, structure.bodyattributes[attrcount].value)
			}
		}

		bulkmail.$.content.val(content);

		if (typeof saveit == 'undefined' || saveit === true) {
			bulkmail.trigger('save');
		}

		setTimeout(function () {
			bulkmail.trigger('redraw');
		}, 100);
	}

	bulkmail.editor.getHeight = function () {
		return Math.max(500, bulkmail.editor.$.body.outerHeight());
	}


	bulkmail.editor.resize = function () {
		if (!bulkmail.editor.loaded) return false;
		setTimeout(function () {
			bulkmail.$.iframe.attr("height", bulkmail.editor.getHeight());
			bulkmail.trigger('resize');
		}, 50);
	}

	bulkmail.editor.colors = bulkmail.$.options.find('.colors').data();

	function initFrame() {
		bulkmail.$.templateWrap.removeClass('load');
		bulkmail.trigger('iframeLoaded');
		makeEditable();
	}

	function makeEditable() {

		bulkmail.editor.$.document.find('.content.bulkmail-btn').remove();
		var modulehelper = null;

		if (!bulkmail.editor.$.document) return;

		bulkmail.editor.$.document
			//.off('.bulkmail')
			.on('click.bulkmail', 'img[editable]', function (event) {
				event.stopPropagation();
				var $this = $(this),
					offset = $this.offset(),
					top = offset.top + 61,
					left = offset.left,
					name = $this.attr('label'),
					type = 'img';

				bulkmail.editbar.open({
					'offset': offset,
					'type': type,
					'name': name,
					'element': $this
				});

			})
			.on('click.bulkmail', 'module td[background],module th[background]', function (event) {
				event.stopPropagation();
				modulehelper = true;
			})
			.on('click.bulkmail', 'td[background], th[background]', function (event) {
				event.stopPropagation();
				if (!modulehelper && event.target != this) return;
				modulehelper = null;

				var $this = $(this),
					offset = $this.offset(),
					top = offset.top + 61,
					left = offset.left,
					name = $this.attr('label'),
					type = 'img';

				bulkmail.editbar.open({
					'offset': offset,
					'type': type,
					'name': name,
					'element': $this
				});

			})
			.on('click.bulkmail', 'a[editable]', function (event) {
				event.stopPropagation();
				event.preventDefault();
				var $this = $(this),
					offset = $this.offset(),
					top = offset.top + 40,
					left = offset.left,
					name = $this.attr('label'),
					type = 'btn';

				bulkmail.editbar.open({
					'offset': offset,
					'type': type,
					'name': name,
					'element': $this
				});


			})

		if (!bulkmaildata.inline) {
			bulkmail.editor.$.container
				.on('click.bulkmail', 'multi, single', function (event) {
					event.stopPropagation();
					var $this = $(this),
						offset = $this.offset(),
						top = offset.top + 40,
						left = offset.left,
						name = $this.attr('label'),
						type = $this.prop('tagName').toLowerCase();

					bulkmail.editbar.open({
						'offset': offset,
						'type': type,
						'name': name,
						'element': $this
					});
				});
		}

		bulkmail.editor.cleanup();

	}

	bulkmail.events = bulkmail.events || [];

	bulkmail.events.push('refresh', bulkmail.editor.resize);
	bulkmail.events.push('editorLoaded', initFrame, bulkmail.editor.resize);
	bulkmail.events.push('redraw', makeEditable);


	// legacy buttons
	bulkmail.editor.$.body.find('div.modulebuttons').remove();
	(bulkmail.isrtl) ? bulkmail.editor.$.html.attr('bulkmail-is-rtl', ''): bulkmail.editor.$.html.removeAttr('bulkmail-is-rtl');

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end block


// block Modules
bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	var changetimeout,
		change = false,
		uploader = false;

	bulkmail.events = bulkmail.events || [];

	bulkmail.events.push('editorLoaded',
		function () {
			bulkmail.events.push('refresh', updateElements, sortable, draggable);
			sortable();
			draggable();
			bulkmail.events.push('resize', buttons) && buttons();
			bulkmail.events.push('selectModule', select);
			typeof mOxie != 'undefined' && bulkmail.events.push('refresh', upload) && upload();
			typeof tinymce != 'undefined' && bulkmail.events.push('refresh', inlineEditor) && inlineEditor();
		}
	)

	$(document).ready(updateElements);

	function updateElements() {
		bulkmail.editor.$ = bulkmail.editor.$ || {};
		bulkmail.editor.$.document = $(document);
		bulkmail.editor.$.window = $(window);
		bulkmail.editor.$.html = $('html');
		bulkmail.editor.$.body = $('body');
		bulkmail.editor.$.container = $('modules');
		bulkmail.editor.$.modules = $('module');
		bulkmail.editor.$.images = $('img[editable]');
		bulkmail.editor.$.buttons = $('buttons');
		bulkmail.editor.$.repeatable = $('[repeatable]');
	}

	function moduleButtons() {

		var elements = $(bulkmaildata.modules).add(bulkmail.editor.$.modules),
			mc = 0;

		//no modules at all
		if (!bulkmail.editor.$.modules.length) {
			//selector.remove();
			return;
		}

		elements = bulkmail.editor.$.modules;

		// add module buttons and add them to the list
		$.each(elements, function (j) {
			var $this = $(this);
			if ($this.is('module') && !$this.find('modulebuttons').length) {
				var name = $this.attr('label') || bulkmail.util.sprintf(bulkmail.l10n.campaigns.module, '#' + (++mc)),
					codeview = bulkmaildata.codeview ? '<button class="bulkmail-btn codeview" title="' + bulkmail.l10n.campaigns.codeview + '"></button>' : '',
					auto = ($this.is('[auto]') ? '<button class="bulkmail-btn auto" title="' + bulkmail.l10n.campaigns.auto + '"></button>' : '');

				$('<modulebuttons>' + '<input class="modulelabel" type="text" value="' + name + '" placeholder="' + name + '" title="' + bulkmail.l10n.campaigns.module_label + '" tabindex="-1"><span>' + auto + '<button class="bulkmail-btn duplicate" title="' + bulkmail.l10n.campaigns.duplicate_module + '"></button><button class="bulkmail-btn up" title="' + bulkmail.l10n.campaigns.move_module_up + '"></button><button class="bulkmail-btn down" title="' + bulkmail.l10n.campaigns.move_module_down + '"></button>' + codeview + '<button class="bulkmail-btn remove" title="' + bulkmail.l10n.campaigns.remove_module + '"></button></span></modulebuttons>').prependTo($this);

			}
		});

	}

	function sortable() {
		if (bulkmail.editor.$.container.data('sortable')) bulkmail.editor.$.container.sortable('destroy');

		if (bulkmail.editor.$.modules.length < 2) return;

		bulkmail.editor.$.container.sortable({
			stop: function (event, ui) {
				event.stopPropagation();
				bulkmail.editor.$.container.removeClass('dragging');
				setTimeout(function () {
					bulkmail.trigger('refresh');
					bulkmail.trigger('save');
				}, 200);
			},
			start: function (event, ui) {
				event.stopPropagation();
				bulkmail.editor.$.container.addClass('dragging');
			},
			containment: 'body',
			revert: 100,
			axis: 'y',
			placeholder: "sortable-placeholder",
			items: "> module",
			delay: 20,
			distance: 5,
			scroll: true,
			scrollSensitivity: 10,
			forcePlaceholderSize: true,
			helper: 'clone',
			zIndex: 10000

		});
	}

	function draggable() {

		if (bulkmail.editor.$.images.data('draggable')) bulkmail.editor.$.images.draggable('destroy');
		if (bulkmail.editor.$.images.data('droppable')) bulkmail.editor.$.images.droppable('destroy');

		bulkmail.editor.$.images
			.draggable({
				helper: "clone",
				scroll: true,
				scrollSensitivity: 10,
				opacity: 0.7,
				zIndex: 1000,
				revert: 'invalid',
				addClasses: false,
				create: function (event, ui) {
					$(event.target).removeClass('ui-draggable-handle');
				},
				start: function () {
					bulkmail.editor.$.body.addClass('ui-dragging');
				},
				stop: function () {
					bulkmail.editor.$.body.removeClass('ui-dragging');
					bulkmail.trigger('refresh');

				}
			})
			.droppable({
				addClasses: false,
				over: function (event, ui) {
					$(event.target).addClass('ui-drag-over');
				},
				out: function (event, ui) {
					$(event.target).removeClass('ui-drag-over');
				},
				drop: function (event, ui) {
					var org = $(ui.draggable[0]),
						target = $(event.target),
						target_id, org_id, crop, copy;

					target.removeClass('ui-drag-over');

					if (!org.is('img') || !target.is('img')) return;

					target_id = target.attr('data-id') ? parseInt(target.attr('data-id'), 10) : null;
					org_id = org.attr('data-id') ? parseInt(org.attr('data-id'), 10) : null;
					crop = org.data('crop');
					copy = org.clone();

					org.addClass('bulkmail-loading');
					target.addClass('bulkmail-loading');

					bulkmail.util.getRealDimensions(org, function (org_w, org_h, org_f) {
						bulkmail.util.getRealDimensions(target, function (target_w, target_h, target_f) {

							if (event.altKey) {
								org.removeClass('bulkmail-loading');
								target.removeClass('bulkmail-loading');
							} else if (target_id) {

								bulkmail.util.ajax('create_image', {
									id: target_id,
									width: org_w,
									height: org_h,
									crop: org.data('crop'),
								}, function (response) {

									org.removeAttr('src').attr({
										'data-id': target_id,
										'title': target.attr('title'),
										'alt': target.attr('alt'),
										'src': response.image.url,
										'width': Math.round(response.image.width / org_f),
										'height': Math.round(response.image.height / org_f)
									}).data('id', target_id).removeClass('bulkmail-loading');

								}, function (jqXHR, textStatus, errorThrown) {

									alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\nCheck the JS console for more info!');

								});
							} else {

								org.removeAttr('src').attr({
									'data-id': 0,
									'title': target.attr('title'),
									'alt': target.attr('alt'),
									'src': target.attr('src'),
									'width': Math.round(org_w / org_f),
									'height': Math.round((org_w / (target_w / target_h)) / org_f)
								}).data('id', 0).removeClass('bulkmail-loading');

							}

							if (org_id) {
								bulkmail.util.ajax('create_image', {
									id: org_id,
									width: target_w,
									height: target_h,
									crop: target.data('crop'),
								}, function (response) {

									target.removeAttr('src').attr({
										'data-id': org_id,
										'title': org.attr('title'),
										'alt': org.attr('alt'),
										'src': response.image.url,
										'width': Math.round(response.image.width / target_f),
										'height': Math.round(response.image.height / target_f)
									}).data('id', org_id).removeClass('bulkmail-loading');

									bulkmail.trigger('refresh');

								}, function (jqXHR, textStatus, errorThrown) {

									alert(textStatus + ' ' + jqXHR.status + ': ' + errorThrown + '\n\nCheck the JS console for more info!');

								});
							} else {

								target.removeAttr('src').attr({
									'data-id': 0,
									'title': copy.attr('title'),
									'alt': copy.attr('alt'),
									'src': copy.attr('src'),
									'width': Math.round(target_w / target_f),
									'height': Math.round((target_w / (org_w / org_h)) / target_f)
								}).data('id', 0).removeClass('bulkmail-loading');

							}

							if (!org_id && !target_id) bulkmail.trigger('refresh');

						});
					});

				}
			});
	}

	function buttons() {
		if (bulkmail.editor.$.buttons) {
			$.each(bulkmail.editor.$.buttons, function () {

				var $this = $(this),
					name = $this.attr('label'),
					offset = this.getBoundingClientRect(),
					top = offset.top + 0,
					left = offset.right + 0,
					btn, tmpl;

				if ($this.data('has-buttons')) return;

				btn = $('<button class="addbutton bulkmail-btn bulkmail-btn-inline" title="' + bulkmail.l10n.campaigns.add_button + '"></button>').appendTo($this);

				btn.data('offset', offset).data('name', name);
				btn.data('element', $this);

				$this.data('has-buttons', true);

				if (!(tmpl = $this.data('tmpl'))) {
					if ($this.find('.textbutton').length) {
						tmpl = $this.find('.textbutton').last();
					} else if ($this.find('img').length) {
						tmpl = $this.find('a[editable]').last();
					} else {
						tmpl = $('<a href="" editable label="Button"></a>');
					}
					tmpl = $('<div/>').text(encodeURIComponent(tmpl[0].outerHTML)).html();
				}

				$this.attr('data-tmpl', tmpl).data('tmpl', tmpl);

			});
		}

		$('button.addrepeater, button.removerepeater').remove();

		if (bulkmail.editor.$.repeatable) {
			$.each(bulkmail.editor.$.repeatable, function () {
				var $this = $(this),
					module = $this.closest('module'),
					name = $this.attr('label'),
					moduleoffset = module[0].getBoundingClientRect(),
					offset = this.getBoundingClientRect(),
					add_top = offset.top - moduleoffset.top,
					add_left = offset.left,
					del_top = offset.top - moduleoffset.top + 18,
					del_left = offset.left,
					btn;

				if ('TH' == this.nodeName || 'TD' == this.nodeName) {
					add_top = 0;
					add_left = offset.width - 36;
					del_top = 0;
					del_left = offset.width - 18;
				}

				btn = $('<button class="addrepeater bulkmail-btn bulkmail-btn-inline" title="' + bulkmail.l10n.campaigns.add_repeater + '"></button>').css({
					top: add_top,
					left: add_left
				}).appendTo($this);

				btn.data('offset', offset).data('name', name);
				btn.data('element', $this);

				btn = $('<button class="removerepeater bulkmail-btn bulkmail-btn-inline" title="' + bulkmail.l10n.campaigns.remove_repeater + '"></button>').css({
					top: del_top,
					left: del_left
				}).appendTo($this);

				btn.data('offset', offset).data('name', name);
				btn.data('element', $this);

			});
		}
	}

	function select(module) {
		if (!module.length) {
			return;
		}
		if (bulkmail.modules.selected) {
			bulkmail.modules.selected.removeAttr('selected');
		}
		bulkmail.modules.selected = module;
		bulkmail.modules.selected.attr('selected', true);
	}

	function upload() {
		$.each(bulkmail.editor.$.images, function () {

			var _this = $(this),
				dropzone;

			if (_this.data('has-dropzone')) return;

			dropzone = new mOxie.FileDrop({
				drop_zone: this,
			});

			dropzone.ondrop = function (e) {

				if (bulkmail.modules.dragging) return;
				_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');

				var file = dropzone.files.shift(),
					altkey = window.event && event.altKey,
					dimensions = [_this.width(), _this.height()],
					crop = _this.data('crop'),
					position = _this.offset(),
					upload = $('<upload><div class="bulkmail-upload-info"><div class="bulkmail-upload-info-bar"></div><div class="bulkmail-upload-info-text"></div></div></upload>'),
					preview = upload.find('.bulkmail-upload-info-bar'),
					previewtext = upload.find('.bulkmail-upload-info-text'),
					preloader = new mOxie.Image(file);

				preloader.onerror = function (e) {

					alert(bulkmail.l10n.campaigns.unsupported_format);

				}
				preloader.onload = function (e) {

					upload.insertAfter(_this);
					_this.appendTo(upload);

					file._element = _this;
					file._altKey = altkey;
					file._crop = crop;
					file._upload = upload;
					file._preview = preview;
					file._previewtext = previewtext;
					file._dimensions = [preloader.width, preloader.height, preloader.width / preloader.height];

					preloader.downsize(dimensions[0], dimensions[1]);
					preview.css({
						'background-image': 'url(' + preloader.getAsDataURL() + ')',
						'background-size': dimensions[0] + 'px ' + (crop ? dimensions[1] : dimensions[0] / file._dimensions[2]) + 'px'
					});

					uploader.addFile(file);
				};

				preloader.load(file);

			};
			dropzone.ondragenter = function (e) {
				if (bulkmail.modules.dragging) return;
				_this.addClass('ui-drag-over-file');
				if (window.event && event.altKey) _this.addClass('ui-drag-over-file-alt');
			};
			dropzone.ondragleave = function (e) {
				if (bulkmail.modules.dragging) return;
				_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');
			};
			dropzone.onerror = function (e) {
				if (bulkmail.modules.dragging) return;
				_this.removeClass('ui-drag-over-file ui-drag-over-file-alt');
			};

			dropzone.init();

			_this.data('has-dropzone', true);

		});


		if (!uploader) {

			$('<button id="bulkmail-editorimage-upload-button" />').hide().appendTo('bulkmail');
			uploader = new plupload.Uploader(bulkmaildata.plupload);

			uploader.bind('Init', function (up) {
				$('.moxie-shim').remove();
			});

			uploader.bind('FilesAdded', function (up, files) {

				var source = files[0].getSource();

				bulkmail.util.getRealDimensions(source._element, function (width, height, factor) {

					up.settings.multipart_params.width = width;
					up.settings.multipart_params.height = height;
					up.settings.multipart_params.factor = factor;
					up.settings.multipart_params.crop = source._crop;
					up.settings.multipart_params.altKey = source._altKey;
					up.refresh();
					up.start();
				});

			});

			uploader.bind('BeforeUpload', function (up, file) {});

			uploader.bind('UploadFile', function (up, file) {});

			uploader.bind('UploadProgress', function (up, file) {

				var source = file.getSource();

				source._preview.width(file.percent + '%');
				source._previewtext.html(file.percent + '%');

			});

			uploader.bind('Error', function (up, err) {
				var source = err.file.getSource();

				alert(err.message);

				source._element.insertAfter(source._upload);
				source._upload.remove();
			});

			uploader.bind('FileUploaded', function (up, file, response) {

				var source = file.getSource(),
					delay, height;

				try {
					response = $.parseJSON(response.response);

					source._previewtext.html(bulkmail.l10n.campaigns.ready);
					source._element.on('load', function () {
						clearTimeout(delay);
						source._preview.fadeOut(function () {
							source._element.insertAfter(source._upload);
							source._upload.remove();
							bulkmail.trigger('refresh');
						});
					});

					height = Math.round(source._element.width() / response.image.asp);

					source._element.attr({
						'src': response.image.url,
						'alt': response.name,
						'height': height,
						'data-id': response.image.id || 0
					}).data('id', response.image.id || 0);

					source._preview.height(height);

					delay = setTimeout(function () {
						source._preview.fadeOut(function () {
							source._element.insertAfter(source._upload);
							source._upload.remove();
							bulkmail.trigger('refresh');
						});
					}, 3000);
				} catch (err) {
					source._preview.addClass('error').find('.bulkmail-upload-info-text').html(bulkmail.l10n.campaigns.error);
					alert(bulkmail.l10n.campaigns.error_occurs + "\n" + err.message);
					source._preview.fadeOut(function () {
						source._element.insertAfter(source._upload);
						source._upload.remove();
					});
				}

			});

			uploader.bind('UploadComplete', function (up, files) {});

			uploader.init();

		}
	}

	function inlineEditor() {
		tinymce.init($.extend(bulkmaildata.tinymce.args, bulkmaildata.tinymce.multi, {
			paste_preprocess: paste_preprocess,
			urlconverter_callback: urlconverter,
			setup: setup
		}));
		tinymce.init($.extend(bulkmaildata.tinymce.args, bulkmaildata.tinymce.single, {
			paste_preprocess: paste_preprocess,
			urlconverter_callback: urlconverter,
			setup: setup
		}));
	}

	function paste_preprocess(pl, o) {

		var str = o.content,
			allowed_tags = '<a><br><i><em><u><p><h1><h2><h3><h4><h5><h6><ul><ol><li>',
			key = '',
			allowed = false,
			matches = [],
			allowed_array = [],
			allowed_tag = '',
			i = 0,
			k = '',
			html = '',
			replacer = function (search, replace, str) {
				return str.split(search).join(replace);
			};
		if (allowed_tags) {
			allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
		}
		str += '';

		matches = str.match(/(<\/?[\S][^>]*>)/gi);
		for (key in matches) {
			if (isNaN(key)) {
				// IE7
				continue;
			}

			html = matches[key].toString();
			allowed = false;

			for (k in allowed_array) { // Init
				allowed_tag = allowed_array[k];
				i = -1;

				if (i != 0) {
					i = html.toLowerCase().indexOf('<' + allowed_tag + '>');
				}
				if (i != 0) {
					i = html.toLowerCase().indexOf('<' + allowed_tag + ' ');
				}
				if (i != 0) {
					i = html.toLowerCase().indexOf('</' + allowed_tag);
				}

				// Determine
				if (i == 0) {
					allowed = true;
					break;
				}
			}
			if (!allowed) {
				str = replacer(html, "", str);
			}
		}

		o.content = str;
		return str;
	}

	function setup(editor) {

		editor.addButton('bulkmail_mce_button', {
			title: bulkmail_mce_button.l10n.tags.title,
			type: 'menubutton',
			icon: 'icon bulkmail-tags-icon',
			menu: $.map(bulkmail_mce_button.tags, function (group, id) {
				return {
					text: group.name,
					menu: $.map(group.tags, function (name, tag) {
						return {
							text: name,
							onclick: function () {
								var poststuff = '',
									selection;
								switch (tag) {
								case 'webversion':
								case 'unsub':
								case 'forward':
								case 'profile':
									poststuff = 'link';
								case 'homepage':
									if (selection = editor.selection.getContent({
											format: "text"
										})) {
										editor.insertContent('<a href="{' + tag + poststuff + '}">' + selection + '</a>');
										break;
									}
								default:
									editor.insertContent('{' + tag + '} ');
								}
							}
						};

					})
				};
			})
		});

		editor.addButton('bulkmail_remove_element', {
			title: bulkmail_mce_button.l10n.remove.title,
			icon: 'icon bulkmail-remove-icon',
			onclick: function () {
				editor.targetElm.remove();
				editor.remove();
				bulkmail.trigger('save');
			}
		});

		editor
			.on('change', function (event) {
				var _self = this;
				clearTimeout(changetimeout);
				changetimeout = setTimeout(function () {
					var content = event.level.content,
						c = content.match(/rgb\((\d+), ?(\d+), ?(\d+)\)/g);
					if (c) {
						for (var i = c.length - 1; i >= 0; i--) {
							content = content.replace(c[i], bulkmail.util.rgb2hex(c[i]));
						}
						_self.bodyElement.innerHTML = content;
					}
					bulkmail.trigger('save');
					change = true;
				}, 100)
			})
			.on('keyup', function (event) {
				$(event.currentTarget).prop('spellcheck', true);
			})
			.on('click', function (event) {
				var module = $(event.currentTarget).closest('module');
				if (bulkmail.modules.selected[0] != module[0]) {
					bulkmail.trigger('selectModule', module);
				}
				event.stopPropagation();
				editor.focus();
			})
			.on('focus', function (event) {
				event.stopPropagation();
				editor.selection.select(editor.getBody(), true);
				if (bulkmail.editor.$.container.data('uiSortable')) bulkmail.editor.$.container.sortable('destroy');
			})
			.on('blur', function (event) {
				bulkmail.trigger('refresh');
			});
	}

	function urlconverter(url, node, on_save, name) {
		if ('_wp_link_placeholder' == url) {
			return url;
		} else if (/^https?:\/\/{.+}/g.test(url)) {
			return url.replace(/^https?:\/\//, '');
		} else if (/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(url)) {
			return 'mailto:' + url;
		}
		return this.documentBaseURI.toAbsolute(url, bulkmaildata.tinymce.remove_script_host);
	}

	bulkmail.editor.updateElements = updateElements;
	bulkmail.editor.moduleButtons = moduleButtons;

	//bulkmail.events.push('refresh', refresh);
	bulkmail.events.push('editorLoaded', updateElements, moduleButtons);
	bulkmail.events.push('redraw', updateElements, moduleButtons);

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));
// end Modules


parent.window.bulkmail = bulkmail;