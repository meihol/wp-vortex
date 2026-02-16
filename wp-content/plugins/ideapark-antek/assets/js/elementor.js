(function ($) {
	"use strict";
	$(window).on('elementor/frontend/init', function () {
		window.elementorFrontend.hooks.addAction('frontend/element_ready/ideapark-slider.default', function ($scope) {
			ideapark_init_slider_carousel();
		});
		window.elementorFrontend.hooks.addAction('frontend/element_ready/ideapark-counter.default', function ($scope) {
			ideapark_start_counters(true);
		});
		window.elementorFrontend.hooks.addAction('frontend/element_ready/ideapark-steps.default', function ($scope) {
			ideapark_init_steps_carousel();
		});
		window.elementorFrontend.hooks.addAction('frontend/element_ready/ideapark-reviews.default', function ($scope) {
			ideapark_init_reviews_carousel();
		});
		window.elementorFrontend.hooks.addAction('frontend/element_ready/ideapark-news.default', function ($scope) {
			ideapark_init_news_carousel();
		});
		window.elementorFrontend.hooks.addAction('frontend/element_ready/ideapark-product-carousel.default', function ($scope) {
			ideapark_init_product_carousel();
		});
		window.elementorFrontend.hooks.addAction('frontend/element_ready/ideapark-woocommerce-carousel.default', function ($scope) {
			ideapark_init_woocommerce_carousel();
		});
	});
	
})(jQuery);
