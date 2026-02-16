<?php

use Elementor\Control_Media;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor icon list widget.
 *
 * Elementor widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.0.0
 */
class Ideapark_Elementor_Product_Carousel extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve icon list widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'ideapark-product-carousel';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve icon list widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Product carousel', 'ideapark-antek' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve icon list widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'ip-pr-carousel';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 */
	public function get_categories() {
		return [ 'ideapark-elements' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @return array Widget keywords.
	 * @since  2.1.0
	 * @access public
	 *
	 */
	public function get_keywords() {
		return [ 'carousel', 'product', 'list' ];
	}

	/**
	 * Register icon list widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __( 'Product Carousel', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'sort',
			[
				'label'   => __( 'Sort', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu_order',
				'options' => [
					'menu_order' => __( 'Default sorting', 'ideapark-antek' ),
					'rand'       => __( 'Sort by rand', 'ideapark-antek' ),
					'date'       => __( 'Sort by latest', 'ideapark-antek' ),
					'price'      => __( 'Sort by price: low to high', 'ideapark-antek' ),
					'price-desc' => __( 'Sort by price: high to low', 'ideapark-antek' ),
				]
			]
		);

		$this->add_control(
			'limit',
			[
				'label'   => __( 'Products in carousel', 'ideapark-antek' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'step'    => 1,
				'default' => 6,
			]
		);

		$this->add_control(
			'filter',
			[
				'label'        => __( 'Category filter', 'ideapark-antek' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'ideapark-antek' ),
				'label_off'    => __( 'No', 'ideapark-antek' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'on_sale',
			[
				'label'        => __( 'Show only on sale', 'ideapark-antek' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'ideapark-antek' ),
				'label_off'    => __( 'No', 'ideapark-antek' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'details',
			[
				'label'   => __( 'Details', 'ideapark-antek' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'yes'     => __( 'Show', 'ideapark-antek' ),
				'no'      => __( 'Hide', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'arrows',
			[
				'label'   => __( 'Arrows', 'ideapark-antek' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'yes'     => __( 'Show', 'ideapark-antek' ),
				'no'      => __( 'Hide', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'dots',
			[
				'label'   => __( 'Navigation dots', 'ideapark-antek' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'yes'     => __( 'Show', 'ideapark-antek' ),
				'no'      => __( 'Hide', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .c-vehicle-vert' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'more_options',
			[
				'label'     => __( 'Categories', 'ideapark-antek' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$terms = get_terms( [
			'taxonomy'   => 'vehicle_type',
			'hide_empty' => false,
		] );

		foreach ( $terms as $term ) {
			$this->add_control(
				'product-' . $term->slug,
				[
					'label'        => $term->name,
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Show', 'ideapark-antek' ),
					'label_off'    => __( 'Hide', 'ideapark-antek' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				]
			);
		}

		$this->end_controls_section();
	}

	/**
	 * Render icon list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings     = $this->get_settings_for_display();
		$terms        = get_terms( [
			'taxonomy'   => 'vehicle_type',
			'hide_empty' => false,
		] );
		$is_all_terms = ( $settings['filter'] != 'yes' );
		$terms_id     = [];
		$terms_list   = [];
		foreach ( $terms as $term ) {
			if ( array_key_exists( 'product-' . $term->slug, $settings ) && $settings[ 'product-' . $term->slug ] == 'yes' ) {
				if ( $settings['filter'] != 'yes' || empty( $terms_id ) ) {
					$terms_id[] = $term->term_id;
				}
				$terms_list[ $term->term_id ] = $term->name;
			} else {
				$is_all_terms = false;
			}
		}

		$args = [
			'numberposts'      => $settings['limit'],
			'post_type'        => 'catalog',
			'suppress_filters' => false,
		];

		if ( ! $is_all_terms && ! empty( $terms_id ) ) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'vehicle_type',
					'field'    => 'term_id',
					'terms'    => $terms_id,
				]
			];
		}

		if ( $settings['on_sale'] == 'yes' ) {
			$args['meta_query'] = [
				[
					'key'     => 'sale',
					'value'   => '',
					'compare' => '!=',
				],
			];
		}

		switch ( $settings['sort'] ) {
			case 'menu_order':
				break;
			case 'rand':
				$args['orderby'] = 'rand';
				break;
			case 'date':
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				ideapark_mod_set_temp( '_disable_menu_order', true );
				break;
			case 'price':
				add_filter( 'posts_orderby', 'ideapark_filter_price_orderby_low', 99 );
				break;
			case 'price-desc':
				add_filter( 'posts_orderby', 'ideapark_filter_price_orderby_high', 99 );
				break;
		}

		$posts = get_posts( $args );
		ideapark_mod_set_temp( '_disable_menu_order', false );

		?>
		<div class="c-ip-product-carousel">
			<?php if ( $settings['filter'] == 'yes' && sizeof( $terms_list ) > 1 ) { ?>
				<?php $is_first = true; ?>
				<div class="c-ip-product-carousel__filter-wrap">
					<div class="c-ip-product-carousel__filter c-ip-product-carousel__filter--desktop">
						<?php foreach ( $terms_list as $term_id => $term_name ) { ?>
							<?php $link = get_term_link( $term_id, 'vehicle_type' ); ?>
							<?php if ( ! is_wp_error( $link ) ) { ?>
								<a class="c-ip-product-carousel__filter-item js-product-carousel-filter<?php if ( $is_first ) { ?> active<?php } ?>"
								   href="<?php echo esc_url( $link ); ?>"
								   data-id="<?php echo esc_attr( $term_id ); ?>"
								   data-limit="<?php echo esc_attr( $settings['limit'] ); ?>"
								   data-sort="<?php echo esc_attr( $settings['sort'] ); ?>"
								   data-on-sale="<?php echo esc_attr( $settings['on_sale'] ); ?>"><?php echo esc_html( $term_name ); ?></a>
							<?php } ?>
							<?php $is_first = false; ?>
						<?php } ?>
					</div>
					<span class="c-ip-product-carousel__filter c-ip-product-carousel__filter--mobile">
						<select class="h-cb js-product-carousel-filter-select">
							<?php foreach ( $terms_list as $term_id => $term_name ) { ?>
								<?php $link = get_term_link( $term_id, 'vehicle_type' ); ?>
								<?php if ( ! is_wp_error( $link ) ) { ?>
									<option <?php if ( $is_first ) { ?> selected<?php } ?> value="<?php echo esc_attr( $term_id ); ?>"><?php echo esc_html( $term_name ); ?>
									</option>
								<?php } ?>
								<?php $is_first = false; ?>
							<?php } ?>
						</select>
					</span>
				</div>
			<?php } ?>
			<div
				class="c-ip-product-carousel__wrap<?php if ( $settings['dots'] == 'yes' ) { ?> c-ip-product-carousel__wrap--dots<?php } ?> <?php if ( $settings['arrows'] == 'yes' ) { ?> c-ip-product-carousel__wrap--nav<?php } ?>">
				<div
					class="c-ip-product-carousel__list c-ip-product-carousel__list--<?php echo sizeof( $posts ); ?> js-product-carousel h-carousel h-carousel--default-dots h-carousel--flex<?php if ( $settings['arrows'] != 'yes' ) { ?> h-carousel--nav-hide<?php } ?><?php if ( $settings['dots'] != 'yes' ) { ?> h-carousel--dots-hide<?php } else { ?> c-ip-product-carousel__list--dots<?php } ?>">
					<?php global $post; ?>
					<?php foreach ( $posts as $post ) { ?>
						<?php setup_postdata( $post ) ?>
						<?php ideapark_get_template_part( 'templates/vehicle', [
								'layout'       => 'vert',
								'hide_details' => $settings['details'] !== 'yes'
							]
						) ?>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
		wp_reset_postdata();
	}
}
