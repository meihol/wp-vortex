<?php

class Ideapark_Filter_Types_Widget extends WP_Widget {

	private $assets;

	function __construct() {

		$this->assets = trailingslashit( plugins_url( '/../assets/', __FILE__ ) );

		$widget_options = [
			'classname'   => 'c-filter-types',
			'description' => esc_html__( 'A widget that displays a Type filter', 'ideapark-antek' )
		];

		parent::__construct(
			'ideapark_types_filter_widget', esc_html__( 'Antek Type Filter', 'ideapark-antek' ), $widget_options
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

		if ( ! ( $wp_query->is_main_query() && ( $wp_query->is_post_type_archive( 'catalog' ) || $wp_query->is_tax( 'vehicle_type' ) ) ) ) {
			return;
		}

		$terms = get_terms( [
			'taxonomy'   => 'vehicle_type',
			'hide_empty' => false
		] );

		if ( ! $terms ) {
			return;
		}

		if ( ( $queried_object = get_queried_object() ) && isset( $queried_object->taxonomy ) && ( $queried_object->taxonomy == 'vehicle_type' ) ) {
			$current_vehicle_type_id = $queried_object->term_id;
		} else {
			$current_vehicle_type_id = false;
		}

		$parent_ids       = [];
		$active_parent_id = 0;

		foreach ( $terms as $term ) {
			if ( $term->parent && ! in_array( $term->parent, $parent_ids ) ) {
				$parent_ids[] = $term->parent;
			}
			if ( $term->parent && $term->term_id == $current_vehicle_type_id ) {
				$active_parent_id = $term->parent;
			}
		}

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		ob_start();

		?>
		<?php echo ideapark_wrap( esc_html( $title ), $before_title, $after_title ); ?>

		<div class="c-filter-types__wrap">
			<label class="c-filter-types__label">
					<span class="c-filter-types__radio-wrap">
						<input type="radio"
							   class="c-filter-types__radio js-type-all js-filter-type-widget js-filter-field"
							   value="<?php echo esc_attr( get_post_type_archive_link( 'catalog' ) ); ?>"
						       <?php if ( ! $current_vehicle_type_id ) { ?>checked<?php } ?>/>
						<i></i>
					</span>
				<?php esc_html_e( 'All types', 'ideapark-antek' ); ?>
			</label>
			<?php
			function sort_terms_hierarchically( array &$cats, array &$into, $parentId = 0 ) {
				foreach ( $cats as $i => $cat ) {
					if ( $cat->parent == $parentId ) {
						$into[] = $cat;
						unset( $cats[ $i ] );
						sort_terms_hierarchically( $cats, $into, $cat->term_id );
					}
				}
			}

			$terms_hierarchically = [];
			sort_terms_hierarchically( $terms, $terms_hierarchically );
			?>
			<?php foreach ( $terms_hierarchically as $term ) { ?>
				<?php $is_parent = in_array( $term->term_id, $parent_ids ); ?>
				<?php $is_child = $term->parent > 0; ?>
				<?php $is_active = $current_vehicle_type_id && ( $term->term_id == $current_vehicle_type_id || $term->parent == $current_vehicle_type_id || $term->parent == $active_parent_id ); ?>
				<label
					class="c-filter-types__label <?php ideapark_class( $is_parent, 'c-filter-types__label--parent' ); ?> <?php ideapark_class( $is_child, 'c-filter-types__label--child c-filter-types__label--' . $term->parent ); ?> <?php ideapark_class( $is_active, 'c-filter-types__label--active' ); ?>">
					<span class="c-filter-types__radio-wrap">
						<input type="radio"
							   data-id="<?php echo esc_attr( $term->term_id ); ?>"
							   class="c-filter-types__radio js-type js-filter-type-widget js-filter-field <?php ideapark_class( $is_parent, 'js-filter-type-widget-parent' ); ?>"
							   value="<?php echo esc_attr( get_term_link( $term->term_id, 'vehicle_type' ) ); ?>"
						       <?php if ( $term->term_id == $current_vehicle_type_id ) { ?>checked<?php } ?>/>
						<i></i>
					</span>
					<?php if ( $is_child ) { ?> &mdash; <?php } ?>
					<?php echo esc_html( $term->name ); ?>
				</label>
			<?php } ?>
		</div>

		<?php
		$inner_widget = ob_get_contents();
		ob_end_clean();
		echo ideapark_wrap( $inner_widget, $before_widget, $after_widget );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}


	function form( $instance ) {
		$defaults = [
			'title' => '',
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
				value="<?php echo esc_attr( $instance['title'] ); ?>"
				class="widefat"/>
		</p>
		<?php
	}
}

register_widget( 'Ideapark_Filter_Types_Widget' );