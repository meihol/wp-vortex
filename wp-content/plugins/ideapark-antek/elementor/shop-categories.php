<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Ideapark_Elementor_Shop_Categories extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve categories widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'ideapark-shop-categories';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve categories widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Shop Categories', 'ideapark-antek' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve icon widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'ip-image-box';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the categories widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @return array Widget categories.
	 * @since  2.0.0
	 * @access public
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
		return [ 'shop', 'categories' ];
	}

	/**
	 * Register categories widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_categories',
			[
				'label' => __( 'Categories', 'ideapark-antek' ),
			]
		);
		$list = [
			'0' => esc_html__( 'Shop', 'ideapark-antek' ),
		];

		$args = [
			'taxonomy'     => 'product_cat',
			'orderby'      => 'meta_value_num',
			'meta_key'     => 'order',
			'show_count'   => 0,
			'pad_counts'   => 0,
			'hierarchical' => 1,
			'title_li'     => '',
			'hide_empty'   => 0,
			'exclude'      => ideapark_mod( 'hide_uncategorized' ) ? get_option( 'default_product_cat' ) : null,
		];
		if ( $all_categories = get_categories( $args ) ) {

			$category_name   = [];
			$category_slug   = [];
			$category_parent = [];
			foreach ( $all_categories as $cat ) {
				$category_name[ $cat->term_id ]    = esc_html( $cat->name );
				$category_slug[ $cat->term_id ]    = $cat->slug;
				$category_parent[ $cat->parent ][] = $cat->term_id;
			}

			$get_category = function ( $parent = 0, $prefix = ' - ' ) use ( &$list, &$category_parent, &$category_name, &$category_slug, &$get_category ) {
				if ( array_key_exists( $parent, $category_parent ) ) {
					$categories = $category_parent[ $parent ];
					foreach ( $categories as $category_id ) {
						if ( array_key_exists( $category_id, $category_parent ) ) {
							$list[ '-' . $category_id ] = $prefix . $category_name[ $category_id ];
						}
						$get_category( $category_id, $prefix . ' - ' );
					}
				}
			};

			$get_category();
		}

		$this->add_control(
			'product_category',
			[
				'label'   => __( 'Product Category', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => $list,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render categories widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ideapark_woocommerce_on() ) {
			ideapark_catalog_categories( abs( $settings['product_category'] ) );
		}
	}
}
