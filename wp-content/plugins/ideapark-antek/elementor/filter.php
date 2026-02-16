<?php

use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Ideapark_Elementor_Filter extends Widget_Base {
	/**
	 * Retrieve the widget name.
	 */
	public function get_name() {
		return 'ideapark-filter';
	}

	/**
	 * Retrieve the widget title.
	 */
	public function get_title() {
		return esc_html__( 'Filter', 'ideapark-antek' );
	}

	/**
	 * Retrieve the widget icon.
	 */
	public function get_icon() {
		return 'eicon-site-search';
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
			'section_category',
			[
				'label' => __( 'Settings', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Title', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'Find The Right Equipment', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'custom_color',
			[
				'label' => esc_attr__( 'Custom Text Color', 'ideapark-antek' ),
				'type'  => Controls_Manager::COLOR,
			]
		);

		$this->add_control(
			'custom_background_color',
			[
				'label' => esc_attr__( 'Custom Background Color', 'ideapark-antek' ),
				'type'  => Controls_Manager::COLOR,
			]
		);

		$this->add_control(
			'global_filter_settings',
			[
				'label' => '',
				'type'  => \Elementor\Controls_Manager::RAW_HTML,
				'raw'   => '<a target="_blank" class="elementor-button elementor-button-default" href="' . esc_url( admin_url( 'customize.php?autofocus[control]=disable_filter' ) ) . '">' . __( 'Global filter settings', 'ideapark-antek' ) . '</a>',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 */
	protected function render() {
		$settings = $this->get_settings();
		ob_start();
		get_template_part( 'templates/filter' );
		$filter = ob_get_clean();
		?>
		<div class="c-ip-filter c-ip-filter--col-<?php echo ideapark_mod( '_filter_col' ); ?> l-section l-section--container">
			<?php if ( $settings['title'] ) { ?>
				<div class="c-ip-filter__title">
					<i class="ip-decor-left c-ip-filter__decor-left"></i>
					<span>
						<?php echo esc_html( $settings['title'] ); ?>
					</span>
					<i class="ip-decor-right c-ip-filter__decor-right"></i>
				</div>
			<?php } ?>
			<?php echo ideapark_wrap( $filter, '<div class="c-ip-filter__form">', '</div>' ); ?>
		</div>
		<?php
	}
}
