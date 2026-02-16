<?php

use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor news  widget.
 *
 * Elementor widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.0.0
 */
class Ideapark_Elementor_News extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve news  widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'ideapark-news';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve news  widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'News', 'ideapark-antek' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve news  widget icon.
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
		return [ 'carousel', 'grid', 'news' ];
	}

	/**
	 * Register news  widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_category',
			[
				'label' => __( 'News', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'count',
			[
				'label'   => __( 'News count', 'ideapark-antek' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'default' => 3,
				'step'    => 1,
			]
		);

		$options = [ 0 => __( 'All', 'ideapark-antek' ) ];
		if ( $categories = get_categories() ) {
			foreach ( $categories as $category ) {
				$options[ $category->term_id ] = $category->name;
			}
		}
		$this->add_control(
			'category',
			[
				'label'   => __( 'Category', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 0,
				'options' => $options
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'grid',
				'options' => [
					'grid'     => __( 'Grid', 'ideapark-antek' ),
					'carousel' => __( 'Carousel', 'ideapark-antek' )
				]
			]
		);

		$this->add_control(
			'arrows',
			[
				'label'     => __( 'Arrows', 'ideapark-antek' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'yes'       => __( 'Show', 'ideapark-antek' ),
				'no'        => __( 'Hide', 'ideapark-antek' ),
				'condition' => [
					'layout' => 'carousel',
				],
			]
		);

		$this->add_control(
			'dots',
			[
				'label'     => __( 'Navigation dots', 'ideapark-antek' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
				'yes'       => __( 'Show', 'ideapark-antek' ),
				'no'        => __( 'Hide', 'ideapark-antek' ),
				'condition' => [
					'layout' => 'carousel',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .c-post-grid' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render news  widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  .0.0
	 * @access protected
	 */
	protected function render() {
		global $post;
		$settings = $this->get_settings();
		$args     = [
			'numberposts'      => $settings['count'],
			'suppress_filters' => false
		];
		if ( ! empty( $settings['category'] ) ) {
			$args['category'] = $settings['category'];
		}
		$news = get_posts( $args );
		if ( ! $news ) {
			return;
		}
		$old_sidebar_blog = ideapark_mod( 'sidebar_blog' );
		$old_post_layout  = ideapark_mod( 'post_layout' );
		ideapark_mod_set_temp( 'sidebar_blog', false );
		ideapark_mod_set_temp( 'post_layout', 'grid' );
		$without_thumbnails = true;
		foreach ( $news as $index => $post ) {
			if ( has_post_thumbnail( $post ) ) {
				$without_thumbnails = false;
				break;
			}
		}
		?>
		<div class="c-ip-news">
			<div
				class="c-ip-news__wrap c-ip-news__wrap--<?php echo $settings['layout']; ?> c-ip-news__wrap--<?php echo sizeof( $news ); ?> <?php if ( $settings['layout'] == 'carousel' && $settings['dots'] == 'yes' ) { ?> c-ip-news__wrap--dots<?php } ?> <?php if ( $settings['layout'] == 'carousel' && $settings['arrows'] == 'yes' ) { ?> c-ip-news__wrap--nav<?php } ?>">
				<div
					class="c-ip-news__list<?php if ( $without_thumbnails ) { ?> c-ip-news__list--no-thumb<?php } ?> c-ip-news__list--<?php echo $settings['layout']; ?> c-ip-news__list--<?php echo sizeof( $news ); ?><?php if ( $settings['layout'] == 'carousel' ) { ?> js-news-carousel h-carousel h-carousel--default-dots h-carousel--flex <?php if ( $settings['arrows'] != 'yes' ) { ?> h-carousel--nav-hide<?php } ?><?php if ( $settings['dots'] != 'yes' ) { ?> h-carousel--dots-hide<?php } else { ?> c-ip-news__list--dots<?php } ?><?php } ?>">
					<?php foreach ( $news as $index => $post ) { ?>
						<?php setup_postdata( $post ); ?>
						<?php get_template_part( 'templates/content-grid' ); ?>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
		ideapark_mod_set_temp( 'sidebar_blog', $old_sidebar_blog );
		ideapark_mod_set_temp( 'post_layout', $old_post_layout );
		wp_reset_postdata();
	}
}
