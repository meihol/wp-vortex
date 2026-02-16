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
class Ideapark_Elementor_Woocommerce_Carousel extends Widget_Base {

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
		return 'ideapark-woocommerce-carousel';
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
		return __( 'Woocommerce carousel', 'ideapark-antek' );
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
		return [ 'carousel', 'woocommerce', 'list' ];
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
				'label' => __( 'Woocommerce Carousel', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'type',
			[
				'label'   => __( 'Type', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'recent_products',
				'options' => $this->type_list()
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => __( 'Sort', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu_order',
				'options' => [
					''           => __( 'Default sorting', 'ideapark-antek' ),
					'rand'       => __( 'Random sorting', 'ideapark-antek' ),
					'date'       => __( 'Sort by date the product was published', 'ideapark-antek' ),
					'id'         => __( 'Sort by post ID of the product', 'ideapark-antek' ),
					'menu_order' => __( 'Sort by menu order', 'ideapark-antek' ),
					'popularity' => __( 'Sort by number of purchases', 'ideapark-antek' ),
					'rating'     => __( 'Sort by average product rating', 'ideapark-antek' ),
					'title'      => __( 'Sort by product title', 'ideapark-antek' ),
				]
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => __( 'Order', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ASC',
				'options' => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC',
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
					'{{WRAPPER}} .product' => 'background-color: {{VALUE}};',
				],
			]
		);

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
		$settings = $this->get_settings_for_display();
		$cat_id   = preg_match( '~^-\d+$~', $settings['type'] ) ? $cat_id = absint( $settings['type'] ) : 0;
		ob_start();
		?>
		<?php if ( $cat_id ) { ?>
			<?php echo do_shortcode( '[products category="' . $cat_id . '" limit="' . $settings['limit'] . '"' . ( $settings['orderby'] ? ' orderby="' . $settings['orderby'] . '" order="' . $settings['order'] . '"' : '' ) . ']' ); ?>
		<?php } else { ?>
			<?php echo do_shortcode( '[' . $settings['type'] . ' limit="' . $settings['limit'] . '"' . ( $settings['orderby'] ? ' orderby="' . $settings['orderby'] . '" order="' . $settings['order'] . '"' : '' ) . ']' ); ?>
		<?php } ?>
		<?php
		$content = ob_get_clean();
		preg_match_all( '~class="product ~', $content, $matches, PREG_SET_ORDER );
		$count   = sizeof( $matches );
		$content = str_replace( 'class="products ', 'class="products c-ip-woocommerce-carousel__list c-product-carousel__list--' . $count . ' js-woocommerce-carousel h-carousel h-carousel--default-dots h-carousel--flex' . ( $settings['arrows'] != 'yes' ? ' h-carousel--nav-hide' : '' ) . ( $settings['dots'] != 'yes' ? ' h-carousel--dots-hide' : ' c-ip-woocommerce-carousel__list--dots' ) . ' ', $content );
		echo ideapark_wrap( $content, '<div class="c-ip-woocommerce-carousel"><div class="c-ip-woocommerce-carousel__wrap' . ( $settings['dots'] == 'yes' ? ' c-ip-woocommerce-carousel__wrap--dots' : '' ) . ( $settings['arrows'] == 'yes' ? ' c-ip-woocommerce-carousel__wrap--nav' : '' ) . '">', '</div></div>' );
	}

	function type_list() {
		$list = [
			'recent_products'       => esc_html__( 'Recent Products', 'ideapark-antek' ),
			'featured_products'     => esc_html__( 'Featured Products', 'ideapark-antek' ),
			'sale_products'         => esc_html__( 'Sale Products', 'ideapark-antek' ),
			'best_selling_products' => esc_html__( 'Best-Selling Products', 'ideapark-antek' ),
		];

		$args = [
			'taxonomy'     => 'product_cat',
			'orderby'      => 'term_group',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 1,
			'title_li'     => '',
			'hide_empty'   => 0,
			'exclude'      => ideapark_mod( 'hide_uncategorized' ) ? get_option( 'default_product_cat' ) : null,
		];
		if ( $all_categories = get_categories( $args ) ) {

			$category_name   = [];
			$category_parent = [];
			foreach ( $all_categories as $cat ) {
				$category_name[ $cat->term_id ]    = esc_html( $cat->name );
				$category_parent[ $cat->parent ][] = $cat->term_id;
			}

			$get_category = function ( $parent = 0, $prefix = ' - ' ) use ( &$list, &$category_parent, &$category_name, &$get_category ) {
				if ( array_key_exists( $parent, $category_parent ) ) {
					$categories = $category_parent[ $parent ];
					foreach ( $categories as $category_id ) {
						$list[ '-' . $category_id ] = $prefix . $category_name[ $category_id ];
						$get_category( $category_id, $prefix . ' - ' );
					}
				}
			};

			$get_category();
		}

		return $list;
	}
}
