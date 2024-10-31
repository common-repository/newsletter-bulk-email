bulkmail = (function (bulkmail, $, window, document) {

	"use strict";

	if (typeof bulkmail_updates == 'undefined') {
		return;
	}

	var $output = $('#output'),
		$error = $('#error-list'),
		finished = [],
		current, current_i,
		skip = $('<span>&nbsp;</span><a class="skipbutton button button-small" href title="skip this step">skip</a>'),
		skipit = false,
		performance = bulkmail_updates_performance[0] || 1,
		keys = $.map(bulkmail_updates, function (element, index) {
			return index
		});

	$output.on('click', '.skipbutton', function () {
		skipit = true;
		return false;
	});

	bulkmail.$.document.ajaxError(function () {
		$error.append('script paused...continues in 5 seconds<br>');
		setTimeout(function () {
			$error.empty();
			run(current_i, true);
		}, 5000);
	});

	if (bulkmail_updates_options.autostart) {
		$('#bulkmail-update-process').show();
		run(0);
	} else {
		$('#bulkmail-update-info').show();
		$('#bulkmail-start-upgrade')
			.on('click', function () {
				$('#bulkmail-update-process').slideDown(200);
				$('#bulkmail-update-info').slideUp(200);
				run(0);
			});
	}

	function run(i, nooutput) {

		if (!i) {
			window.onbeforeunload = function () {
				return 'You have to finish the update before you can use Bulkmail!';
			};
		}

		var id = keys[i];

		current_i = i;

		if (!(current = bulkmail_updates[id])) {
			finish();
			return
		}

		if (!nooutput) output(id, '<strong>' + current + '</strong> ...', true);

		do_update(id, function () {
			setTimeout(function () {
				run(++i);
			}, 1000);
		}, function () {
			error();
		}, 1);

	}

	function do_update(id, onsuccess, onerror, round) {

		bulkmail.util.ajax('batch_update', {
			id: id,
			performance: performance
		}, function (response) {

			if (response && response.success) {

				if (response.output) textoutput(response.output);

				if (skipit) {
					output(id, ' &otimes;', false);
					skipit = false;
					onsuccess && onsuccess();
				} else if (response[id]) {
					output(id, ' &#10004;', false);
					onsuccess && onsuccess();
				} else {
					output(id, '.', false, round);
					setTimeout(function () {
						do_update(id, onsuccess, onerror, ++round)
					}, 5);
				}

			} else {
				onerror && onerror();
			}

		}, function (jqXHR, textStatus, errorThrown) {

			textoutput(jqXHR.responseText);
			alert('There was an error while doing the update! Please check the textarea on the right for more info!');
			error();

		});


	}

	function error() {

		window.onbeforeunload = null;

		output('error', 'error', true);

	}

	function finish() {

		window.onbeforeunload = null;

		output('finished', '<strong>Alright, all updates have been finished!</strong>', true, 0, true);
		output('finished_button', '<a href="admin.php?page=bulkmail_welcome" class="button button-primary">Ok, fine!</a>', true, 0, true);

		$('#bulkmail-post-upgrade').show();

	}

	function output(id, content, newline, round, nobox) {

		var el = $('#output_' + id).length ?
			$('#output_' + id) :
			$('<div id="output_' + id + '" class="' + (nobox ? '' : 'updated inline') + '" style="padding: 0.5em 6px;word-wrap: break-word;"></div>').appendTo($output);


		el.append(content);
		round > 20 ? el.append(skip.show()) : skip.hide();

	}

	function textoutput(content) {

		var curr_content = $('#textoutput').val();

		content = content + "\n\n" + curr_content;

		$('#textoutput').val($.trim(content));

	}

	return bulkmail;

}(bulkmail || {}, jQuery, window, document));