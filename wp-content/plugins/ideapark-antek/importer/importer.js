(function ($, root, undefined) {
	
	$(function () {
		"use strict";
		
		var importing = false;
		var request_cnt = 0;
		
		var ideapark_import_demo_check_options = function () {
			$('.ip-import-demo:checked').each(function () {
				var $this = $(this);
				if ($this.closest('.ip-demo').data('revslider') === 'yes') {
					$('.ip-rev-slider-radio').show();
				} else {
					$('.ip-rev-slider-radio').hide();
				}
			});
		};
		
		$('.ip-import-demo').on('change', ideapark_import_demo_check_options);
		
		ideapark_import_demo_check_options();
		
		$('#ip-import-submit').on('click', function (e) {
			if (importing) {
				alert(ideapark_wp_vars_importer.please_wait);
				e.preventDefault();
				return false;
			}
			
			if (confirm(ideapark_wp_vars_importer.are_you_sure)) {
				$('#ip-import-submit').attr('disabled', 'disabled');
				$('.ip-loading-progress').addClass('importing').slideDown();
				importing = true;
				var data = {
					action            : 'ideapark_importer',
					stage             : 'start',
					cnt               : request_cnt++,
					import_option     : $('#ip-import input[name=import_option]:checked').val(),
					import_attachments: $('#ip-import input[name=import_attachments]:checked').length ? 1 : 0,
					import_demo       : $('#ip-import input[name=import_demo]:checked').val()
				};
				ideaparkSendImportRequest(data);
			}
			e.preventDefault();
			return false;
		});
		
		$('#ip-export-submit').on('click', function () {
			if (!$('#ip-export-submit').is(':disabled')) {
				$('#ip-export-submit').attr('disabled', 'disabled');
				$('.ip-loading-progress').addClass('importing').slideDown();
				var data = {
					action: 'ideapark_exporter',
					stage : 'start',
					cnt   : request_cnt++
				};
				
				$('.ip-loading-info').html(ideapark_wp_vars_importer.progress + ': ' + 0);
				$('.ip-loading-state').width(0);
				
				ideaparkSendImportRequest(data);
				
				return false;
			}
		});
		
		
		function ideaparkSendImportRequest(data) {
			var orig_data = data;
			$.post(ajaxurl, data, function (response) {
				try {
					response = jQuery.parseJSON(response);
					if (response.code == 'completed') {
						ideaparkReturnStartState();
					} else if (response.code == 'continue') {
						var data = {
							action: orig_data['action'],
							stage : 'continue',
							cnt   : request_cnt++
							// XDEBUG_PROFILE: 1
						};
						ideaparkSendImportRequest(data);
					} else {
						$('.ip-import-output').html('<h3>' + ideapark_wp_vars_importer.output_error + ':</h3><div class="ip-import-error">' + response + '</div>');
						ideaparkReturnStartState();
					}
					$('.ip-loading-info').html(ideapark_wp_vars_importer.progress + ': ' + response.percent);
					$('.ip-import-output').html(response.msg + (response.code == 'completed' ? '' : ' ...'));
					$('.ip-loading-state').width(response.percent);
					
				} catch (err) {
					$('.ip-import-output').html('<h3>' + ideapark_wp_vars_importer.output_error + ':</h3><div class="ip-import-error">' + response + '</div>');
					ideaparkReturnStartState();
				}
				
			}).fail(function (jqXHR, textStatus, errorThrown) {
				$('.ip-import-output').html('<h3>' + ideapark_wp_vars_importer.output_error + ':</h3><div class="ip-import-error">' + jqXHR.statusText + '</div>' + (jqXHR.statusText == 'Not Found' ? '<div>Disable apache mod_security or configure it correctly</div>' : ''));
				ideaparkReturnStartState();
			});
		}
		
		function ideaparkReturnStartState() {
			importing = false;
			$('#ip-export-submit').removeAttr('disabled');
			$('#ip-import-submit').removeAttr('disabled');
		}
	});
	
})(jQuery, this);