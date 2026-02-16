<?php

class Ideapark_Filter_Price_Widget extends WP_Widget {

	private $assets;

	function __construct() {

		$this->assets = trailingslashit( plugins_url( '/../assets/', __FILE__ ) );

		$widget_options = [
			'classname'   => 'c-filter-price',
			'description' => esc_html__( 'A widget that displays a Price Filter', 'ideapark-antek' )
		];

		if ( ideapark_woocommerce_on() ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting' . $suffix . '.js', [ 'jquery' ], '0.4.2', true );
			wp_register_script( 'wc-jquery-ui-touchpunch', WC()->plugin_url() . '/assets/js/jquery-ui-touch-punch/jquery-ui-touch-punch' . $suffix . '.js', [ 'jquery-ui-slider' ], IDEAPARK_ANTEK_FUNC_VERSION, true );

			if ( is_customize_preview() ) {
				wp_enqueue_script( 'jquery-ui-slider' );
				wp_enqueue_script( 'accounting' );
				wp_enqueue_script( 'wc-jquery-ui-touchpunch' );
			}
		}

		parent::__construct(
			'ideapark_price_filter_widget', esc_html__( 'Antek Price Filter', 'ideapark-antek' ), $widget_options
		);
	}


	function widget( $args, $instance ) {
		/**
		 * @var string $before_widget
		 * @var string $before_title
		 * @var string $after_title
		 * @var string $after_widget
		 */
		global $wp_query, $wpdb;

		if ( ! ideapark_woocommerce_on() ) {
			return;
		}

		// Requires lookup table added in 3.6.
		if ( version_compare( get_option( 'woocommerce_db_version', null ), '3.6', '<' ) ) {
			return;
		}

		if ( ! ( $wp_query->is_main_query() && ( $wp_query->is_post_type_archive( 'catalog' ) || $wp_query->is_tax( 'vehicle_type' ) ) ) ) {
			return;
		}

		// If there are not posts and we're not filtering, hide the widget.
		if ( ! $wp_query->post_count && ! isset( $_GET['min_price'] ) && ! isset( $_GET['max_price'] ) ) { // WPCS: input var ok, CSRF ok.
			return;
		}

		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'accounting' );
		wp_enqueue_script( 'wc-jquery-ui-touchpunch' );

		// Round values to nearest 5 by default.
		$step = ! empty( $instance['step'] ) ? $instance['step'] : max( apply_filters( 'woocommerce_price_filter_widget_step', 5 ), 1 );

		if ( is_tax() ) {
			$queried_object = get_queried_object();
			$term_id        = $queried_object->term_id;
			$term_sql       = $wpdb->prepare( " INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = {$wpdb->posts}.ID AND tr.term_taxonomy_id = %d ", $term_id );
		} else {
			$term_sql = '';
		}

		$date_sql   = ideapark_filter_dates_join( '', null );
		$date_where = ideapark_filter_dates_where( '', null );

		$dates_range  = ideapark_get_filter_dates_range();
		$price_filter = ideapark_get_range_sql( $dates_range['start'], $dates_range['end'], $dates_range['delivery'], '', false, $dates_range['location_id'] );

		if ( ! empty( $_REQUEST['pickup'] ) && ( $location_id = abs( (int) $_REQUEST['pickup'] ) ) ) {
			$term_sql .= $wpdb->prepare( " INNER JOIN {$wpdb->term_relationships} tr_l ON tr_l.object_id = {$wpdb->posts}.ID AND tr_l.term_taxonomy_id = %d ", $location_id );
		}

