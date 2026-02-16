(function ($) {
	var fixHelper = function (e, ui) {
		ui.children().each(function () {
			$(this).width($(this).width());
		});
		return ui;
	};
	$('table.posts #the-list').sortable({
		'items' : 'tr',
		'axis'  : 'y',
		'helper': fixHelper,
		'update': function (e, ui) {
			$.post(ajaxurl, {
				action: 'update-post-order',
				order : $('#the-list').sortable('serialize'),
			});
		}
	});
	$('table.tags #the-list').sortable({
		'items' : 'tr',
		'axis'  : 'y',
		'helper': fixHelper,
		'update': function (e, ui) {
			$.post(ajaxurl, {
				action  : 'update-term-order',
				taxonomy: $('input[name="taxonomy"]').val(),
				order   : $('#the-list').sortable('serialize'),
			});
		}
	});
	
	if (typeof ideapark_sort_vars != 'undefined' && ideapark_sort_vars.notice) {
		$('#col-right .col-wrap').append('<div class="ideapark-sortable-notice">' + ideapark_sort_vars.notice + '</div>');
	}
	
})(jQuery);