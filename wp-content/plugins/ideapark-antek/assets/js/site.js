(function ($, root, undefined) {
	"use strict";
	
	var $ideapark_counters;
	
	$(function () {
		ideapark_defer_action_add(function () {
			$ideapark_counters = $('.js-counter-number');
			
			ideapark_init_slider_carousel();
			ideapark_start_counters();
			ideapark_init_steps_carousel();
			ideapark_init_reviews_carousel();
			ideapark_init_news_carousel();
			ideapark_init_product_carousel();
			ideapark_init_woocommerce_carousel();
			ideapark_init_accordion();
			
			ideapark_scroll_action_add(function () {
				ideapark_start_counters();
			});
			
			ideapark_resize_action_500_add(function () {
				ideapark_init_steps_carousel();
			});
		});
	});
	
	root.ideapark_init_slider_carousel = function () {
		
		$('.js-slider-carousel:not(.owl-carousel)')
			.each(function () {
				var $this = $(this);
				var autoplay = $this.data('autoplay') === 'yes';
				var animation = $this.data('animation');
				var animation_timeout = $this.data('animation-timeout');
				var dots = !$this.hasClass('h-carousel--dots-hide');
				var params = {
					items        : 1,
					center       : false,
					autoWidth    : false,
					margin       : 0,
					rtl          : ideapark_is_rtl,
					nav          : !$(this).hasClass('h-carousel--nav-hide'),
					dots         : dots,
					loop         : true,
					navText      : ideapark_nav_text,
					responsive   : {
						0  : {
							nav: 0
						},
						768: {
							nav: !$(this).hasClass('h-carousel--nav-hide'),
						}
					},
					onInitialized: function (event) {
						if ($(window).width() <= 767) {
							$('.c-ip-slider__image--mobile[loading="lazy"]').removeAttr('loading');
						} else {
							$('.c-ip-slider__image--desktop[loading="lazy"]').removeAttr('loading');
						}
					}
				};
				
				
				if (autoplay) {
					params.autoplay = true;
					params.autoplayTimeout = animation_timeout;
				}
				
				if (animation != '') {
					params.animateOut = animation + '-out';
					params.animateIn = animation + '-in';
				}
				$this
					.addClass('owl-carousel')
					.on('changed.owl.carousel', function (event) {
						if (autoplay) {
							$this.trigger('stop.owl.autoplay');
							$this.trigger('play.owl.autoplay');
						}
					})
					.owlCarousel(params);
				
			});
	};
	
	root.ideapark_start_counters = function (force) {
		
		if (force) {
			$ideapark_counters = $('.js-counter-number');
		}
		
		$ideapark_counters.each(function () {
			
			var $number = $(this),
				data = $number.data();
			
			if ($number.data('started')) {
				return;
			}
			
			if ($(window).scrollTop() > $number.offset().top - window.innerHeight) {
				
				var decimalDigits = data.toValue.toString().match(/\.(.*)/);
				
				if (decimalDigits) {
					data.rounding = decimalDigits[1].length;
				}
				
				$number.numerator(data).data('started', true);
			}
		});
	};
	
	root.ideapark_init_steps_carousel = function () {
		$('.js-steps').each(function () {
			var $widget = $(this);
			var $container = $('.js-steps-carousel', $widget);
			var $items = $('.c-ip-steps__item', $container);
			
			var container_divider = parseInt($container.data('divider'));
			var container_width = $container.innerWidth();
			if (!container_divider) {
				return;
			}
			$items.each(function () {
				var $this = $(this);
				$this.css({
					'width': Math.round(container_width / container_divider) + 'px'
				});
			});
			if ($items.length > 1) {
				if (!$container.hasClass('owl-carousel')) {
					var object = $container
						.addClass('owl-carousel')
						.owlCarousel({
							center   : false,
							autoWidth: true,
							loop     : false,
							margin   : 0,
							rtl      : ideapark_is_rtl,
							nav      : false,
							dots     : false,
						});
					$container.data('carousel', object);
				} else {
					$container.data('carousel').trigger('refresh.owl.carousel');
				}
			}
			
			if (!$container.hasClass('init')) {
				$container.addClass('init');
				$('.c-ip-steps__item', $widget).on('mouseenter', function () {
					var $item = $(this);
					var index = $item.data('index');
					var $desc = $('.c-ip-steps__description[data-index="' + index + '"]', $widget);
					$desc.css({
						left: Math.round($item.offset().left - $widget.offset().left + $item.outerWidth() / 2) + 'px'
					});
					$('.c-ip-steps__description--active', $widget).removeClass('c-ip-steps__description--active');
					$desc.addClass('c-ip-steps__description--active');
				}).on('mouseleave', function () {
					$('.c-ip-steps__description--active', $widget).removeClass('c-ip-steps__description--active');
				});
			}
			
		});
	};
	
	root.ideapark_init_reviews_carousel = function () {
		$('.js-reviews-carousel:not(.owl-carousel)').each(function () {
			var $this = $(this);
			var layout = $this.data('layout');
			if ($this.find('.c-ip-reviews__item').length > 1) {
				var params = {
					center       : false,
					autoWidth    : true,
					loop         : false,
					margin       : 30,
					nav          : !$this.hasClass('h-carousel--nav-hide'),
					dots         : !$this.hasClass('h-carousel--dots-hide'),
					navText      : ideapark_nav_text,
					onInitialized: ideapark_owl_hide_arrows,
					responsive   : {
						0  : {
							items    : 1,
							autoWidth: false,
						},
						768: {}
					},
				};
				if (layout === 'layout-1') {
					params.items = 1;
					params.autoWidth = false;
				}
				$this.addClass('owl-carousel')
					.on('resized.owl.carousel', ideapark_owl_hide_arrows)
					.owlCarousel(params)
					.on('changed.owl.carousel', function () {
						ideapark_owl_hide_arrows($this);
					});
			}
		});
	};
	
	root.ideapark_init_news_carousel = function () {
		$('.js-news-carousel:not(.owl-carousel)').each(function () {
			var $this = $(this);
			if ($this.find('.c-post-grid').length > 1) {
				$this.addClass('owl-carousel')
					.on('resized.owl.carousel', ideapark_owl_hide_arrows)
					.owlCarousel({
						center       : false,
						autoWidth    : true,
						loop         : false,
						margin       : 30,
						nav          : !$this.hasClass('h-carousel--nav-hide'),
						dots         : !$this.hasClass('h-carousel--dots-hide'),
						navText      : ideapark_nav_text,
						onInitialized: ideapark_owl_hide_arrows,
						responsive   : {
							0  : {
								items    : 1,
								autoWidth: false,
							},
							420: {}
						},
					})
					.on('changed.owl.carousel', function () {
						ideapark_owl_hide_arrows($this);
					});
			}
		});
	};
	
	root.ideapark_init_woocommerce_carousel = function () {
		
		$('.js-woocommerce-carousel:not(.owl-carousel)').each(function () {
			if ($('.product', $(this)).length) {
				$(this)
					.addClass('owl-carousel')
					.on('resized.owl.carousel', ideapark_owl_hide_arrows)
					.owlCarousel({
						center       : false,
						autoWidth    : true,
						loop         : false,
						margin       : 0,
						nav          : !$(this).hasClass('h-carousel--nav-hide'),
						dots         : !$(this).hasClass('h-carousel--dots-hide'),
						navText      : ideapark_nav_text,
						onInitialized: ideapark_owl_hide_arrows
					})
					.on('changed.owl.carousel', function () {
						$(this).find('.owl-nav,.owl-dots').removeClass('disabled');
					});
			}
		});
	};
	
	root.ideapark_init_product_filter = function () {
		$('.js-product-carousel-filter-select:not(.init)').addClass('init').on('change', function (e) {
			var $this = $(this);
			var $main = $this.closest('.c-ip-product-carousel');
			var id = $this.val();
			$('.js-product-carousel-filter[data-id="' + id + '"]', $main).trigger('click');
		});
		$('.js-product-carousel-filter:not(.init)').addClass('init').on('click', function (e) {
			e.preventDefault();
			var $this = $(this);
			if ($this.hasClass('active')) {
				return;
			}
			var $main = $this.closest('.c-ip-product-carousel');
			var $block = $main.find('.js-product-carousel');
			$block.addClass('c-ip-product-carousel__list--loading');
			$main.find('.c-ip-product-carousel__filter-item.active').removeClass('active');
			$this.closest('.c-ip-product-carousel__filter-item').addClass('active');
			$('.js-product-carousel-filter-select', $main).val($this.data('id'));
			$.ajax({
				url    : ideapark_wp_vars.ajaxUrl,
				type   : 'POST',
				data   : {
					action : 'ideapark_product_filter',
					id     : $this.data('id'),
					limit  : $this.data('limit'),
					sort   : $this.data('sort'),
					on_sale: $this.data('on-sale')
				},
				success: function (results) {
					$block.trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded owl-drag');
					$block.attr('class', $block.attr('class').replace(/c-ip-product-carousel__list--\d/, ''));
					$block.html(results);
					var count = $block.find('.c-vehicle,.c-vehicle-vert').length;
					$block.addClass('c-ip-product-carousel__list--' + count);
					$block.removeClass('c-ip-product-carousel__list--loading');
					if (count > 1) {
						ideapark_init_product_carousel();
					}
					if (typeof ideapark_init_zoom == 'function') {
						ideapark_init_zoom();
					}
				}
			});
		});
	};
	
	root.ideapark_init_product_carousel = function () {
		ideapark_init_product_filter();
		$('.js-product-carousel:not(.owl-carousel)').each(function () {
			var $this = $(this);
			var $items = $('.c-vehicle-vert', $this);
			if ($items.length > 1) {
				var min_height = 0;
				$items.each(function () {
					var height = $('.c-vehicle-vert__title', $(this)).outerHeight();
					if (height > min_height) {
						min_height = height;
					}
				});
				if (min_height > 28) {
					$('.c-vehicle-vert__title', $this).css({
						'min-height': min_height
					});
				}
				$this
					.addClass('owl-carousel')
					.on('resized.owl.carousel', ideapark_owl_hide_arrows)
					.owlCarousel({
						center       : false,
						autoWidth    : true,
						loop         : false,
						margin       : 0,
						nav          : !$this.hasClass('h-carousel--nav-hide'),
						dots         : !$this.hasClass('h-carousel--dots-hide'),
						navText      : ideapark_nav_text,
						onInitialized: ideapark_owl_hide_arrows
					})
					.on('changed.owl.carousel', function () {
						$this.find('.owl-nav,.owl-dots').removeClass('disabled');
					});
			}
		});
	};
	
	root.ideapark_init_accordion = function () {
		$('.js-accordion-title').on('click', function () {
			var $this = $(this);
			var $accordion = $this.closest('.c-ip-accordion');
			var $item = $this.closest('.c-ip-accordion__item');
			var $content = $item.find('.c-ip-accordion__content');
			var $old_item = $accordion.find('.c-ip-accordion__item--active');
			var is_active = $item.hasClass('c-ip-accordion__item--active');
			if ($old_item.length) {
				$old_item.removeClass('c-ip-accordion__item--active');
				$old_item.find('.c-ip-accordion__content').slideUp();
			}
			if (!is_active) {
				$content.slideDown();
				$item.addClass('c-ip-accordion__item--active');
			}
		});
	};
	
})(jQuery, window);