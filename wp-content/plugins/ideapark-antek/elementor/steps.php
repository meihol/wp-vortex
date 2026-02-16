<?php

use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

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
class Ideapark_Elementor_Steps extends Widget_Base {

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
		return 'ideapark-steps';
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
		return __( 'Steps', 'ideapark-antek' );
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
		return 'ip-steps';
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
		return [ 'icon list', 'icon', 'steps', 'list' ];
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
			'section_list',
			[
				'label' => __( 'Items', 'ideapark-antek' ),
			]
		);


		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Image', 'ideapark-antek' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_control(
			'text',
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
			'description',
			[
				'label'       => __( 'Description', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
				'placeholder' => __( 'Enter your description', 'ideapark-antek' ),
				'separator'   => 'none',
				'rows'        => 5,
				'show_label'  => false,
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
						'text'        => __( 'List Item #1', 'ideapark-antek' ),
						'description' => __( 'Labore tempore usmod sed incididunt labore et dolore magna aliqua. Ut enim ad minim veniam quis nostrud.', 'ideapark-antek' ),
					],
					[
						'text'        => __( 'List Item #2', 'ideapark-antek' ),
						'description' => __( 'Labore tempore usmod sed incididunt labore et dolore magna aliqua. Ut enim ad minim veniam quis nostrud.', 'ideapark-antek' ),
					],
					[
						'text'        => __( 'List Item #3', 'ideapark-antek' ),
						'description' => __( 'Labore tempore usmod sed incididunt labore et dolore magna aliqua. Ut enim ad minim veniam quis nostrud.', 'ideapark-antek' ),
					],
				],
				'title_field' => '{{{ text }}}',
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
		$settings          = $this->get_settings_for_display();
		$container_divider = sizeof( $settings['icon_list'] );
		if ( $container_divider > 5 ) {
			$container_divider = 5;
		}
		?>
		<div class="c-ip-steps js-steps">
			<div class="c-ip-steps__list h-carousel js-steps-carousel" data-divider="<?php echo esc_attr( $container_divider ); ?>">
				<?php foreach ( $settings['icon_list'] as $index => $item ) { ?>
					<div
						data-index="<?php echo esc_attr( $index ); ?>"
						class="c-ip-steps__item c-ip-steps__item--<?php echo sizeof( $settings['icon_list'] ); ?><?php if ( ! $index ) { ?> c-ip-steps__item--first<?php } ?><?php if ( $index == sizeof( $settings['icon_list'] ) - 1 ) { ?> c-ip-steps__item--last<?php } ?>">
						<div class="c-ip-steps__item-wrap">
							<?php if ( ! empty( $item['link']['url'] ) ) {
								$link_key = 'link_' . $index; /* new */

								$this->add_link_attributes( $link_key, $item['link'] );
								$this->add_render_attribute( $link_key, 'class', 'c-ip-steps__link' );
								echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
							}
							?>
							<div class="c-ip-steps__icon">
								<?php if ( ! empty( $item['image']['id'] ) && ( $type = get_post_mime_type( $item['image']['id'] ) ) ) {
									$image = '';
									if ( $type == 'image/svg+xml' ) {
										$image = ideapark_get_inline_svg( $item['image']['id'], 'c-ip-steps__svg' );
									} else {
										if ( $image_meta = ideapark_image_meta( $item['image']['id'], 'medium' ) ) {
											echo ideapark_img( $image_meta, 'c-ip-steps__image' );
										}
									}
									echo ideapark_wrap( $image );
								}
								?>
							</div>
							<div class="c-ip-steps__text-wrap">
								<span class="c-ip-steps__number"><?php echo $index + 1; ?></span>
								<span class="c-ip-steps__text"><?php echo esc_html( $item['text'] ); ?></span>
							</div>
							<?php if ( ! empty( $item['link']['url'] ) ) { ?></a><?php } ?>
						</div>
						<span class="c-ip-steps__dot-left"></span>
						<span class="c-ip-steps__dot-right"></span>
					</div>
				<?php } ?>
			</div>
			<?php foreach ( $settings['icon_list'] as $index => $item ) { ?>
				<?php echo ideapark_wrap( $item['description'], '<div data-index="' . esc_attr( $index ) . '" class="c-ip-steps__description">', '</div>' ); ?>
			<?php } ?>
		</div>
		<?php
	}
}
