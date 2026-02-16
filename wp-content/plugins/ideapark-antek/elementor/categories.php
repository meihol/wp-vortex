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
class Ideapark_Elementor_Categories extends Widget_Base {

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
		return 'ideapark-categories';
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
		return __( 'Categories', 'ideapark-antek' );
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
		return 'ip-image-box';
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
		return [ 'category', 'categories', 'list' ];
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
			'section_categories',
			[
				'label' => __( 'Categories', 'ideapark-antek' ),
			]
		);


		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'image' => __( 'Image list', 'ideapark-antek' ),
					'icon'  => __( 'Icon list', 'ideapark-antek' ),
				]
			]
		);

		$this->add_control(
			'view_mode',
			[
				'label'     => __( 'Show', 'ideapark-antek' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'icon',
				'options'   => [
					'icon'  => __( 'Category icon', 'ideapark-antek' ),
					'image' => __( 'Category image', 'ideapark-antek' ),
				],
				'condition' => [
					'layout' => 'icon',
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => __( 'Button text', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Enter text', 'ideapark-antek' ),
				'default'     => __( 'View Equipments', 'ideapark-antek' ),
				'condition'   => [
					'layout' => 'icon',
				],
			]
		);

		$this->add_control(
			'hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);

		$category_list = ideapark_get_categories( true );

		foreach ( $category_list as $category_slug => $category_name ) {
			$this->add_control(
				'vehicle-' . $category_slug,
				[
					'label'        => esc_html( $category_name ),
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
		$settings = $this->get_settings_for_display();
		?>
		<div class="c-ip-categories">
			<div
				class="c-ip-categories__list c-ip-categories__list--<?php echo $settings['layout']; ?>">
				<?php foreach ( $settings as $index => $show ) { ?>
					<?php if ( preg_match( '~^vehicle-(.+)$~', $index, $match ) && $show == 'yes' && ( $term = get_term_by( 'slug', $match[1], 'vehicle_type' ) ) && ! is_wp_error( $term ) ) {
						$meta              = get_term_meta( $term->term_id );
						$url               = get_term_link( $term->term_id );
						$short_description = ! empty( $meta['short_description'][0] ) ? $meta['short_description'][0] : '';
						?>
						<div class="c-ip-categories__item c-ip-categories__item--<?php echo $settings['layout']; ?> ">
							<?php if ( $settings['layout'] == 'image' ) { ?>
								<a class="c-ip-categories__link" href="<?php echo esc_url( $url ); ?>">
									<div class="c-ip-categories__image-wrap">
										<?php
										if ( ! empty( $meta['image'][0] ) && ( $type = get_post_mime_type( $meta['image'][0] ) ) ) {
											if ( $type == 'image/svg+xml' ) {
												echo ideapark_get_inline_svg( $meta['image'][0], 'c-ip-categories__image' );
											} else {
												if ( $image_meta = ideapark_image_meta( $meta['image'][0], 'medium_large' ) ) {
													echo ideapark_img( $image_meta, 'c-ip-categories__image' );
												}
											}
										}
										?>
										<div class="c-ip-categories__image-overlay"></div>
										<i class="ip-plus c-ip-categories__plus"></i>
									</div>
									<div class="c-ip-categories__title c-ip-categories__title--image">
										<span>
											<?php echo esc_html( $term->name ); ?>
										</span>
									</div>
								</a>
							<?php } else { ?>
								<div class="c-ip-categories__round"></div>
								<div class="c-ip-categories__icon-wrap">
									<?php
									if ( $settings['view_mode'] == 'image' && ! empty( $meta['image'][0] ) && ( $type = get_post_mime_type( $meta['image'][0] ) ) ) {
										if ( $type == 'image/svg+xml' ) {
											echo ideapark_get_inline_svg( $meta['image'][0], 'c-ip-categories__icon' );
										} else {
											if ( $image_meta = ideapark_image_meta( $meta['image'][0], 'medium' ) ) {
												echo ideapark_img( $image_meta, 'c-ip-categories__icon c-ip-categories__icon--image' );
											}
										}
									} elseif ( $settings['view_mode'] == 'icon' && ! empty( $meta['font-icon'][0] ) ) { ?>
										<i class="c-ip-categories__font-icon <?php echo esc_attr( $meta['font-icon'][0] ); ?>"
										   aria-hidden="true"></i>
									<?php }
									?>
								</div>
								<div class="c-ip-categories__title c-ip-categories__title--icon">
									<span>
										<?php echo esc_html( $term->name ); ?>
									</span>
								</div>
								<?php echo ideapark_wrap( $short_description, '<div class="c-ip-categories__description">', '</div>' ); ?>
								<?php if ( $settings['button_text'] ) { ?>
									<div class="c-ip-categories__spacer"></div>
									<a class="c-button c-button--outline c-ip-categories__button"
									   href="<?php echo esc_url( $url ); ?>">
										<?php echo esc_html( $settings['button_text'] ); ?><!--
									   --><i class="ip-double-arrow c-button__arrow"></i></a>
								<?php } ?>
							<?php } ?>
						</div>
						<?php
					}
				} ?>
			</div>
		</div>
		<?php
		wp_reset_postdata();
	}
}
