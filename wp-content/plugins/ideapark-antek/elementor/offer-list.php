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
 * Elementor activities widget.
 *
 * Elementor widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.0.0
 */
class Ideapark_Elementor_Offer_List extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve activities widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'ideapark-offer-list';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve activities widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Offers', 'ideapark-antek' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve activities widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'eicon-bullet-list';
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
		return [ 'list', 'activities', 'offer' ];
	}

	/**
	 * Register activities widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_activities',
			[
				'label' => __( 'Offers', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .c-ip-offer-list__block' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'     => __( 'Accent Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-offer-list__price .amount' => 'color: {{VALUE}};',
					'{{WRAPPER}} .c-ip-offer-list__price-postfix' => 'color: {{VALUE}};',
					'(desktop) {{WRAPPER}} .c-ip-offer-list__title--link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Choose Image', 'ideapark-antek' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'title_text',
			[
				'label'       => __( 'Title', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'This is the heading', 'ideapark-antek' ),
				'placeholder' => __( 'Enter your title', 'ideapark-antek' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'description_text',
			[
				'label'       => __( 'Description', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Click edit button to change this text', 'ideapark-antek' ),
				'placeholder' => __( 'Enter your description', 'ideapark-antek' ),
				'separator'   => 'none',
				'rows'        => 5,
				'show_label'  => false,
			]
		);

		$repeater->add_control(
			'price_prefix',
			[
				'label'       => __( 'Price prefix', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Enter prefix', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'price',
			[
				'label' => __( 'Price', 'ideapark-antek' ),
				'type'  => Controls_Manager::NUMBER,
				'min'   => 0,
				'step'  => 0.01,
			]
		);

		$repeater->add_control(
			'price_postfix',
			[
				'label'       => __( 'Price postfix', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Enter postfix', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => __( 'Link', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'offer_list',
			[
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title_text'       => __( 'Offer #1', 'ideapark-antek' ),
						'description_text' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'price'            => '45',
					],
					[
						'title_text'       => __( 'Offer #2', 'ideapark-antek' ),
						'description_text' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'price'            => '45',
					],
					[
						'title_text'       => __( 'Offer #3', 'ideapark-antek' ),
						'description_text' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'price'            => '45',
					],
				],
				'title_field' => '{{{ title_text }}}',
			]
		);


		$this->end_controls_section();
	}

	/**
	 * Render activities widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$count    = sizeof( $settings['offer_list'] );
		$limit    = $count;
		if ( ! $count ) {
			return;
		}

		if ( $count > 1 ) {
			$limit = ceil( $count / 2 );
		}
		?>
		<div class="l-section l-section--container c-ip-offer-list">
			<div class="c-ip-offer-list__wrap">
				<div class="c-ip-offer-list__block">
					<?php foreach ( $settings['offer_list'] as $index => $item ) { ?>
					<?php

					$link_key = 'link-' . $index;

					if ( ! empty( $item['link']['url'] ) ) {
						$this->add_link_attributes( $link_key, $item['link'] );
					}

					?>
					<div class="c-ip-offer-list__item">
						<div class="c-ip-offer-list__thumb-wrap">
							<?php if ( ! empty( $item['link']['url'] ) ) { ?>
							<a <?php echo $this->get_render_attribute_string( $link_key ) ?>>
								<?php } ?>
								<?php
								if ( ! empty( $item['image']['id'] ) && ( $type = get_post_mime_type( $item['image']['id'] ) ) ) {
									if ( $type == 'image/svg+xml' ) {
										echo ideapark_get_inline_svg( $item['image']['id'], 'c-ip-offer-list__thumb' );
									} else {
										echo ideapark_img( ideapark_image_meta( $item['image']['id'], 'thumbnail' ), 'c-ip-offer-list__thumb' );
									}
								} ?>
								<?php if ( ! empty( $item['link']['url'] ) ) { ?>
							</a>
						<?php } ?>
						</div>
						<div class="c-ip-offer-list__content-wrap">
							<div class="c-ip-offer-list__content-row">

								<?php
								$title = esc_html( trim( $item['title_text'] ) ) . '&nbsp;&nbsp;';
								if ( ! empty( $item['link']['url'] ) ) {
									$title = ideapark_wrap( $title, '<a ' . $this->get_render_attribute_string( $link_key ) . '>', '</a>' );
								}
								?>
								<?php echo ideapark_wrap( $title, '<div class="c-ip-offer-list__title' . ( ! empty( $item['link']['url'] ) ? ' c-ip-offer-list__title--link' : '') . '">', '</div>' ); ?>
								<?php if ( $item['price'] && function_exists( 'wc_price' ) ) { ?>
									<span class="c-ip-offer-list__price">
										<?php echo ideapark_wrap( $item['price_prefix'], '<span class="c-ip-offer-list__price-prefix">', '</span>' ); ?>
										<?php echo wc_price( $item['price'] ); ?>
										<?php echo ideapark_wrap( $item['price_postfix'], '<span class="c-ip-offer-list__price-postfix">', '</span>' ); ?>
									</span>
								<?php } ?>
							</div>
							<div class="c-ip-offer-list__content-row">
								<?php echo ideapark_wrap( esc_html( $item['description_text'] ), '<div class="c-ip-offer-list__excerpt">', '</div>' ); ?>
							</div>
						</div>

					</div>
					<?php if ( $index + 1 >= $limit ) {
					$limit = $count + 1; ?>
				</div>
				<div class="c-ip-offer-list__block">
					<?php } ?>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}
}
