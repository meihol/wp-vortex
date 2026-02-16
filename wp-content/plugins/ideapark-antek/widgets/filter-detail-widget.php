<?php

class Ideapark_Filter_Detail_Widget extends WP_Widget {

	private $assets;

	function __construct() {

		$this->assets = trailingslashit( plugins_url( '/../assets/', __FILE__ ) );

		$widget_options = [
			'classname'   => 'c-filter-price',
			'description' => esc_html__( 'A widget that displays a numeric detail filter', 'ideapark-antek' )
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
			'ideapark_detail_filter_widget', esc_html__( 'Antek Detail Filter', 'ideapark-antek' ), $widget_options
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

		$detail_slug = ! empty( $instance['detail'] ) ? $instance['detail'] : '';

		if ( ! ( $wp_query->is_main_query() && ( $wp_query->is_post_type_archive( 'catalog' ) || $wp_query->is_tax( 'vehicle_type' ) ) ) ) {
			return;
		}

		// If there are not posts and we're not filtering, hide the widget.
		if ( ! $wp_query->post_count && ! isset( $_GET[ 'min_' . $detail_slug ] ) && ! isset( $_GET[ 'max_' . $detail_slug ] ) ) { // WPCS: input var ok, CSRF ok.
			return;
		}


		$numeric_details = ideapark_get_numeric_fields( true );

		if ( ! $detail_slug || ! array_key_exists( $detail_slug, $numeric_details ) ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'accounting' );
		wp_enqueue_script( 'wc-jquery-ui-touchpunch' );

		$step = ! empty( $instance['step'] ) ? $instance['step'] : 1;

		if ( is_tax() ) {
			$queried_object = get_queried_object();
			$term_id        = $queried_object->term_id;
			$term_sql       = $wpdb->prepare( " INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = {$wpdb->posts}.ID AND tr.term_taxonomy_id = %d ", $term_id );
		} else {
			$term_sql = '';
		}

		$dates    = ideapark_get_filter_dates_range();
		$start    = $dates['start']->format( 'Y-m-d' );
		$end      = $dates['end']->format( 'Y-m-d' );
		$date_sql = $wpdb->prepare( "
			LEFT JOIN {$wpdb->prefix}antek_order ao
			ON ( ao.vehicle_id={$wpdb->posts}.ID AND " . ideapark_mysql_dates_cond() . ( ideapark_mod( 'unlimited_booking' ) ? " AND order_id IS NULL " : "" ) . " )",
			$start, $start, $end, $end, $start, $end );

		$detail_sql = $wpdb->prepare( "
			INNER JOIN {$wpdb->prefix}antek_filter af
			ON af.vehicle_id={$wpdb->posts}.ID AND af.field = %s
		", $detail_slug );

		$values = $wpdb->get_row( "
			SELECT min( af.value ) as min_value, MAX( af.value ) as max_value
			FROM {$wpdb->posts} {$detail_sql} {$term_sql} {$date_sql} WHERE ao.vehicle_id IS NULL AND {$wpdb->posts}.post_status = 'publish' AND {$wpdb->posts}.post_type = 'catalog'" );

		if ( ! $values ) {
			return;
		}
		$min_value = $values->min_value;
		$max_value = $values->max_value;

		if ( $min_value === $max_value ) {
			return;
		}
		$unit = ! empty( $numeric_details[ $detail_slug ]->meta['unit'][0] ) ? ' ' . $numeric_details[ $detail_slug ]->meta['unit'][0] : '';

		$current_min_value = isset( $_GET[ 'min_' . $detail_slug ] ) ? floor( floatval( wp_unslash( $_GET[ 'min_' . $detail_slug ] ) ) / $step ) * $step : $min_value;
		$current_max_value = isset( $_GET[ 'max_' . $detail_slug ] ) ? ceil( floatval( wp_unslash( $_GET[ 'max_' . $detail_slug ] ) ) / $step ) * $step : $max_value;

		extract( $args );

		$title = ! empty( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		ob_start();
		?>

		<?php echo ideapark_wrap( esc_html( $title ), $before_title, $after_title ); ?>

		<div class="c-filter-price__wrap js-filter-price" data-slug="<?php echo esc_attr( $detail_slug ); ?>">
			<div class="c-filter-price__slider js-filter-price-slider" style="display:none;"></div>
			<div class="js-filter-price-amount" data-step="<?php echo esc_attr( $step ); ?>">
				<input type="hidden" class="js-filter-price-min_price js-filter-field"
					   name="min_<?php echo esc_attr( $detail_slug ); ?>"
					   value="<?php echo esc_attr( $current_min_value ); ?>"
					   data-min="<?php echo esc_attr( $min_value ); ?>"/>
				<input type="hidden" class="js-filter-price-max_price js-filter-field"
					   name="max_<?php echo esc_attr( $detail_slug ); ?>"
					   value="<?php echo esc_attr( $current_max_value ); ?>"
					   data-max="<?php echo esc_attr( $max_value ); ?>"/>
				<div class="c-filter-price__label js-filter-price-label" style="display:none;">
					<?php echo sprintf( esc_html__( "Range from %s to %s", 'ideapark-antek' ) . esc_html( $unit ), '<span class="from"></span>', '<span class="to"></span>' ); ?>
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

		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['detail'] = trim( strip_tags( $new_instance['detail'] ) );
		$instance['step']   = preg_replace( '~[^\.0-9]~', '', strip_tags( $new_instance['step'] ) );

		return $instance;
	}


	function form( $instance ) {
		$defaults = [
			'title'  => '',
			'detail' => '',
			'step'   => '',
		];
		$details  = ideapark_get_numeric_fields( true );
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
				id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat"/>
		</p>
		<p>
			<label
				for="<?php echo esc_attr( $this->get_field_id( 'detail' ) ); ?>"><?php esc_html_e( 'Filter by detail (numeric type only):', 'ideapark-antek' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'detail' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'detail' ) ); ?>" class="widefat">
				<option
					value="" <?php if ( ! $instance['detail'] ) { ?> selected<?php } ?>><?php esc_html_e( '- select detail -', 'ideapark-antek' ); ?></option>
				<?php foreach ( $details as $term_slug => $term ) {
					?>
					<option
						value="<?php echo esc_attr( $term_slug ); ?>" <?php if ( $term_slug == $instance['detail'] ) { ?> selected<?php } ?>><?php echo esc_html( $term->name ); ?></option>
				<?php } ?>
			</select>
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

register_widget( 'Ideapark_Filter_Detail_Widget' );