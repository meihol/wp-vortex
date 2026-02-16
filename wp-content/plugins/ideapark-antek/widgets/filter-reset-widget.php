<?php

class Ideapark_Filter_Reset_Widget extends WP_Widget {

	private $assets;

	function __construct() {

		$this->assets = trailingslashit( plugins_url( '/../assets/', __FILE__ ) );

		$widget_options = [
			'classname'   => 'c-filter-reset',
			'description' => esc_html__( 'A widget that displays a filter reset button', 'ideapark-antek' )
		];

		parent::__construct(
			'ideapark_reset_filter_widget', esc_html__( 'Antek Reset Filter', 'ideapark-antek' ), $widget_options
		);
	}


	function widget( $args, $instance ) {
		/**
		 * @var string $before_widget
		 * @var string $before_title
		 * @var string $after_title
		 * @var string $after_widget
		 */
		global $wp_query;

		if ( ! ( $wp_query->is_main_query() && ( $wp_query->is_post_type_archive( 'catalog' ) || $wp_query->is_tax( 'vehicle_type' ) ) ) ) {
			return;
		}

		$is_hidden = ( ! ( isset( $_GET['reset'] ) || isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) );

		if ( $is_hidden && ( $details = ideapark_get_numeric_fields( true ) ) ) {
			foreach ( $details as $name => $data ) {
				if ( isset( $_GET[ 'min_' . $name ] ) || isset( $_GET[ 'max_' . $name ] ) ) {
					$is_hidden = false;
					break;
				}
			}
		}

		extract( $args );
		ob_start();
		?>

		<div class="c-filter-reset__wrap">
			<button type="button"
					class="c-button c-button--white c-button--full js-filter-reset"><?php esc_html_e( 'Reset all filters', 'ideapark-antek' ); ?></button>
		</div>

		<?php
		$inner_widget = ob_get_contents();
		ob_end_clean();

		if ( $is_hidden ) {
			$before_widget = str_replace( 'c-filter-reset', 'c-filter-reset h-hidden', $before_widget );
		}

		echo ideapark_wrap( $inner_widget, $before_widget, $after_widget );
	}
}

register_widget( 'Ideapark_Filter_Reset_Widget' );