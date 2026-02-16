<?php

use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor counter widget.
 *
 * Elementor widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.0.0
 */
class Ideapark_Elementor_Counter extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve counter widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'ideapark-counter';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve counter widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Counter', 'ideapark-antek' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve counter widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'eicon-counter';
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
	 * Retrieve the list of scripts the counter widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @return array Widget scripts dependencies.
	 * @since  1.3.0
	 * @access public
	 *
	 */
	public function get_script_depends() {
		return [ 'jquery-numerator' ];
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
		return [ 'counter', 'icon', 'list' ];
	}

	/**
	 * Register counter widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'     => __( 'Accent Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-counter__counter' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Icon Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-counter__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} svg'                 => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-counter' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_items',
			[
				'label' => __( 'Items', 'ideapark-antek' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon_svg',
			[
				'label'            => __( 'Icon', 'ideapark-antek' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => true,
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'fa4compatibility' => 'icon'
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => __( 'Title', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your title', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'subtitle',
			[
				'label'       => __( 'Subtitle', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter your subtitle', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => __( 'Link', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [
					'active' => true,
				],
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'starting_number',
			[
				'label'   => __( 'Starting Number', 'ideapark-antek' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
			]
		);

		$repeater->add_control(
			'ending_number',
			[
				'label'   => __( 'Ending Number', 'ideapark-antek' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 100,
			]
		);

		$repeater->add_control(
			'prefix',
			[
				'label'       => __( 'Number Prefix', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => 1,
			]
		);

		$repeater->add_control(
			'suffix',
			[
				'label'       => __( 'Number Suffix', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => __( 'Plus', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'duration',
			[
				'label'   => __( 'Animation Duration', 'ideapark-antek' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 2000,
				'min'     => 100,
				'step'    => 100,
			]
		);

		$repeater->add_control(
			'thousand_separator',
			[
				'label'     => __( 'Thousand Separator', 'ideapark-antek' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Show', 'ideapark-antek' ),
				'label_off' => __( 'Hide', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'thousand_separator_char',
			[
				'label'     => __( 'Separator', 'ideapark-antek' ),
				'type'      => Controls_Manager::SELECT,
				'condition' => [
					'thousand_separator' => 'yes',
				],
				'options'   => [
					''  => 'Default',
					'.' => 'Dot',
					' ' => 'Space',
				],
			]
		);

		$this->add_control(
			'icon_list',
			[
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'    => __( 'List Item #1', 'ideapark-antek' ),
						'subtitle' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'icon_svg' => 'fa fa-dot-circle-o',
					],
					[
						'title'    => __( 'List Item #2', 'ideapark-antek' ),
						'subtitle' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'icon_svg' => 'fa fa-dot-circle-o',
					],
					[
						'title'    => __( 'List Item #3', 'ideapark-antek' ),
						'subtitle' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'icon_svg' => 'fa fa-dot-circle-o',
					],
					[
						'title'    => __( 'List Item #4', 'ideapark-antek' ),
						'subtitle' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'icon_svg' => 'fa fa-dot-circle-o',
					],
				],
				'title_field' => '{{{ elementor.helpers.renderIcon( this, icon_svg, {}, "i", "panel" ) }}} {{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render counter widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="c-ip-counter">
			<ul class="c-ip-counter__list">
				<?php
				foreach ( $settings['icon_list'] as $index => $item ) : ?>
					<li class="c-ip-counter__item">
						<?php
						if ( ! empty( $item['link']['url'] ) ) {
							$link_key = 'link_' . $index;
							$this->add_link_attributes( $link_key, $item['link'] );
							echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
						} ?>

						<div
							class="c-ip-counter__item-wrap">

							<div class="c-ip-counter__row">
								<?php

								$counter_key = 'counter-' . $index;

								$this->add_render_attribute( $counter_key, [
									'class'         => 'js-counter-number',
									'data-duration' => $item['duration'],
									'data-to-value' => $item['ending_number'],
								] );

								if ( ! empty( $item['thousand_separator'] ) ) {
									$delimiter = empty( $item['thousand_separator_char'] ) ? ',' : $item['thousand_separator_char'];
									$this->add_render_attribute( $counter_key, 'data-delimiter', $delimiter );
								} ?>
								<div class="c-ip-counter__counter">
								<span
									class="c-ip-counter__counter-hidden"><?php echo $item['prefix']; ?><?php echo $item['ending_number']; ?><?php echo $item['suffix']; ?></span>
									<span class="c-ip-counter__counter-visible"><span
											class="c-ip-counter__counter-number-prefix"><?php echo $item['prefix']; ?></span><span <?php echo $this->get_render_attribute_string( $counter_key ); ?>><?php echo $item['starting_number']; ?></span><span
											class="c-ip-counter__counter-number-suffix"><?php echo $item['suffix']; ?></span></span>

								</div>
								<?php
								if ( ! empty( $item['icon_svg'] ) ) {
									Icons_Manager::render_icon( $item['icon_svg'], [
										'aria-hidden' => 'true',
										'class'       => 'c-ip-counter__icon'
									] );
								} ?>
							</div>

							<?php echo ideapark_wrap( esc_html( $item['title'] ), '<div class="c-ip-counter__title">', '</div>' ); ?>
							<?php echo ideapark_wrap( esc_html( $item['subtitle'] ), '<div class="c-ip-counter__subtitle">', '</div>' ); ?>
						</div>
						<?php if ( ! empty( $item['link']['url'] ) ) : ?>
							</a>
						<?php endif; ?>
					</li>
				<?php
				endforeach;
				?>
			</ul>
		</div>
		<?php
	}
}
