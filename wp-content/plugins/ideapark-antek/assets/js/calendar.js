(function ($) {
	"use strict";
	
	$.migrateMute = true;
	$.migrateTrace = false;
	
	var ideapark_date_class = [];
	var ideapark_date_hint = [];
	var ideapark_date_hint_timeout;
	var ideapark_date_class_lock = false;
	var ideapark_date_first;
	var $post_id = $('#post_ID');
	var vehicle_id = $post_id.val();
	var dateFormat = ideapark_calendar_vars.dateFormat;
	moment.locale(ideapark_calendar_vars.locale);
	
	var ideapark_antek_empty = function (obj) {
		return typeof (obj) == 'undefined' || (typeof (obj) == 'object' && obj == null) || (typeof (obj) == 'array' && obj.length == 0) || (typeof (obj) == 'string' && ideapark_alltrim(obj) == '') || obj === 0;
	};
	
	var ideapark_antek_get_date_class = function (date, vehicle_id) {
		
		var date_text = moment(date).format("YYYY-MM-DD");
		if (!ideapark_date_first) {
			ideapark_date_first = date_text;
		}
		if (
			(vehicle_id in ideapark_date_class) &&
			!ideapark_antek_empty(ideapark_date_class[vehicle_id]) &&
			(date_text in ideapark_date_class[vehicle_id])) {
			return ideapark_date_class[vehicle_id][date_text];
		} else {
			if (ideapark_date_class_lock) {
				return;
			}
			$.ajax({
				url     : ajaxurl,
				type    : 'POST',
				async   : false,
				data    : {
					action    : 'ideapark_ajax_date_class',
					vehicle_id: vehicle_id,
					date      : date_text
				},
				dataType: 'json',
				success : function (result) {
					if (typeof result.dates === 'object') {
						ideapark_date_class[vehicle_id] = Object.assign({}, ideapark_date_class[vehicle_id], result.dates);
					}
					if (typeof result.hints === 'object') {
						ideapark_date_hint = Object.assign({}, ideapark_date_hint, result.hints);
					}
				}
			});
			if (typeof ideapark_date_class[vehicle_id] === 'object' && !ideapark_antek_empty(ideapark_date_class[vehicle_id]) && date_text in ideapark_date_class[vehicle_id]) {
				return ideapark_date_class[vehicle_id][date_text];
			} else {
				ideapark_date_class_lock = true;
				setTimeout(function () {
					ideapark_date_class_lock = false;
				}, 1000);
			}
		}
	};
	
	var $ideapark_hint;
	var ideapark_antek_hint = function (message, $td) {
		if ($ideapark_hint && !$td.hasClass('hint')) {
			$ideapark_hint.closest('.hint').removeClass('hint');
			$ideapark_hint.remove();
		}
		if ($td.hasClass('hint')) {
			return;
		}
		if (!message) {
			return;
		}
		
		$td.addClass('hint');
		
		if (ideapark_date_hint_timeout !== null) {
			clearTimeout(ideapark_date_hint_timeout);
			ideapark_date_hint_timeout = null;
		}
		var $html = $('<div class="ideapark-antek-hint"></div>');
		$html.append(message);
		$ideapark_hint = $html;
		$td.append($html);
		
		ideapark_date_hint_timeout = setTimeout(function () {
			ideapark_date_hint_timeout = null;
			if ($ideapark_hint) {
				$ideapark_hint.closest('.hint').removeClass('hint');
				$ideapark_hint.remove();
			}
		}, 3000);
	};
	
	var ideapark_antek_init_calendar = function () {
		var $booking = $('#ideapark-antek-booking');
		if (!$booking.length) {
			return;
		}
		
		$('.js-ideapark-antek-booking').remove();
		$booking.val('').before('<div class="ideapark-antek-booking js-ideapark-antek-booking"></div>');
		
		var $calendar = $('.js-ideapark-antek-booking');
		var av_debounce = ideapark_debounce(function () {
			$calendar.find('.table-condensed tbody td:not(.av-added)').each(function () {
				var $td = $(this);
				$td.addClass('.av-added');
				$td.append('<i></i>');
			});
		}, 10);
		
		var picker = $booking.daterangepicker({
			locale             : {
				format: dateFormat
			},
			parentEl           : ".js-ideapark-antek-booking",
			alwaysShowCalendars: true,
			autoApply          : true,
			autoUpdateInput    : false,
			isCustomDate       : function (data) {
				if (ideapark_calendar_vars.showAvailable) {
					av_debounce();
				}
				return ideapark_antek_get_date_class(data, vehicle_id);
			}
		});
		if (ideapark_calendar_vars.showAvailable) {
			picker.on('showCalendar.daterangepicker', function (ev, picker) {
				av_debounce();
			});
		}
		picker.on('apply.daterangepicker', function (ev, picker) {
			if (ideapark_calendar_vars.bookingType == 'night' && picker.startDate.isSame(picker.endDate, 'day')) {
				picker.endDate = picker.startDate.clone().add(1, 'day');
			}
			var range = picker.startDate.format(dateFormat) + ' — ' + picker.endDate.format(dateFormat);
			$("#ideapark-antek-booking").val(range).data('start', picker.startDate.format('YYYY-MM-DD')).data('end', picker.endDate.format('YYYY-MM-DD'));
		});
		picker.data('daterangepicker').hide = function (e) {
			var _this = picker.data('daterangepicker');
			if (!_this.endDate) {
				_this.startDate = _this.oldStartDate.clone();
				_this.endDate = _this.oldEndDate.clone();
			}
			if (!_this.startDate.isSame(_this.oldStartDate) || !_this.endDate.isSame(_this.oldEndDate))
				_this.callback(_this.startDate.clone(), _this.endDate.clone(), _this.chosenLabel);
			_this.updateElement();
			_this.element.trigger('hide.daterangepicker', _this);
		};
		$('.daterangepicker:not(.type-day):not(.type-night)').addClass('type-' + ideapark_calendar_vars.bookingType);
		picker.data('daterangepicker').show();
	};
	
	var ideapark_antek_init_edit_calendar = function () {
		
		$('.js-ideapark-edit-dates:not(.init)').each(function () {
			var $booking = $(this);
			var vehicle_id = $booking.data('vehicle_id');
			var order_id = $booking.data('order_id');
			var order_product_id = $booking.data('order_product_id');
			var startDate = moment($booking.data('start'));
			var startOriginal = $booking.data('start');
			var diff = parseInt($booking.data('diff'));
			var picker = $booking.daterangepicker({
				locale             : {
					format: dateFormat
				},
				opens              : "right",
				alwaysShowCalendars: true,
				startDate          : startDate.clone(),
				autoApply          : true,
				autoUpdateInput    : false,
				singleDatePicker   : true,
				isCustomDate       : function (data) {
					return ideapark_antek_get_date_class(data, vehicle_id);
				}
			});
			picker.on('apply.daterangepicker', function (ev, picker) {
				$.ajax({
					url     : ajaxurl,
					type    : 'POST',
					async   : false,
					data    : {
						action          : 'ideapark_ajax_date_action',
						button          : 'change',
						vehicle_id      : vehicle_id,
						order_id        : order_id,
						order_product_id: order_product_id,
						start           : picker.startDate.format(dateFormat),
						end             : picker.startDate.clone().add(diff, 'day').format(dateFormat),
						start_orig      : startOriginal
					},
					dataType: 'json',
					success : function (result) {
						if (result.error) {
							alert(result.error);
							picker.startDate = startDate.clone();
							picker.endDate = startDate.clone();
						}
						if (result.success) {
							document.location.reload(true);
						}
					}
				});
			});
			$('.daterangepicker:not(.type-day):not(.type-night)').addClass('type-' + ideapark_calendar_vars.bookingType);
			$booking.addClass('init');
		});
	};
	
	$('.ideapark-antek-booking-wrap').on('mouseover', 'td', function () {
		var $this = $(this);
		var hint_date = $this.attr('class').match(/date-(\d+-\d+-\d+)/);
		if (hint_date) {
			var date_text = hint_date[1];
			if (typeof ideapark_date_hint === 'object' && !ideapark_antek_empty(ideapark_date_hint) && date_text in ideapark_date_hint) {
				ideapark_antek_hint(ideapark_date_hint[date_text], $this);
			} else {
				ideapark_antek_hint('', $this);
			}
		} else {
			ideapark_antek_hint('', $this);
		}
	});
	$('#ideapark-antek-booking-reserve,#ideapark-antek-booking-clear').on('click', function () {
		var $this = $(this);
		var $input = $("#ideapark-antek-booking");
		var range = $input.val().trim();
		if (range === '') {
			alert(ideapark_calendar_vars.selectRangeMessage);
		} else {
			$('.ideapark-antek-booking-wrap').addClass('cal-loading');
			$.ajax({
				url     : ajaxurl,
				type    : 'POST',
				data    : {
					action    : 'ideapark_ajax_date_action',
					button    : $this[0].id === 'ideapark-antek-booking-reserve' ? 'reserve' : 'clear',
					vehicle_id: vehicle_id,
					start     : $input.data('start'),
					end       : $input.data('end')
				},
				dataType: 'json',
				success : function (result) {
					if (result.error) {
						$('.ideapark-antek-booking-wrap').removeClass('cal-loading');
						alert(result.error);
					}
					if (result.success) {
						ideapark_date_class = [];
						ideapark_date_hint = [];
						ideapark_date_class_lock = false;
						ideapark_antek_get_date_class(ideapark_date_first, vehicle_id);
						$('.ideapark-antek-booking-wrap').removeClass('cal-loading');
						ideapark_antek_init_calendar();
					}
				}
			});
		}
	});
	
	ideapark_antek_init_calendar();
	ideapark_antek_init_edit_calendar();
	
	$(document.body).on('order-totals-recalculate-complete', ideapark_antek_init_edit_calendar);
	
})(jQuery);