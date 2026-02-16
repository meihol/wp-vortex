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
 * Elementor slider widget.
 *
 * Elementor widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.0.0
 */
class Ideapark_Elementor_Slider extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve slider widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'ideapark-slider';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve slider widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Slider Carousel', 'ideapark-antek' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve slider widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'eicon-slides';
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
		return [ 'carousel', 'slider' ];
	}

	/**
	 * Register slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_slider_settings',
			[
				'label' => __( 'Slider Settings', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'centered',
				'options' => [
					'centered' => __( 'Centered', 'ideapark-antek' ),
					'filter'   => __( 'With filter', 'ideapark-antek' ),
				]
			]
		);

		$this->add_control(
			'filter_title',
			[
				'label'       => __( 'Filter title', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Find The Right Equipment', 'ideapark-antek' ),
				'condition'   => [
					'layout' => 'filter',
				]
			]
		);

		$this->add_responsive_control(
			'max_width',
			[
				'label'      => __( 'Max width of the text block', 'ideapark-antek' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'size' => 800,
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1140,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],

				'selectors' => [
					'{{WRAPPER}} .c-ip-slider__wrap--centered' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout' => 'centered',
				]
			]
		);

		$this->add_control(
			'slider_animation',
			[
				'label'   => __( 'Animation', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''               => __( 'Default', 'ideapark-antek' ),
					'banners-fade'   => __( 'Fade', 'ideapark-antek' ),
					'owl-fade-scale' => __( 'Fade and Scale', 'ideapark-antek' ),
				]
			]
		);

		$this->add_control(
			'slider_autoplay',
			[
				'label'   => __( 'Autoplay', 'ideapark-antek' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'yes'     => __( 'Yes', 'ideapark-antek' ),
				'no'      => __( 'No', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'slider_animation_timeout',
			[
				'label'      => __( 'Autoplay Timeout (sec)', 'ideapark-antek' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 5,
				],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition'  => [
					'slider_autoplay' => 'yes',
				],
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
				'default' => 'yes',
				'yes'     => __( 'Show', 'ideapark-antek' ),
				'no'      => __( 'Hide', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'random',
			[
				'label'     => __( 'Random sorting', 'ideapark-antek' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'no',
				'label_on'  => __( 'Yes', 'ideapark-antek' ),
				'label_off' => __( 'No', 'ideapark-antek' ),
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label'      => __( 'Slider height', 'ideapark-antek' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 700,
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1280,
					],
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],

				'selectors' => [
					'{{WRAPPER}} .c-ip-slider__item' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label'     => __( 'Image opacity', 'ideapark-antek' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 0.4,
				],
				'range'     => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .c-ip-slider__image' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_responsive_control(
			'font_size_text_above_title',
			[
				'label'       => __( 'Custom subtitle font size', 'ideapark-antek' ),
				'description' => __( 'Text above or below the title', 'ideapark-antek' ),
				'type'        => \Elementor\Controls_Manager::SLIDER,
				'size_units'  => [ 'px' ],
				'range'       => [
					'px' => [
						'min' => 10,
						'max' => 30,
					]
				],
				'devices'     => [ 'desktop', 'tablet', 'mobile' ],

				'selectors' => [
					'{{WRAPPER}} .c-ip-slider__text-above' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'font_size',
			[
				'label'      => __( 'Custom title font size', 'ideapark-antek' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 20,
						'max' => 100,
					]
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],

				'selectors' => [
					'{{WRAPPER}} .c-ip-slider__title' => 'font-size: calc(30px + ({{SIZE}} - 30) * ((100vw - 320px) / (1920 - 320)));',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_colors',
			[
				'label' => __( 'Colors', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .c-ip-slider__list'           => 'color: {{VALUE}};',
					'{{WRAPPER}} .h-carousel--inner .owl-prev' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .h-carousel--inner .owl-next' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .owl-dots'                    => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .c-ip-slider__list' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'     => __( 'Accent Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .c-ip-slider__decor'                    => 'color: {{VALUE}};',
					'{{WRAPPER}} .c-ip-slider__bar'                      => 'color: {{VALUE}};',
					'{{WRAPPER}} .c-ip-slider__filter-decor-left'        => 'color: {{VALUE}};',
					'{{WRAPPER}} .c-ip-slider__filter-decor-right'       => 'color: {{VALUE}};',
					'{{WRAPPER}} .c-button--outline .c-button__arrow'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .c-filter__button--widget'              => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .c-ip-slider__button.c-button--default' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_list',
			[
				'label' => __( 'Slides', 'ideapark-antek' ),
			]
		);


		$repeater = new Repeater();

		$repeater->add_control(
			'image_desktop',
			[
				'label'   => __( 'Image (Desktop)', 'ideapark-antek' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'image_mobile',
			[
				'label'   => __( 'Image (Mobile)', 'ideapark-antek' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'image_icon',
			[
				'label'   => __( 'Icon', 'ideapark-antek' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_responsive_control(
			'icon_width',
			[
				'label'      => __( 'Icon width', 'ideapark-antek' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 220,
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 800,
					],
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],

				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .c-ip-slider__icon' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => __( 'Title', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter title', 'ideapark-antek' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'text_above',
			[
				'label'       => __( 'Subtitle', 'ideapark-antek' ),
				'description' => __( 'Text above or below the title', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter text', 'ideapark-antek' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'button_text',
			[
				'label'       => __( 'Button text', 'ideapark-antek' ),
				'default'     => __( 'Read more', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter title', 'ideapark-antek' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label'     => __( 'Button link', 'ideapark-antek' ),
				'type'      => Controls_Manager::URL,
				'default'   => [
					'url' => '#',
				],
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'button_type',
			[
				'label'   => __( 'Button type', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'ideapark-antek' ),
					'outline' => __( 'Outline', 'ideapark-antek' ),
				]
			]
		);

		$repeater->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}'                          => 'background-color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .c-button--outline:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$repeater->add_control(
			'custom_color',
			[
				'label'     => __( 'Custom Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'slider_list',
			[
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render slider widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( $settings['random'] == 'yes' ) {
			shuffle( $settings['slider_list'] );
		}

		?>
		<div
			class="c-ip-slider c-ip-slider--<?php echo $settings['layout']; ?> js-slider">
			<div
				class="c-ip-slider__list js-slider-carousel h-carousel h-carousel--flex h-carousel--big-dots h-carousel--inner h-carousel--hover <?php if ( $settings['dots'] != 'yes' ) { ?> h-carousel--dots-hide<?php } else { ?> c-ip-slider__list--dots <?php if ( $settings['slider_autoplay'] == 'yes' ) { ?> h-carousel--dot-animated <?php } ?><?php } ?> <?php if ( $settings['arrows'] != 'yes' ) { ?> h-carousel--nav-hide<?php } ?>"
				data-autoplay="<?php echo esc_attr( $settings['slider_autoplay'] ); ?>"
				data-animation="<?php echo esc_attr( $settings['slider_animation'] ); ?>"
				<?php if ( ! empty( $settings['slider_animation_timeout']['size'] ) ) { ?>
					data-animation-timeout="<?php echo esc_attr( abs( $settings['slider_animation_timeout']['size'] * 1000 ) ); ?>"
				<?php } ?>
				data-widget-id="<?php echo esc_attr( $this->get_id() ); ?>">
				<?php foreach ( $settings['slider_list'] as $index => $item ) { ?>
					<div
						class="c-ip-slider__item<?php if ( $settings['dots'] == 'yes' ) { ?> c-ip-slider__item--dots<?php } ?> elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?>"
						data-index="<?php echo esc_attr( $index ); ?>">
						<?php
						$has_desktop_image = ! empty( $item['image_desktop']['id'] );
						$has_mobile_image  = ! empty( $item['image_mobile']['id'] );
						if ( $has_desktop_image ) {
							echo ideapark_img( ideapark_image_meta( $item['image_desktop']['id'], 'full' ), 'c-ip-slider__image' . ( $has_mobile_image ? ' c-ip-slider__image--desktop' : '' ),  $index && ideapark_mod( 'lazyload' ) ?: 'eager', [ 'data-index' => $index ] );
						}

						if ( $has_mobile_image ) {
							echo ideapark_img( ideapark_image_meta( $item['image_mobile']['id'], 'full' ), 'c-ip-slider__image c-ip-slider__image--mobile',  $index && ideapark_mod( 'lazyload' ) ?: 'eager', [ 'data-index' => $index ] );
						}
						?>
						<div class="c-ip-slider__shadow"></div>
						<?php if ( $settings['layout'] == 'filter' ) { ?>
						<div class="l-section__container">
							<?php } ?>
							<div class="c-ip-slider__wrap c-ip-slider__wrap--<?php echo $settings['layout']; ?>">
								<?php
								if ( ! empty( $item['image_icon']['id'] ) && ( $type = get_post_mime_type( $item['image_icon']['id'] ) ) ) {
									if ( $type == 'image/svg+xml' ) {
										echo ideapark_get_inline_svg( $item['image_icon']['id'], 'c-ip-slider__icon' );
									} else {
										echo ideapark_img( ideapark_image_meta( $item['image_icon']['id'], 'medium_large' ), 'c-ip-slider__icon' );
									}
									echo '<div class="c-ip-slider__decor"><i class="ip-decor"></i></div>';
								}
								$text_above = ideapark_wrap( $item['text_above'], '<div class="c-ip-slider__text-above"><span class="c-ip-slider__text-above-inner">', '</span></div>' );
								$title      = ideapark_wrap( $item['title'], '<div class="c-ip-slider__title"><span class="c-ip-slider__title-inner">', '</span></div>' );
								?>
								<?php if ( $settings['layout'] == 'centered' ) { ?>
									<?php echo $text_above; ?>
									<?php echo $title; ?>
								<?php } else { ?>
									<?php echo $title; ?>
									<?php echo $text_above; ?>
								<?php } ?>

								<?php if ( ! empty( $item['button_link']['url'] ) && $item['button_text'] ) { ?>
									<?php
									$link_key = 'link_' . $index;
									$this->add_link_attributes( $link_key, $item['button_link'] );
									$this->add_render_attribute( $link_key, 'class', 'c-button c-button--' . $item['button_type'] . ' c-ip-slider__button' );
									?>
									<a <?php echo $this->get_render_attribute_string( $link_key ); ?>><?php echo esc_html( $item['button_text'] ); ?>
										<i class="ip-double-arrow c-button__arrow"></i></a>
								<?php } else { ?>
									<?php echo ideapark_get_inline_svg( IDEAPARK_DIR . '/assets/img/bar-page-header.svg', 'c-ip-slider__bar' ); ?>
								<?php } ?>
							</div>
							<?php if ( $settings['layout'] == 'filter' ) { ?>
						</div>
					<?php } ?>
					</div>
				<?php } ?>
			</div>
			<?php if ( $settings['layout'] == 'filter' ) { ?>
				<div class="l-section__container c-ip-slider__filter-wrap">
					<div class="c-ip-slider__filter">
						<?php if ( $settings['filter_title'] ) { ?>
							<div class="c-ip-slider__filter-title">
								<i class="ip-decor-left-small c-ip-slider__filter-decor-left"></i>
								<span>
									<?php echo esc_html( $settings['filter_title'] ); ?>
								</span>
								<i class="ip-decor-right-small c-ip-slider__filter-decor-right"></i>
							</div>
						<?php } ?>
						<?php get_template_part( 'templates/filter' ); ?>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}
}