<?php
global $post;
$vehicle_id   = $post->ID;
$with_booking = ( ideapark_woocommerce_on() && ! ideapark_mod( 'disable_booking' ) );
$request_form = ! $with_booking && ideapark_mod( 'request_shortcode' ) && ideapark_mod( 'request_action' ) == 'popup';
$booking_link = ! $with_booking && ideapark_mod( 'request_link' ) && ideapark_mod( 'request_action' ) == 'link';

$layout       = ! empty( $ideapark_var['layout'] ) ? $ideapark_var['layout'] : ideapark_mod( 'catalog_layout' );
$hide_details = ! empty( $ideapark_var['hide_details'] );
$lang         = apply_filters( 'wpml_current_language', null );
$meta         = get_post_meta( $vehicle_id );
if ( function_exists( 'ideapark_set_details_transient' ) ) {
	$details = ideapark_set_details_transient( 'list' );
} else {
	$details = [];
}

foreach ( $details as $detail_slug => $detail ) {
	$detail_slug_new = $detail_slug;
	if ( $lang ) {
		$detail_slug_new = preg_replace( '~-' . $lang . '$~', '', $detail_slug_new );
	}
	$details[ $detail_slug_new ]['value'] = ! empty( $meta[ $detail_slug ] ) ? $meta[ $detail_slug ] : [];
	$details[ $detail_slug_new ]['text']  = isset( $meta[ $detail_slug ][0] ) && $meta[ $detail_slug ][0] !== '' && $meta[ $detail_slug ][0] !== null ? $meta[ $detail_slug ][0] : '';
}

$permalink = apply_filters( 'the_permalink', get_permalink() );
$args      = [ 'start', 'end', 'pickup', 'delivery' ];
foreach ( $args as $arg ) {
	if ( ! empty( $_REQUEST[ $arg ] ) ) {
		$permalink = add_query_arg( $arg, $_REQUEST[ $arg ], $permalink );
	}
}

if ( is_tax() && ( $term_ids = wp_cache_get( $vehicle_id, "vehicle_type_relationships" ) ) && is_array( $term_ids ) && sizeof( $term_ids ) > 1 ) {
	$permalink = add_query_arg( 'c', get_queried_object()->term_id, $permalink );
}

$is_hide_price  = ! empty( $ideapark_var['hide_price'] );
$original_price = 0;

if ( function_exists( 'ideapark_get_filter_dates_range' ) ) {
	$dates_range    = ideapark_get_filter_dates_range();
	$diff           = $dates_range['diff'];
	$delivery_price = ( ! empty( $dates_range['delivery'] ) || ideapark_mod( 'disable_self_pickup' ) ? ideapark_get_delivery_price( $vehicle_id, $dates_range['location_id'], true ) : null );
} else {
	$diff           = 1;
	$delivery_price = null;
}


if ( ! empty( $meta['price_on_request'][0] ) ) {
	$price_on_request = ideapark_mod( 'price_on_request_label' );
} else {
	$price = ! empty( $post->price_total ) ? $post->price_total : ( ! empty( $meta['price'][0] ) ? $meta['price'][0] : ( ! empty( $meta['price_week'][0] ) ? $meta['price_week'][0] : ( ! empty( $meta['price_month'][0] ) ? $meta['price_month'][0] : 0 ) ) );

	$original_price   = ! empty( $meta['price'][0] ) ? $diff * $meta['price'][0] + ( $delivery_price ?: 0 ) : 0;
	$price_on_request = false;
}
$is_favorites_list = isset( $_REQUEST['favorites'] );
$is_favorite       = ( $fav = ideapark_get_favorites() ) && array_key_exists( $vehicle_id, $fav );
ob_start();
if ( $price_on_request ) { ?>
	<span class="c-vehicle__total-request">
		<?php echo esc_html( $price_on_request ); ?>
	</span>
<?php } else {
	if ( ! empty( $meta['custom_price_text'][0] ) ) { ?>
		<?php echo esc_html( $meta['custom_price_text'][0] ); ?>
	<?php } elseif ( ideapark_woocommerce_on() && $price > 0 ) { ?>
		<?php if ( ideapark_mod( 'show_price_before_discount' ) && $original_price > $price ) { ?>
			<div class="c-vehicle__total-orig"><?php echo wc_price( $original_price ); ?></div>
		<?php } ?>
		<?php echo wc_price( $price ); ?>
	<?php }
}
$total_html = ob_get_clean();
if ( $total_html ) {
	ob_start(); ?>
	<div class="c-vehicle__total-wrap">
		<?php if ( ! $is_hide_price ) { ?>
			<?php if ( $layout == 'vert' ) { ?>
				<div class="c-vehicle__total-wrap-col">
			<?php } ?>
			<?php echo ideapark_wrap( ideapark_mod( 'price_block_title' ), '<div class="c-vehicle__total-title">', '</div>' ); ?>
			<?php echo ideapark_wrap( ideapark_mod( 'price_block_tax' ), '<div class="c-vehicle__total-tax">', '</div>' ); ?>
			<?php if ( $layout == 'vert' ) { ?>
				</div>
			<?php } ?>
			<?php echo ideapark_wrap( $total_html, '<div class="c-vehicle__total">', '</div>' ); ?>
		<?php } ?>
	</div>
	<?php
	$total_html = ob_get_clean();
}

