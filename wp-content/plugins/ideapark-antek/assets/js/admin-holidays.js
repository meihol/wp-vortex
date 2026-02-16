(function ($, root, undefined) {
	"use strict";
	
	(function (api) {
		if (api === undefined) {
			return;
		}
		
		api.bind('ready', function () {
			var dateFormat = ideapark_holidays_vars.dateFormat;
			var $booking = $('#_customize-input-holidays');
			var selected_date_text = moment().format('YYYY-MM-DD');
			
			$booking
				.val('')
				.before('<div class="ideapark-antek-booking ideapark-antek-holiday js-ideapark-antek-holidays"></div>')
				.after('<div class="ideapark-antek-holiday-buttons"><button class="button ideapark_holiday_remove_button" type="button" data-button="remove">Remove</button><button class="button ideapark_holiday_add_button" type="button" data-button="add">Add</button></div>');
			var picker = $booking.daterangepicker({
				locale             : {
					format: dateFormat
				},
				parentEl           : ".js-ideapark-antek-holidays",
				alwaysShowCalendars: true,
				autoApply          : true,
				autoUpdateInput    : false,
				singleDatePicker   : true,
				isCustomDate       : function (date) {
					var date_text = moment(date).format("YYYY-MM-DD");
					if (date_text in ideapark_holidays_vars.dateClass) {
						return 'date-' + date_text + ' ' + ideapark_holidays_vars.dateClass[date_text];
					} else {
						return 'date-' + date_text;
					}
				}
			});
			picker.on('apply.daterangepicker', function (ev, picker) {
				selected_date_text = picker.startDate.format("YYYY-MM-DD");
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
			picker.data('daterangepicker').show();
			
			$('.ideapark_holiday_remove_button, .ideapark_holiday_add_button').on('click', function () {
				var button = $(this).data('button');
				$.ajax({
					url     : ajaxurl,
					type    : 'POST',
					async   : false,
					data    : {
						action: 'ideapark_ajax_holiday_action',
						button: button,
						date  : selected_date_text
					},
					dataType: 'json',
					success : function (result) {
						if (result.success) {
							if (button === 'add') {
								$('.ideapark-antek-holiday .date-' + selected_date_text).addClass('holiday');
								ideapark_holidays_vars.dateClass[selected_date_text] = 'holiday'
							} else {
								$('.ideapark-antek-holiday .date-' + selected_date_text).removeClass('holiday');
								ideapark_holidays_vars.dateClass[selected_date_text] = '';
							}
						}
					}
				});
			});
			$booking.addClass('init');
		});
		
		// $(document).ready(function () {
		//
		// });
	})(wp.customize);
})(jQuery, window);