		$prices = $wpdb->get_row( "
			SELECT min( t1.price_total ) as min_price, MAX( t1.price_total ) as max_price
			FROM ( SELECT $price_filter AS price_total FROM {$wpdb->posts} {$term_sql} {$date_sql} WHERE 1 {$date_where} AND {$wpdb->posts}.post_status = 'publish' AND {$wpdb->posts}.post_type = 'catalog' ) AS t1
			" );

		if ( ! $prices ) {
			return;
		}
		$min_price = $prices->min_price;
		$max_price = $prices->max_price;

		// Check to see if we should add taxes to the prices if store are excl tax but display incl.
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

		if ( wc_tax_enabled() && ! wc_prices_include_tax() && 'incl' === $tax_display_mode ) {
			$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
			$tax_rates = WC_Tax::get_rates( $tax_class );

			if ( $tax_rates ) {
				$min_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $min_price, $tax_rates ) );
				$max_price += WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $max_price, $tax_rates ) );
			}
		}

		// If both min and max are equal, we don't need a slider.
		if ( $min_price === $max_price ) {
			return;
		}

		$min_price = apply_filters( 'woocommerce_price_filter_widget_min_amount', floor( $min_price / $step ) * $step );
		$max_price = apply_filters( 'woocommerce_price_filter_widget_max_amount', ceil( $max_price / $step ) * $step );

		// If both min and max are equal, we don't need a slider.
		if ( $min_price === $max_price ) {
			return;
		}

		$current_min_price = isset( $_GET['min_price'] ) ? floor( floatval( wp_unslash( $_GET['min_price'] ) ) / $step ) * $step : $min_price;
		$current_max_price = isset( $_GET['max_price'] ) ? ceil( floatval( wp_unslash( $_GET['max_price'] ) ) / $step ) * $step : $max_price;

		extract( $args );

		$title = ! empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';

		ob_start();

		?>

		<?php echo ideapark_wrap( esc_html( $title ), $before_title, $after_title ); ?>

		<div class="c-filter-price__wrap js-filter-price" data-slug="price">
			<div class="c-filter-price__slider js-filter-price-slider" style="display:none;"></div>
			<div class="js-filter-price-amount" data-step="<?php echo esc_attr( $step ); ?>">
				<input type="hidden" class="js-filter-price-min_price js-filter-field" name="min_price"
					   value="<?php echo esc_attr( $current_min_price ); ?>"
					   data-min="<?php echo esc_attr( $min_price ); ?>"/>
				<input type="hidden" class="js-filter-price-max_price js-filter-field" name="max_price"
					   value="<?php echo esc_attr( $current_max_price ); ?>"
					   data-max="<?php echo esc_attr( $max_price ); ?>"/>
				<div class="c-filter-price__label js-filter-price-label" style="display:none;">
					<?php echo sprintf( esc_html__( "Range from %s to %s", 'ideapark-antek' ), '<span class="from"></span>', '<span class="to"></span>' ); ?>
				</div>
			</div>
		</div>

		<?php
		$inner_widget = ob_get_contents();
		ob_end_clean();
		echo ideapark_wrap( $inner_widget, $before_widget, $after_widget );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['step']  = preg_replace( '~[^\.0-9]~', '', strip_tags( $new_instance['step'] ) );

		return $instance;
	}


	function form( $instance ) {
		$defaults = [
			'title' => '',
			'step'  => max( apply_filters( 'woocommerce_price_filter_widget_step', 5 ), 1 ),
		];
		$steps    = [
			'0.1',
			'1',
			'5',
			'10',
			'100',
			'1000',
		];
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'ideapark-antek' ); ?></label>
			<input
				type="text"
				class="widefat"
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				value="<?php echo esc_attr( $instance['title'] ); ?>"/>
		</p>
		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'step' ) ); ?>"><?php esc_html_e( 'Slider step:', 'ideapark-antek' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'step' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'step' ) ); ?>" class="widefat">
				<option
					value="" <?php if ( ! $instance['step'] ) { ?> selected<?php } ?>><?php esc_html_e( '- select step -', 'ideapark-antek' ); ?></option>
				<?php foreach ( $steps as $step ) { ?>
					<option
						value="<?php echo esc_attr( $step ); ?>" <?php if ( $step == $instance['step'] ) { ?> selected<?php } ?>><?php echo esc_html( $step ); ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
	}
}

register_widget( 'Ideapark_Filter_Price_Widget' );