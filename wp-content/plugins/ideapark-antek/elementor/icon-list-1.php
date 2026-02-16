<?php

use Elementor\Icons_Manager;
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
class Ideapark_Elementor_Icon_List_1 extends Widget_Base {

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
		return 'ideapark-icon-list-1';
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
		return __( 'Icon List', 'ideapark-antek' );
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
		return 'ip-icon-list';
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
		return [ 'icon list', 'icon', 'list' ];
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
			'section_icon',
			[
				'label' => __( 'Icon List', 'ideapark-antek' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => __( 'Title', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 'List Item', 'ideapark-antek' ),
				'placeholder' => __( 'Enter title', 'ideapark-antek' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

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

		$this->add_control(
			'icon_list',
			[
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'title'    => __( 'List Item #1', 'ideapark-antek' ),
						'icon_svg' => 'fa fa-dot-circle-o',
						'link'     => '#'
					],
					[
						'title'    => __( 'List Item #2', 'ideapark-antek' ),
						'icon_svg' => 'fa fa-dot-circle-o',
						'link'     => '#'
					],
					[
						'title'    => __( 'List Item #3', 'ideapark-antek' ),
						'icon_svg' => 'fa fa-dot-circle-o',
						'link'     => '#'
					],
				],
				'title_field' => '{{{ elementor.helpers.renderIcon( this, icon_svg, {}, "i", "panel" ) }}} {{{ title }}}',
			]
		);

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
		<div class="c-ip-icon-list-1">
			<ul class="c-ip-icon-list-1__list">
				<?php
				foreach ( $settings['icon_list'] as $index => $item ) { ?>
					<?php
					if ( ! empty( $item['link']['url'] ) ) {
						$link_key = 'link_' . $index;
						$this->add_link_attributes( $link_key, $item['link'] );
						$this->add_render_attribute( $link_key, 'class', 'c-ip-icon-list-1__link' );
						$is_link = true;
					} else {
						$is_link = false;
					}
					?>
					<li class="c-ip-icon-list-1__item">
						<?php
						if ( $is_link ) {
							echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
						} else { ?>
						<div class="c-ip-icon-list-1__link">
							<?php } ?>

							<div class="c-ip-icon-list-1__icon-wrap">
								<?php if ( ! empty( $item['icon_svg'] ) ) { ?>
									<?php Icons_Manager::render_icon( $item['icon_svg'], [
										'aria-hidden' => 'true',
										'class'       => 'c-ip-icon-list-1__icon'
									] ); ?>
								<?php } ?>
							</div>
							<?php echo ideapark_wrap( esc_html( $item['title'] ), '<div class="c-ip-icon-list-1__title">', '</div>' ); ?>

							<?php if ( $is_link ) { ?></a><?php } else { ?>
						</div>
					<?php } ?>
						<div class="c-ip-icon-list-1__stretch-bar"></div>
					</li>
				<?php } ?>
			</ul>
		</div>
		<?php
	}
}
