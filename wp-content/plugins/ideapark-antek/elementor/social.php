<?php

use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Ideapark_Elementor_Social extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 */
	public function get_name() {
		return 'ideapark-social';
	}

	/**
	 * Retrieve the widget title.
	 */
	public function get_title() {
		return esc_html__( 'Social', 'ideapark-antek' );
	}

	/**
	 * Retrieve the widget icon.
	 */
	public function get_icon() {
		return 'eicon-social-icons';
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
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_social',
			[
				'label' => __( 'Social icons', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'rounded',
			[
				'label'     => __( 'Rounded', 'ideapark-antek' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Yes', 'ideapark-antek' ),
				'label_off' => __( 'No', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-social__icon--rounded:after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'rounded' => 'yes',
				],
			]
		);

		$this->add_control(
			'background_color_hover',
			[
				'label'     => __( 'Background Color on Hover', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'(desktop) {{WRAPPER}} .c-ip-social__icon--rounded:hover:after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'rounded' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Icons Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-social__icon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color_hover',
			[
				'label'     => __( 'Icons Color on Hover', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'(desktop) {{WRAPPER}} .c-ip-social__icon:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Icon size', 'ideapark-antek' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 16,
				],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 30,
					]
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],

				'selectors' => [
					'{{WRAPPER}} .c-ip-social'                      => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .c-ip-social--rounded'             => 'padding-left: calc({{SIZE}}{{UNIT}} / 3 * 1.6); padding-right: calc({{SIZE}}{{UNIT}} / 3 * 1.6);',
					'{{WRAPPER}} .c-ip-social__icon'                => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .c-ip-social__icon--rounded:after' => 'margin-left:-{{SIZE}}{{UNIT}}; margin-top:-{{SIZE}}{{UNIT}}; width: calc({{SIZE}}{{UNIT}} * 2);height: calc({{SIZE}}{{UNIT}} * 2);',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label'      => __( 'Space', 'ideapark-antek' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 30,
				],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],

				'selectors' => [
					'{{WRAPPER}} .c-ip-social__icon' => 'margin: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .c-ip-social'       => 'margin: calc(-{{SIZE}}{{UNIT}} / 2);'
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'        => __( 'Alignment', 'ideapark-antek' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
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
				'prefix_class' => 'elementor%s-align-',
				'default'      => '',
			]
		);

		$this->add_control(
			'separator',
			[
				'label'     => __( 'Social links', 'ideapark-antek' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		foreach ( ideapark_social_networks() as $code => $name ) {
			$this->add_control(
				'soc-' . $code,
				[
					'label'       => sprintf( __( '%s url', 'ideapark-antek' ), $name ),
					'type'        => Controls_Manager::URL,
					'label_block' => true,
					'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
				]
			);
		}
		
		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings  = $this->get_settings_for_display();
		$soc_count = 0;
		ob_start();
		foreach ( $settings as $item_index => $row ) {
			if ( strpos( $item_index, 'soc-' ) !== false && ! empty( $row['url'] ) ) {
				$soc_count ++;

				$link_key = 'link_' . $item_index;

				$this->add_link_attributes( $link_key, $row );
				$this->add_render_attribute( $link_key, 'class', 'c-ip-social__link' );

				$soc_index = str_replace( 'soc-', '', $item_index );
				?>
				<a <?php echo $this->get_render_attribute_string( $link_key ); ?>><i
						class="ip-<?php echo esc_attr( $soc_index ) ?> c-ip-social__icon c-ip-social__icon--<?php echo esc_attr( $soc_index ) ?><?php if ( $settings['rounded'] ) { ?> c-ip-social__icon--rounded<?php } ?>">
						<!-- --></i></a>
			<?php };
		}
		$content = ob_get_clean();
		echo ideapark_wrap( $content, '<div class="c-ip-social' . ( $settings['rounded'] ? ' c-ip-social--rounded' : '' ) . '">', '</div>' );
	}
}
