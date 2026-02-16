<?php

if ( ! function_exists( 'ideapark_setup_woocommerce' ) ) {
	function ideapark_setup_woocommerce() {

		if ( ( ideapark_is_requset( 'frontend' ) || ideapark_is_elementor_preview() ) && ideapark_woocommerce_on() ) {

			if ( ideapark_is_elementor_preview() ) {
				WC()->frontend_includes();
				WC()->initialize_session();
			}

			/* Product loop page */

			ideapark_ra( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			ideapark_ra( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			add_action( 'woocommerce_before_shop_loop', 'ideapark_wc_ordering', 30 );
			ideapark_ra( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

			add_action( 'woocommerce_before_shop_loop', 'ideapark_woocommerce_search_form', 25 );
			add_action( 'woocommerce_no_products_found', 'ideapark_woocommerce_search_form', 5 );

			add_filter( 'woocommerce_gallery_image_size', function (){return 'woocommerce_single';}, 99, 1 );

			add_filter('woocommerce_post_class', function($classes){
				$classes[] = 'product-image--' . ideapark_mod( 'grid_image_fit' );
				return $classes;
			});

			add_filter('product_cat_class', function($classes){
				$classes[] = 'product-image--' . ideapark_mod( 'subcat_image_fit' );
				return $classes;
			});

			add_action( 'woocommerce_before_subcategory_title', function () { ?><div class="product-thumb-wrap"><?php }, 9 );
			add_action( 'woocommerce_before_subcategory_title', function () { ?></div><?php }, 11 );

			add_action( 'woocommerce_before_shop_loop_item_title', function () { ?><div class="product-thumb-wrap"><?php }, 9 );
			add_action( 'woocommerce_before_shop_loop_item_title', function () { ?>
				<div class="product-image-overlay"></div><i
					class="ip-plus product-image-plus"></i></div><?php }, 15 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 20 );
			add_action( 'woocommerce_before_shop_loop_item_title', function () { ?><div class="product-content-wrap"><?php }, 25 );
			add_action( 'woocommerce_after_shop_loop_item', function () { ?></div><?php }, 9 );

			add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 9 );
			add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 11 );

			add_action( 'woocommerce_after_shop_loop_item_title', function () { ?><div class="product-price-wrap"><?php }, 8 );
		add_action( 'woocommerce_after_shop_loop_item_title', function () { ?>
			<div
				class="product-price-wrap-col"><?php echo ideapark_wrap( ideapark_mod( 'product_price_block_title' ), '<div class="product-price-total-title">', '</div>' ) . ideapark_wrap( ideapark_mod( 'product_price_block_tax' ), '<div class="product-price-total-tax">', '</div>' ); ?></div>
			<div class="product-price-wrap-col"><?php }, 8 );
			add_action( 'woocommerce_after_shop_loop_item_title', function () { ?></div></div><?php }, 11 );

			add_filter( 'woocommerce_loop_add_to_cart_link', function ( $text, $product ) {
				$text = str_replace( 'class="', 'class="c-button c-button--outline ', $text );
				if ( $product->is_type( 'simple' ) ) {
					$text = str_replace( '</a>', '<i class="ip-cart-button c-button__arrow c-button__arrow--cart"></i></a>', $text );
				} else {
					$text = str_replace( '</a>', '<i class="ip-double-arrow c-button__arrow"></i></a>', $text );
				}

				return $text;
			}, 99, 2 );

			add_filter( 'woocommerce_format_price_range', function ( $price, $from, $to ) {
				return '<span class="range">' . $price . '</span>';
			}, 99, 3 );

			add_filter( 'woocommerce_after_output_product_categories', function () { return '</ul><ul class="products">'; } );

			/* Product page */
			if ( ideapark_mod( 'product_title_in_header' ) ) {
				ideapark_ra( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			}

			add_action( 'woocommerce_product_thumbnails', function () {
				global $product;
				$product_id     = $product->get_id();
				$attachment_ids = $product->get_gallery_image_ids();
				if ( ! is_array( $attachment_ids ) ) {
					$attachment_ids = [];
				}
				if ( get_post_meta( $product_id, '_thumbnail_id', true ) ) {
					array_unshift( $attachment_ids, get_post_thumbnail_id( $product_id ) );
				}

				if ( $attachment_ids && $product->get_image_id() && sizeof( $attachment_ids ) > 1 ) { ?>
					</figure>
					<div
						class="c-product__thumbs h-carousel h-carousel--nav-hide h-carousel--dots-hide js-product-thumbs-carousel">
						<?php foreach ( $attachment_ids as $index => $attachment_id ) {
							$thumb = wp_get_attachment_image( $attachment_id, 'woocommerce_gallery_thumbnail', false, [
								'alt'   => get_the_title( $product_id ),
								'class' => 'c-product__thumbs-img'
							] );
							?>
							<?php echo sprintf( '<div class="c-product__thumbs-item ' . ( ! $index ? 'active' : '' ) . '"><button type="button" class="h-cb js-single-product-thumb wc-thumb" data-index="%s">%s</button></div>',
								$index,
								$thumb
							); ?>
						<?php } ?>
					</div>
					<figure class="h-hidden">
					<?php
				}
			}, 30 );

			add_filter( 'loop_shop_per_page', function () {
				return ideapark_mod( 'products_per_page' );
			} );

			/* Cart page */

			add_action( 'woocommerce_before_cart_totals', 'woocommerce_checkout_coupon_form', 10 );
			if ( filter_input( INPUT_SERVER, 'HTTP_X_REQUESTED_WITH' ) ) {
				ideapark_ra( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
				add_action( 'woocommerce_before_cart_table', 'woocommerce_output_all_notices', 10 );
			}

			/* Checkout page */
			ideapark_ra( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
			add_action( 'woocommerce_checkout_before_order_review', 'woocommerce_checkout_coupon_form', 10 );

			if ( ideapark_mod( 'store_notice_button_text' ) || ideapark_mod( 'store_notice_button_hide' ) ) {
				add_filter( "woocommerce_demo_store", function ( $notice ) {
					if ( ideapark_mod( 'store_notice_button_hide' ) ) {
						return preg_replace( "~<a href=\"#\" class=\"woocommerce-store-notice__dismiss-link\">[^>]+</a>~", '', $notice );
					} else {
						return preg_replace( "~(dismiss-link\">)([^>]+)(<)~", "\\1" . esc_html( ideapark_mod( 'store_notice_button_text' ) ) . "\\3", $notice );
					}
				} );
			}

			if ( ideapark_mod( 'hide_uncategorized' ) && get_option( 'default_product_cat' ) ) {
				$category_ids = [ get_option( 'default_product_cat' ) ];

				add_filter( 'get_terms_args', function ( $params ) use ( $category_ids ) {
					if ( ! is_admin() && $params['taxonomy'] == [ 'product_cat' ] ) {
						$params['exclude'] = implode( ',', $category_ids );
					}

					return $params;
				}, 20, 1 );

				add_filter( 'get_the_terms', function ( $terms, $post_ID, $taxonomy ) use ( $category_ids ) {
					if ( is_product() && $taxonomy == "product_cat" ) {
						foreach ( $terms as $key => $term ) {
							if ( in_array( $term->term_id, $category_ids ) ) {
								unset( $terms[ $key ] );
							}
						}
					}

					return $terms;
				}, 20, 3 );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_woocommerce_breadcrumbs' ) ) {
	function ideapark_woocommerce_breadcrumbs() {
		return [
			'delimiter'   => '',
			'wrap_before' => '<nav class="c-breadcrumbs"><ol class= "c-breadcrumbs__list">',
			'wrap_after'  => '</ol></nav>',
			'before'      => '<li class= "c-breadcrumbs__item c-breadcrumbs__item--wc">',
			'after'       => '</li>',
			'home'        => esc_html_x( 'Home', 'breadcrumb', 'antek' ),
		];
	}
}

if ( ! function_exists( 'ideapark_woocommerce_account_menu_items' ) ) {
	function ideapark_woocommerce_account_menu_items( $items ) {
		unset( $items['customer-logout'] );

		return $items;
	}
}

if ( ! function_exists( 'ideapark_remove_product_description_heading' ) ) {
	function ideapark_remove_product_description_heading() {
		return '';
	}
}

if ( ! function_exists( 'ideapark_woocommerce_search_form' ) ) {
	function ideapark_woocommerce_search_form() {
		if ( is_search() ) {
			echo '<div class="c-product-search-form">';
			get_search_form();
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'ideapark_woocommerce_pagination_args' ) ) {
	function ideapark_woocommerce_pagination_args( $args ) {
		$args['prev_text'] = '<i class="ip-double-arrow page-numbers__prev-ico"></i>';
		$args['next_text'] = '<i class="ip-double-arrow page-numbers__next-ico"></i>';
		$args['end_size']  = 1;
		$args['mid_size']  = 1;

		return $args;
	}
}

if ( ! function_exists( 'ideapark_add_to_cart_ajax_notice' ) ) {
	function ideapark_add_to_cart_ajax_notice( $product_id ) {
		wc_add_to_cart_message( $product_id );
	}
}

if ( ! function_exists( 'ideapark_excerpt_in_product_archives' ) ) {
	function ideapark_excerpt_in_product_archives() {
		if ( ideapark_mod( 'product_short_description' ) ) {
			?>
			<div class="woocommerce-loop-product__excerpt">
				<?php the_excerpt(); ?>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'ideapark_cart_item_thumbnail' ) ) {
	function ideapark_cart_item_thumbnail( $product_get_image, $cart_item, $cart_item_key ) {
		if ( empty( $cart_item['ideapark_antek'] ) ) {
			$_product          = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_get_image = $_product->get_image( 'woocommerce_gallery_thumbnail' );
		}

		return $product_get_image;
	}
}

if ( ! function_exists( 'ideapark_header_add_to_cart_fragment' ) ) {
	function ideapark_header_add_to_cart_fragment( $fragments ) {

		ob_start();
		wc_print_notices();
		$fragments['ideapark_notice'] = ob_get_clean();
		$fragments['.js-cart-info']   = ideapark_cart_info();

		return $fragments;
	}
}

if ( ! function_exists( 'ideapark_wc_product_post_class' ) ) {
	function ideapark_wc_product_post_class( $classes, $class = '', $post_id = 0 ) {
		if ( is_singular( [ 'product', 'product_variation' ] ) && $post_id == get_queried_object_id() ) {
			$classes[] = 'c-product';
			$classes[] = 'c-product--image-' . ideapark_mod( 'product_image_fit' );
		}

		return $classes;
	}
}


if ( ! function_exists( 'ideapark_related_products_args' ) ) {
	function ideapark_related_products_args( $args ) {
		$args['posts_per_page'] = 3;

		return $args;
	}
}

if ( ! function_exists( 'ideapark_author_box_gravatar_size' ) ) {
	function ideapark_author_box_gravatar_size( $size ) {
		return 90;
	}
}

if ( ! function_exists( 'ideapark_ajax_product_images' ) ) {
	function ideapark_ajax_product_images() {
		ob_start();
		if ( isset( $_REQUEST['product_id'] ) && ( $product_id = absint( $_REQUEST['product_id'] ) ) && ( $product = wc_get_product( $product_id ) ) ) {
			$variation_id   = isset( $_REQUEST['variation_id'] ) ? absint( $_REQUEST['variation_id'] ) : 0;
			$attachment_ids = $product->get_gallery_image_ids();
			$images         = [];
			if ( $variation_id && ( $attachment_id = get_post_thumbnail_id( $variation_id ) ) ) {
				array_unshift( $attachment_ids, $attachment_id );
			} else if ( $attachment_id = get_post_thumbnail_id( $product_id ) ) {
				array_unshift( $attachment_ids, $attachment_id );
			}
			foreach ( $attachment_ids as $attachment_id ) {
				$image    = wp_get_attachment_image_src( $attachment_id, 'full' );
				$images[] = [
					'src' => $image[0],
					'w'   => $image[1],
					'h'   => $image[2],
				];
			}

			if ( $video_url = get_post_meta( $product_id, '_ip_product_video_url', true ) ) {
				$images[] = [
					'html' => ideapark_wrap( wp_oembed_get( $video_url ), '<div class="pswp__video-wrap">', '</div>' )
				];
			}
			ob_end_clean();
			wp_send_json( [ 'images' => $images ] );
		}
		ob_end_clean();
	}
}

if ( ! function_exists( 'ideapark_product_id' ) ) {
	function ideapark_product_id() {
		global $post;
		?>
		<input type="hidden" class="js-product-id" value="<?php echo esc_attr( $post->ID ); ?>"/>
	<?php }
}

if ( ! function_exists( 'ideapark_product_sidebar_start' ) ) {
	function ideapark_product_sidebar_start() {
		if ( ideapark_mod( 'product_sidebar' ) && is_active_sidebar( 'product-sidebar' ) ) { ?>
			<div class="l-section__content l-section__content--with-sidebar l-section__content--st-width">
		<?php }
	}
}

if ( ! function_exists( 'ideapark_product_sidebar_end' ) ) {
	function ideapark_product_sidebar_end() {
		if ( ideapark_mod( 'product_sidebar' ) && is_active_sidebar( 'product-sidebar' ) ) { ?>
			</div>
			<div class="l-section__sidebar l-section__sidebar--right">
				<?php get_sidebar( 'product-page' ); ?>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'ideapark_sale_flash' ) ) {
	function ideapark_sale_flash( $html, $post, $product ) {
		return '<div class="onsale__wrap">' . $html . '</div>';
	}
}

if ( ! function_exists( 'ideapark_cart_info' ) ) {
	function ideapark_cart_info() {
		$cart_count = wc()->cart->get_cart_contents_count();

		return '<span class="js-cart-info">'
		       . ( ! wc()->cart->is_empty() ? ideapark_wrap( esc_html( $cart_count ), '<span class="c-header__cart-count js-cart-count">', '</span>' ) : '' )
		       . '</span>';
	}
}

if ( ! function_exists( 'ideapark_woocommerce_demo_store' ) ) {
	function ideapark_woocommerce_demo_store( $notice ) {
		return str_replace( 'woocommerce-store-notice ', 'woocommerce-store-notice woocommerce-store-notice--' . ideapark_mod( 'store_notice' ) . ' ', $notice );
	}
}

if ( ! function_exists( 'ideapark_remove_subtotal' ) ) {
	function ideapark_remove_subtotal( $totals ) {
		if ( ! ideapark_mod( 'show_subtotal' ) ) {
			unset( $totals['cart_subtotal'] );
		}

		return $totals;
	}
}

if ( ! function_exists( 'ideapark_wc_ordering' ) ) {
	function ideapark_wc_ordering( ) {
		if ( ! is_singular( 'product' ) && woocommerce_product_loop() ) { ?>
		<div class="c-catalog-ordering">
			<div class="c-catalog-ordering__col c-catalog-ordering__col--result">
				<?php woocommerce_result_count(); ?>
			</div>
			<div class="c-catalog-ordering__col c-catalog-ordering__col--ordering">
				<?php woocommerce_catalog_ordering(); ?>
			</div>
		</div>
		<?php }
	}
}

if ( ! function_exists( 'ideapark_wc_footer' ) ) {
	function ideapark_wc_footer( ) { ?>
		<div class="woocommerce-notices-wrapper woocommerce-notices-wrapper--ajax"></div>
		<?php
	}
}

if ( ! function_exists( 'ideapark_structured_data' ) ) {
	function ideapark_structured_data() {
		$new_types = [];
		if ( ideapark_woocommerce_on() && ( $structured_data = ideapark_mod( 'wc_structured_data' ) ) ) {
			foreach ( $structured_data as $type => $markup ) {
				WC()->structured_data->set_data( $markup );
				$type = strtolower( $type );
				if ( ! in_array( $type, $new_types ) ) {
					$new_types[] = $type;
				}
			}

			if ( $new_types ) {
				add_filter( 'woocommerce_structured_data_type_for_page', function ( $types ) use ( $new_types ) {
					return array_unique( array_merge( $types, $new_types ) );
				} );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_woocommerce_product_get_image' ) ) {
	function ideapark_woocommerce_product_get_image( $image, $product, $size, $attr, $placeholder ) {
		/**
		 * @var $product WC_Product
		 */
		if ( strpos( $image, 'woocommerce-placeholder' ) !== false && ( $video_thumb_id = get_post_meta( $product->get_id(), '_ip_product_video_thumb', true ) ) ) {
			$image = wp_get_attachment_image( $video_thumb_id, $size, false, $attr );
		}

		return $image;
	}
}

if ( IDEAPARK_IS_AJAX_IMAGES ) {
	add_action( 'wp_ajax_ideapark_product_images', 'ideapark_ajax_product_images' );
	add_action( 'wp_ajax_nopriv_ideapark_product_images', 'ideapark_ajax_product_images' );
} else {
	add_action( 'wp_loaded', 'ideapark_setup_woocommerce', 99 );
	add_action( 'woocommerce_ajax_added_to_cart', 'ideapark_add_to_cart_ajax_notice' );
	add_action( 'woocommerce_after_shop_loop_item_title', 'ideapark_excerpt_in_product_archives', 40 );
	add_action( 'woocommerce_before_single_product_summary', 'ideapark_product_id', 1 );
	add_action( 'woocommerce_before_single_product_summary', 'ideapark_product_sidebar_start', 2 );
	add_action( 'woocommerce_after_single_product_summary', 'ideapark_product_sidebar_end', 11 );

	add_filter( 'woocommerce_cart_item_thumbnail', 'ideapark_cart_item_thumbnail', 10, 3 );
	add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
	add_filter( 'woocommerce_show_page_title', '__return_false' );
	add_filter( 'woocommerce_product_additional_information_heading', '__return_false' );
	add_filter( 'woocommerce_show_variation_price', '__return_true' );
	add_filter( 'woocommerce_breadcrumb_defaults', 'ideapark_woocommerce_breadcrumbs' );
	add_filter( 'woocommerce_account_menu_items', 'ideapark_woocommerce_account_menu_items' );
	add_filter( 'woocommerce_product_description_heading', 'ideapark_remove_product_description_heading' );
	add_filter( 'woocommerce_pagination_args', 'ideapark_woocommerce_pagination_args' );
	add_filter( 'woocommerce_add_to_cart_fragments', 'ideapark_header_add_to_cart_fragment' );
	add_filter( 'woocommerce_output_related_products_args', 'ideapark_related_products_args', 20 );
	add_filter( 'post_class', 'ideapark_wc_product_post_class', 99, 3 );
	add_filter( 'genesis_author_box_gravatar_size', 'ideapark_author_box_gravatar_size' );
	add_filter( 'woocommerce_review_gravatar_size', 'ideapark_author_box_gravatar_size' );
	add_filter( 'woocommerce_sale_flash', 'ideapark_sale_flash', 10, 3 );
	add_filter( 'woocommerce_demo_store', 'ideapark_woocommerce_demo_store' );
	add_filter( 'woocommerce_get_order_item_totals', 'ideapark_remove_subtotal', 100, 1 );
	add_filter( 'woocommerce_product_get_image', 'ideapark_woocommerce_product_get_image', 10, 5 );

	add_action( 'wp_footer', 'ideapark_structured_data', 9 );
	add_action( 'wp_footer', 'ideapark_wc_footer' );
}

add_action( 'after_update_theme_late', function () {
	delete_transient( 'wc_system_status_theme_info' );
} );
add_action( 'woocommerce_page_wc-status', function () { // Fix WooCommerce bug
	if ( ! class_exists( 'WC_Plugin_Updates' ) && ideapark_is_file( WP_PLUGIN_DIR . '/woocommerce/includes/admin/plugin-updates/class-wc-plugin-updates.php' ) ) {
		include_once WP_PLUGIN_DIR . '/woocommerce/includes/admin/plugin-updates/class-wc-plugin-updates.php';
	}
}, 1 );