if ( $layout == 'vert' ) {
	ob_start();
}
?>
	<div
		class="c-vehicle<?php if ( $is_favorite ) { ?> c-vehicle--favorite<?php } ?> <?php if ( ! $total_html ) { ?> c-vehicle--wide<?php } ?>"
		<?php if ( $is_favorites_list ) { ?>data-favorites-list="yes"<?php } ?>
		data-id="<?php echo esc_attr( $vehicle_id ); ?>" data-title="<?php echo esc_attr( get_the_title() ); ?>">
		<div class="c-vehicle__thumb-wrap">

			<a href="<?php echo esc_url( $permalink ) ?>" class="c-vehicle__link">
				<div class="c-vehicle__thumb-inner">
					<?php if ( has_post_thumbnail() ) { ?>
						<?php the_post_thumbnail( $layout == 'vert' ? 'medium_large' : 'ideapark-vehicle', [ 'class' => 'c-vehicle__thumb' ] ); ?>
						<?php if ( $layout == 'vert' ) { ?>
							<div class="c-vehicle__image-overlay"></div>
							<i class="ip-plus c-vehicle__plus"></i>
						<?php } ?>
					<?php } elseif ( $video_url = get_post_meta( $post->ID, 'video_url', true ) ) {
						if ( $video_thumb_id = get_post_meta( $post->ID, '_ip_product_video_thumb', true ) ) {
							$image_url = ( $image = wp_get_attachment_image_src( $video_thumb_id, $layout == 'vert' ? 'medium_large' : 'ideapark-vehicle' ) ) ? $image[0] : '';
						} else {
							$pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
							if ( preg_match( $pattern, $video_url, $match ) ) {
								$image_url = 'https://img.youtube.com/vi/' . $match[1] . '/hqdefault.jpg';
//								$thumb_url = 'https://img.youtube.com/vi/' . $match[1] . '/mqdefault.jpg';
							} else {
								$image_url = '';
							}
						}
						if ( $image_url ) {
							echo ideapark_wrap( '<img alt="" src="' . esc_url( $image_url ) . '" class="c-vehicle__thumb">' );
							if ( $layout == 'vert' ) { ?>
								<div class="c-vehicle__image-overlay"></div>
								<i class="ip-plus c-vehicle__plus"></i>
							<?php }
						}
					} ?>
				</div>
			</a>

			<?php if ( array_key_exists( 'sale', $details ) && ! empty( $meta['sale'][0] ) ) { ?>
				<div class="c-vehicle__sale" <?php if ( ! empty( $meta['sale_color'][0] ) ) {
					echo ideapark_bg( ideapark_mod_hex_color_norm( $meta['sale_color'][0] ) ) ?><?php } ?>>
					<?php echo esc_html( $meta['sale'][0] ); ?>
				</div>
			<?php } ?>

			<?php if ( $is_favorite ) { ?>
				<a href="<?php echo esc_url( add_query_arg( 'favorites', '', get_post_type_archive_link( 'catalog' ) ) ); ?>"
				   class="js-favorites">
					<i class="<?php echo( ideapark_mod( 'custom_product_icon_favorite' ) ?: 'ip-star' ); ?> c-vehicle__favorite-ico"></i>
				</a>
				<a href="" onclick="return false;" class="js-favorite-remove">
					<i class="ip-close c-vehicle__favorite-ico-remove"></i>
				</a>
			<?php } ?>

			<div class="c-vehicle__thumb-buttons">
				<?php if ( ! empty( $details['download']['value'][0] ) && ( $url = wp_get_attachment_url( $details['download']['value'][0] ) ) ) { ?>
					<?php
					$image       = get_post( $details['download']['value'][0] );
					$image_title = $image->post_title;
					if ( ! $image_title ) {
						$image_title = __( 'Attached File', 'antek' );
					}
					?>
					<a
						target="_blank"
						<?php if ( get_post_mime_type( $details['download']['value'][0] ) == 'application/pdf' ) { ?>data-vbtype="iframe"<?php } ?>
						class="c-vehicle__download" href="<?php echo esc_attr( $url ); ?>">
						<?php esc_html_e( 'View', 'antek' ); ?>&nbsp;<?php echo esc_html( $image_title ); ?>
					</a>
					<span class="c-vehicle__download-spacer"></span>
				<?php } ?>
				<a class="c-vehicle__download"
				   href="<?php echo esc_url( $permalink ) ?>"><?php esc_html_e( 'View Details', 'antek' ); ?></a>
			</div>
		</div>
		<div
			class="c-vehicle__content-wrap<?php if ( ! $total_html ) { ?> c-vehicle__content-wrap--wide<?php } ?>">
			<a href="<?php echo esc_url( $permalink ) ?>" class="c-vehicle__title-link">
				<div class="c-vehicle__title"><span class="c-vehicle__title-inner"><?php the_title(); ?></span></div>
			</a>
			<?php if ( $layout == 'vert' ) {
				echo ideapark_wrap( $total_html );
			} ?>
			<?php
			if ( ideapark_woocommerce_on() ) {
				ob_start();
				if ( ideapark_mod( 'price_type' ) == 'cond' ) {
					$price_postfix = ideapark_mod( 'booking_type' ) == 'day' ? esc_html__( 'Day', 'antek' ) : esc_html__( 'Night', 'antek' );
					if ( ! $price_on_request ) {
						if ( array_key_exists( 'price', $details ) ) {
							$price_per_day = $diff ? ( $price - ( $delivery_price ?: 0 ) ) / $diff : 0;
							?>
							<?php if ( $price_per_day > 0 ) { ?>
								<li class="c-vehicle__price"><?php echo wc_price( $price_per_day ); ?>
									/ <?php echo ideapark_wrap( $price_postfix ); ?></li>
							<?php } ?>
							<?php
						}
					} elseif ( ! empty( $details['price']['text'] ) ) { ?>
						<li class="c-vehicle__price"><?php echo wc_price( $details['price']['text'] ); ?>
							/ <?php echo ideapark_wrap( $price_postfix ); ?></li>
					<?php }
				} else {
					foreach (
						[
							'price'       => esc_html__( 'Day', 'antek' ),
							'price_week'  => esc_html__( 'Week', 'antek' ),
							'price_month' => esc_html__( 'Month', 'antek' ),
						] as $price_name => $price_postfix
					) {
						if ( array_key_exists( $price_name, $details ) && ! empty( $details[ $price_name ]['text'] ) ) {
							?>
							<li class="c-vehicle__price"><?php echo wc_price( $details[ $price_name ]['text'] ); ?>
								/ <?php echo ideapark_wrap( $price_postfix ); ?></li>
							<?php
						}
					}
				}
				$prices = ob_get_clean();
			} else {
				$prices = '';
			}

			?>

			<?php echo ideapark_wrap( $prices, '<ul class="c-vehicle__prices">', '</ul>' ); ?>
			<?php if ( ideapark_woocommerce_on() && array_key_exists( 'price_delivery', $details ) && $delivery_price !== null ) { ?>
				<div
					class="c-vehicle__price-delivery">
					<?php echo esc_html( ideapark_mod( 'delivery_title_list' ) ); ?><?php if ( $delivery_price > 0 ) { ?><!--
					-->: <?php echo wc_price( $delivery_price ); ?>
					<?php } ?>
				</div>
			<?php } ?>
			<?php if ( has_excerpt() ) { ?>
				<div class="c-vehicle__excerpt">
					<?php the_excerpt(); ?>
				</div>
			<?php } ?>
			<?php if ( $details && ! $hide_details ) {
				ob_start();
				foreach ( $details as $detail_slug => $detail ) {
					if ( in_array( $detail_slug, [
							'sale',
							'price',
							'price_week',
							'price_month',
							'price_delivery',
							'download',
						] ) || empty( $detail['value'] ) && ! in_array( $detail_slug, [
							'location',
							'vehicle_type'
						] ) ) {
						continue;
					}
					$text = esc_html( $detail['text'] );
					switch ( $detail_slug ) {
						case 'location':
							if ( $locations = get_the_terms( $vehicle_id, 'location' ) ) {
								$text = [];
								foreach ( $locations as $location ) {
									$text[] = $location->name;
								}
								$text = implode( ', ', $text );
							} else {
								continue 2;
							}
							break;
						case 'vehicle_type':
							if ( $types = get_the_terms( $vehicle_id, 'vehicle_type' ) ) {
								$text = [];
								foreach ( $types as $type ) {
									$text[] = $type->name;
								}
								$text = implode( ', ', $text );
							} else {
								continue 2;
							}
							break;
					}
					if ( $text !== '' ) { ?>
						<li class="c-vehicle__detail-item">
							<?php $name = apply_filters( 'wpml_translate_single_string', $detail['name'], IDEAPARK_SLUG, 'Details - ' . $detail['name'], apply_filters( 'wpml_current_language', null ) ); ?>
							<?php if ( $detail['unit'] ) { ?>
								<?php $unit = apply_filters( 'wpml_translate_single_string', $detail['unit'], IDEAPARK_SLUG, 'Details - ' . $detail['unit'], apply_filters( 'wpml_current_language', null ) ); ?>
								<?php $unit = ideapark_wrap( $unit, '<span class="c-vehicle__detail-unit">', '</span>' ); ?>
							<?php } else {
								$unit = '';
							} ?>
							<?php echo ideapark_wrap( esc_html( $name ), '<span class="c-vehicle__detail-name">', ':</span>' ); ?>
							<?php echo ideapark_wrap( esc_html( $text ), '<span class="c-vehicle__detail-value">', $unit . '</span>' ); ?>
						</li>
					<?php }
				}
				$content = ob_get_clean();
				echo ideapark_wrap( $content, '<ul class="c-vehicle__detail-list">', '</ul>' );
			} ?>
		</div>

		<div class="c-vehicle__booking-wrap<?php if ( ! $total_html ) { ?> c-vehicle__booking-wrap--wide<?php } ?>">

			<?php if ( $layout != 'vert' ) {
				echo ideapark_wrap( $total_html );
			} ?>

			<?php if ( ideapark_mod( 'reserve_button_title_list' ) ) { ?>
				<?php if ( ideapark_mod( 'request_button_behavior' ) == 'popup' && ( $request_form || $booking_link ) ) { ?>
					<?php ideapark_mod_set_temp( '_request_form', true ); ?>
					<?php if ( $request_form ) { ?>
						<a onclick="return false"
						   class="c-button c-button--default c-button--compact c-vehicle__button js-request-price">
							<?php echo esc_html( ideapark_mod( 'reserve_button_title_list' ) ); ?><?php if ( $layout == 'vert' ) { ?><!--
					--><i class="ip-double-arrow c-button__arrow"></i><?php } ?></a>
					<?php } elseif ( $booking_link ) { ?>
						<a
							href="<?php echo esc_url( ideapark_mod( 'request_link' ) . ( preg_match( '~\?~', ideapark_mod( 'request_link' ) ) ? '&' : '?' ) . '_n=' . rawurlencode( get_the_title() ) ); ?>"
							class="c-button c-button--default c-button--compact c-vehicle__button">
							<?php echo esc_html( ideapark_mod( 'reserve_button_title_list' ) ); ?><?php if ( $layout == 'vert' ) { ?><!--
					--><i class="ip-double-arrow c-button__arrow"></i><?php } ?></a>
					<?php } ?>
				<?php } else { ?>
					<a href="<?php echo esc_url( $permalink ) ?>"
					   class="c-button c-button--default c-button--compact c-vehicle__button">
						<?php echo esc_html( ideapark_mod( 'reserve_button_title_list' ) ); ?><?php if ( $layout == 'vert' ) { ?><!--
					--><i class="ip-double-arrow c-button__arrow"></i><?php } ?></a>
				<?php } ?>
			<?php } ?>
		</div>

	</div>
<?php
if ( $layout == 'vert' ) {
	echo str_replace( 'c-vehicle', 'c-vehicle-' . $layout, ob_get_clean() );
}