jQuery(document).ready(function ($) {

	"use strict"

	var l10n = bulkmail_mce_button.l10n,
		tags = bulkmail_mce_button.tags,
		designs = bulkmail_mce_button.designs,
		selection;

	tinymce.PluginManager.add('bulkmail_mce_button', function (editor, url) {
		editor.addButton('bulkmail_mce_button', {
			title: l10n.title,
			type: 'menubutton',
			icon: 'icon bulkmail-tags-icon',
			menu: $.map(tags, function (group, id) {
				return {
					text: group.name,
					menu: $.map(group.tags, function (name, tag) {
						return {
							text: name,
							onclick: function () {
								var poststuff = '';
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
	});
});