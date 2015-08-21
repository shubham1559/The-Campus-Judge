/**
 * Sharif Judge
 * @file shj_submissions.js
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 *
 *     Javascript codes for "All Submissions" and "Final Submissions" pages
 */

shj.modal_open = false;
$(document).ready(function () {
	$(document).on('click', '#select_all', function (e) {
		e.preventDefault();
		$('.code-column').selectText();
	});
	$(".btn").click(function () {
		var button = $(this);
		var row = button.parents('tr');
		var type = button.data('type');
		if(type=='result') return;
		if (type == 'download') {
			window.location = shj.site_url + 'submissions/download_file/' + row.data('u') + '/' + row.data('a') + '/' + row.data('p') + '/' + row.data('s');
			return;
		}
		var view_code_request = $.ajax({
			cache: true,
			type: 'POST',
			url: shj.site_url + 'submissions/view_code',
			data: {
				type: type,
				username: row.data('u'),
				assignment: row.data('a'),
				problem: row.data('p'),
				submit_id: row.data('s'),
				shj_csrf_token: shj.csrf_token
			},
			success: function (data) {
				if (type == 'code')
					 data.text = shj.html_encode(data.text);
				$('.modal_inside').html('<pre class="code-column">'+data.text+'</pre>');
				$('.modal_inside').prepend('<p><code>'+data.file_name+' | Submit ID: '+row.data('s')+' | Username: '+row.data('u')+' | Problem: '+row.data('p')+'</code></p>');
				if (type == 'code'){
					$('pre.code-column').snippet(data.lang, {style: shj.color_scheme});
				}
				else
					$('pre.code-column').addClass('shj_code');
			}
		});
		if (!shj.modal_open) {
			shj.modal_open = true;
			$('#shj_modal').reveal(
				{
					animationspeed: 300,
					on_close_modal: function () {
						view_code_request.abort();
					},
					on_finish_modal: function () {
						$(".modal_inside").html('<div style="text-align: center;">Loading<br><img src="'+shj.base_url+'assets/images/loading.gif"/></div>');
						shj.modal_open = false;
					}
				}
			);
		}

	});
	$(".shj_details").attr('title', 'Show test case');
	shj.details_open=false;
	$(".shj_details").click(function () {
		var row = $(this).parents('tr');
		var view_test_request=$.ajax({
			cache:true,
			type: 'POST',
			url: shj.site_url + 'submissions/show_diff',
			data: {
				username: row.data('u'),
				assignment: row.data('a'),
				problem: row.data('p'),
				submit_id: row.data('s'),
				shj_csrf_token: shj.csrf_token
			},
			success: function (data) {
				$('.test_input').html('<pre class="code-column">'+data.input+'</pre>');
				$('.test_output').html('<pre class="code-column">'+data.output+'</pre>');
				$('.test_useroutput').html('<pre class="code-column">'+data.useroutput+'</pre>');
			$('.header').html('<p><code>Verdict : '+data.verdict+' | Test Case : '+data.wrong_at+' | Submit ID: '+row.data('s')+' | Username: '+row.data('u')+' | Problem: '+row.data('p')+'</code></p>');
			}
		});
		if (!shj.details_open) {
			shj.details_open = true;
			$('#test_modal').reveal(
				{
					animationspeed: 300,
					on_close_modal: function () {
						view_test_request.abort();
					},
					on_finish_modal: function () {
						$("#test_details").html('<div style="text-align: center;">Loading<br><img src="'+shj.base_url+'assets/images/loading.gif"/></div>');
						shj.details_open = false;
					}
				}
			);
		}
	});

	$(".shj_rejudge").attr('title', 'Rejudge');
	$(".shj_rejudge").click(function () {
		var row = $(this).parents('tr');
		$.ajax({
			type: 'POST',
			url: shj.site_url + 'rejudge/rejudge_single',
			data: {
				username: row.data('u'),
				assignment: row.data('a'),
				problem: row.data('p'),
				submit_id: row.data('s'),
				shj_csrf_token: shj.csrf_token
			},
			beforeSend: shj.loading_start,
			complete: shj.loading_finish,
			error: shj.loading_error,
			success: function (response) {
				if (response.done) {
					row.children('.status').html('<div class="btn pending" data-code="0">PENDING</div>');
					noty({text: 'Rejudge in progress', layout: 'bottomRight', type: 'success', timeout: 2500});
				}
				else
					shj.loading_failed(response.message);
			}
		});
	});


	$(".set_final").click(
		function () {
			var row = $(this).parents('tr');
			var submit_id = row.data('s');
			var problem = row.data('p');
			var username = row.data('u');
			$.ajax({
				type: 'POST',
				url: shj.site_url + 'submissions/select',
				data: {
					submit_id: submit_id,
					problem: problem,
					username: username,
					shj_csrf_token: shj.csrf_token
				},
				beforeSend: shj.loading_start,
				complete: shj.loading_finish,
				error: shj.loading_error,
				success: function (response) {
					if (response.done) {
						$("tr[data-u='" + username + "'][data-p='" + problem + "'] i.set_final").removeClass('fa-check-circle-o color11').addClass('fa-circle-o');
						$("tr[data-u='" + username + "'][data-p='" + problem + "'][data-s='" + submit_id + "'] i.set_final").removeClass('fa-circle-o').addClass('fa-check-circle-o color11');
					}
					else
						shj.loading_failed(response.message);
				}
			});
		}
	);
});
