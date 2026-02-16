<?php

use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Ideapark_Elementor_Button extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 */
	public function get_name() {
		return 'ideapark-button';
	}

	/**
	 * Retrieve the widget title.
	 */
	public function get_title() {
		return esc_html__( 'Antek Button', 'ideapark-antek' );
	}

	/**
	 * Retrieve the widget icon.
	 */
	public function get_icon() {
		return 'eicon-button';
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
	 * Register button widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_button',
			[
				'label' => __( 'Button', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'button_type',
			[
				'label'   => __( 'Type', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => __( 'Default', 'ideapark-antek' ),
					'outline' => __( 'Outline', 'ideapark-antek' ),
				]
			]
		);


		$this->add_control(
			'text',
			[
				'label'       => __( 'Text', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Click here', 'ideapark-antek' ),
				'placeholder' => __( 'Click here', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => __( 'Link', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'arrow',
			[
				'label'     => __( 'Arrow', 'ideapark-antek' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Show', 'ideapark-antek' ),
				'label_off' => __( 'Hide', 'ideapark-antek' ),
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
			'default_bg_color',
			[
				'label'     => __( 'Accent Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-button--default'                     => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .c-button--outline:hover'               => 'color: {{VALUE}};',
					'{{WRAPPER}} .c-button--outline .c-button__arrow' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label'       => __( 'Button ID', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => '',
				'title'       => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'ideapark-antek' ),
				'label_block' => false,
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'ideapark-antek' ),
				'separator'   => 'before',

			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render button widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'c-ip-button__wrap' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['link'] );
		}

		$this->add_render_attribute( 'button', 'class', 'c-ip-button c-button' );
		$this->add_render_attribute( 'button', 'role', 'button' );

		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'button', 'id', $settings['button_css_id'] );
		}

		if ( ! empty( $settings['button_type'] ) ) {
			$this->add_render_attribute( 'button', 'class', 'c-button--' . $settings['button_type'] );
		}

		?>
		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
			<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
				<?php $this->render_text(); ?><?php if ( $settings['arrow'] == 'yes' ) { ?><i
					class="ip-double-arrow c-ip-button__arrow c-button__arrow"></i><?php } ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Render button text.
	 *
	 * Render button widget text.
	 *
	 * @since  1.5.0
	 * @access protected
	 */
	protected function render_text() {
		$settings = $this->get_settings_for_display();
		?><span class="c-ip-button__text"><?php echo $settings['text']; ?></span><?php
	}
}
