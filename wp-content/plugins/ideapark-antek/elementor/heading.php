<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor heading widget.
 *
 * Elementor widget that displays an eye-catching headlines.
 *
 * @since 1.0.0
 */
class Ideapark_Elementor_Heading extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'ideapark-heading';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Antek Heading', 'ideapark-antek' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'eicon-t-letter';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
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
		return [ 'heading', 'title', 'text' ];
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Title', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Title', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter your title', 'ideapark-antek' ),
				'default'     => __( 'Add Your Heading Text Here', 'ideapark-antek' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label'     => __( 'Link', 'ideapark-antek' ),
				'type'      => Controls_Manager::URL,
				'default'   => [
					'url' => '',
				],
				'separator' => 'before',
				'dynamic'   => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'size',
			[
				'label'   => __( 'Size', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'ideapark-antek' ),
					'medium'  => __( 'Medium', 'ideapark-antek' ),
					'large'   => __( 'Large', 'ideapark-antek' ),
				],
			]
		);

		$this->add_control(
			'header_size',
			[
				'label'   => __( 'HTML Tag', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default' => 'div',
			]
		);

		$this->add_control(
			'divider',
			[
				'label'        => __( 'Divider', 'ideapark-antek' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'ideapark-antek' ),
				'label_off'    => __( 'No', 'ideapark-antek' ),
				'return_value' => 'yes',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'     => __( 'Alignment', 'ideapark-antek' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => __( 'Left', 'ideapark-antek' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'ideapark-antek' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'ideapark-antek' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'ideapark-antek' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .c-ip-heading' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label'   => __( 'View', 'ideapark-antek' ),
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => __( 'Title', 'ideapark-antek' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'title_color',
			[
				'label'     => __( 'Text Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .c-ip-heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'max_width',
			[
				'label'      => __( 'Max width', 'ideapark-antek' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
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
					'{{WRAPPER}} .c-ip-heading .c-ip-heading__inner' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'block_align',
			[
				'label'   => __( 'Block alignment', 'ideapark-antek' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => __( 'Left', 'ideapark-antek' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'ideapark-antek' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'ideapark-antek' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => '',
			]
		);

		$this->add_control(
			'starting_number',
			[
				'label'   => __( 'Starting Number', 'ideapark-antek' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .c-ip-heading',
			]
		);


		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'text_shadow',
				'selector' => '{{WRAPPER}} .c-ip-heading',
			]
		);

		$this->add_control(
			'blend_mode',
			[
				'label'     => __( 'Blend Mode', 'ideapark-antek' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''            => __( 'Normal', 'ideapark-antek' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'difference'  => 'Difference',
					'exclusion'   => 'Exclusion',
					'hue'         => 'Hue',
					'luminosity'  => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .c-ip-heading' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( '' === $settings['title'] ) {
			return;
		}

		$this->add_render_attribute( 'title', 'class', 'c-ip-heading' );
		$this->add_render_attribute( 'title', 'class', 'c-ip-heading--' . $settings['size'] );
		$this->add_render_attribute( 'title', 'class', 'c-ip-heading--' . ( ! empty( $settings['align'] ) ? $settings['align'] : 'left' ) );
		if ( ! empty( $settings['align_tablet'] ) ) {
			$this->add_render_attribute( 'title', 'class', 'c-ip-heading--tablet-' . $settings['align_tablet'] );
		}
		if ( ! empty( $settings['align_mobile'] ) ) {
			$this->add_render_attribute( 'title', 'class', 'c-ip-heading--mobile-' . $settings['align_mobile'] );
		}
		if ( $settings['block_align'] ) {
			$this->add_render_attribute( 'title', 'class', 'c-ip-heading--block-align-' . $settings['block_align'] );
		}

		$title = nl2br( $settings['title'] );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'url', $settings['link'] );

			$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $title );
		}

		$title_html = sprintf( '<%1$s %2$s><span class="c-ip-heading__inner">%3$s</span>%4$s</%1$s>', $settings['header_size'], $this->get_render_attribute_string( 'title' ), $title, $settings['divider'] ? '<div class="c-ip-heading__divider c-ip-heading__divider--' . $settings['size'] . '"><i class="' . ( $settings['size'] == 'large' ? 'ip-decor' : 'ip-decor-right' ) . '"></i></div>' : '' );

		echo $title_html;
	}
}
