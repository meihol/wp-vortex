<?php

use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor team widget.
 *
 * Elementor widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.0.0
 */
class Ideapark_Elementor_Team extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve team widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'ideapark-team';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve team widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Team Members', 'ideapark-antek' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve team widget icon.
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
		return [ 'team', 'members', 'list' ];
	}

	/**
	 * Register team widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_image',
			[
				'label' => __( 'Team Members', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .c-ip-team__item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'     => __( 'Accent Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-team__item:hover .c-ip-team__content' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .c-ip-team__content:before'                 => 'border-bottom-color: {{VALUE}};',
					'(desktop) {{WRAPPER}} .c-ip-team__soc-link:hover'       => 'background-color: {{VALUE}};',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => __( 'Choose Photo', 'ideapark-antek' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);


		$repeater->add_control(
			'name',
			[
				'label'       => __( 'Name', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'John Doe', 'ideapark-antek' ),
				'placeholder' => __( 'Enter Name', 'ideapark-antek' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'occupation',
			[
				'label'       => __( 'Occupation', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Manager', 'ideapark-antek' ),
				'placeholder' => __( 'Enter Occupation', 'ideapark-antek' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'phone',
			[
				'label'       => __( 'Phone', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Phone Number', 'ideapark-antek' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'email',
			[
				'label'       => __( 'Email', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter Email', 'ideapark-antek' ),
				'label_block' => true,
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

		$repeater->add_control(
			'separator',
			[
				'label'     => __( 'Social links', 'ideapark-antek' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'soc-facebook',
			[
				'label'       => __( 'Facebook url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-instagram',
			[
				'label'       => __( 'Instagram url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-vk',
			[
				'label'       => __( 'VK url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-ok',
			[
				'label'       => __( 'OK url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-telegram',
			[
				'label'       => __( 'Telegram url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-twitter',
			[
				'label'       => __( 'Twitter url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-whatsapp',
			[
				'label'       => __( 'Whatsapp url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-youtube',
			[
				'label'       => __( 'YouTube url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-vimeo',
			[
				'label'       => __( 'Vimeo url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-linkedin',
			[
				'label'       => __( 'LinkedIn url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-flickr',
			[
				'label'       => __( 'Flickr url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-tumblr',
			[
				'label'       => __( 'Tumblr url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'placeholder' => __( 'https://your-link.com', 'ideapark-antek' ),
			]
		);

		$repeater->add_control(
			'soc-github',
			[
				'label'       => __( 'Github url', 'ideapark-antek' ),
				'type'        => Controls_Manager::URL,
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
						'name'       => __( 'Name #1', 'ideapark-antek' ),
						'occupation' => __( 'Manager', 'ideapark-antek' ),
					],
					[
						'name'       => __( 'Name #2', 'ideapark-antek' ),
						'occupation' => __( 'Manager', 'ideapark-antek' ),
					],
					[
						'name'       => __( 'Name #3', 'ideapark-antek' ),
						'occupation' => __( 'Manager', 'ideapark-antek' ),
					],
				],
				'title_field' => '{{{ name }}}',
			]
		);


		$this->end_controls_section();
	}

	/**
	 * Render team widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="c-ip-team">
			<ul class="c-ip-team__list">
				<?php
				foreach ( $settings['icon_list'] as $index => $item ) { ?>
					<?php
					$soc_count = 0;
					ob_start();
					foreach ( $item as $item_index => $row ) {
						if ( strpos( $item_index, 'soc-' ) !== false && ! empty( $item[ $item_index ]['url'] ) ) {
							$soc_count ++;

							$link_key = 'link_' . $index . '_' . $item_index; /* new */

							$this->add_link_attributes( $link_key, $item[ $item_index ] );
							$this->add_render_attribute( $link_key, 'class', 'c-ip-team__soc-link' );

							$soc_index = str_replace( 'soc-', '', $item_index );
							?>
							<a <?php echo $this->get_render_attribute_string( $link_key ); ?>><i
									class="ip-<?php echo esc_attr( $soc_index ) ?> c-ip-team__soc-icon c-ip-team__soc-icon--<?php echo esc_attr( $soc_index ) ?>">
									<!-- --></i></a>
						<?php };
					}
					$content = ob_get_clean();
					?>
					<?php if ( ! empty( $item['link']['url'] ) ) {
						$is_link  = true;
						$link_key = 'link_' . $index; /* new */

						$this->add_link_attributes( $link_key, $item['link'] );
						$this->add_render_attribute( $link_key, 'class', 'c-ip-team__link' );
						?>
					<?php } else {
						$is_link = false;
					} ?>

					<li class="c-ip-team__item c-ip-team__item--person">
						<div class="c-ip-team__thumb-wrap">
							<?php if ( $is_link ) { ?>
							<a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
								<?php } ?>
								<?php
								if ( ! empty( $item['image']['id'] ) && ( $type = get_post_mime_type( $item['image']['id'] ) ) ) {
									if ( $type == 'image/svg+xml' ) {
										echo ideapark_get_inline_svg( $item['image']['id'], 'c-ip-team__svg' );
									} else {
										echo ideapark_img( ideapark_image_meta( $item['image']['id'], 'ideapark-vehicle' ), 'c-ip-team__image' );
									}
								} ?>
								<div class="c-ip-team__thumb-shadow"></div>
								<?php if ( $is_link ) { ?>
							</a>
						<?php } ?>
							<?php echo ideapark_wrap( $content, '<div class="c-ip-team__soc">', '</div>' ) ?>
						</div>

						<div class="c-ip-team__content">
							<?php if ( $is_link ) { ?>
							<a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
								<?php } ?>
								<?php if ( ! empty( $item['name'] ) ) { ?>
									<div class="c-ip-team__name"><?php echo $item['name']; ?></div>
								<?php } ?>
								<?php if ( $is_link ) { ?>
							</a>
						<?php } ?>
							<?php if ( ! empty( $item['occupation'] ) ) { ?>
								<div class="c-ip-team__occupation"><?php echo $item['occupation']; ?></div>
							<?php } ?>
							<?php if ( ! empty( $item['phone'] ) ) { ?>
								<div class="c-ip-team__phone"><i
										class="ip-phone c-ip-team__icon"></i><?php echo ideapark_phone_wrap( esc_html( trim( $item['phone'] ) ) ); ?>
								</div>
							<?php } ?>
							<?php if ( ! empty( $item['email'] ) ) { ?>
								<div class="c-ip-team__email">
									<a href="mailto:<?php echo esc_attr( $item['email'] ); ?>"><i
											class="ip-email c-ip-team__icon"></i><?php echo esc_html( $item['email'] ); ?>
									</a>
								</div>
							<?php } ?>
						</div>

					</li>
					<?php
				}
				?>
			</ul>
		</div>
		<?php
	}
}
