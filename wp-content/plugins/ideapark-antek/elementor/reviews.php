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
 * Elementor reviews widget.
 *
 * Elementor widget that displays a bullet list with any chosen icons and texts.
 *
 * @since 1.0.0
 */
class Ideapark_Elementor_Reviews extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve reviews widget name.
	 *
	 * @return string Widget name.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'ideapark-reviews';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve reviews widget title.
	 *
	 * @return string Widget title.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'Reviews carousel', 'ideapark-antek' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve reviews widget icon.
	 *
	 * @return string Widget icon.
	 * @since  1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'ip-pr-carousel';
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
		return [ 'carousel', 'reviews', 'reviews' ];
	}

	/**
	 * Register reviews widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_reviews_settings',
			[
				'label' => __( 'Settings', 'ideapark-antek' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => __( 'Layout', 'ideapark-antek' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'layout-2',
				'options' => [
					'layout-1' => __( '1 per row', 'ideapark-antek' ),
					'layout-2' => __( '2 per row', 'ideapark-antek' ),
				]
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
			'text_color',
			[
				'label'     => __( 'Text Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-reviews__item' => 'color: {{VALUE}};',
					'{{WRAPPER}} .owl-nav'            => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accent_color',
			[
				'label'     => __( 'Accent Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-reviews__quote'                => 'color: {{VALUE}};',
					'{{WRAPPER}} .c-ip-reviews__star-rating i:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'     => __( 'Border Color', 'ideapark-antek' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .c-ip-reviews__item'        => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .c-ip-reviews__item:before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .c-ip-reviews__item:after'  => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_reviews',
			[
				'label' => __( 'Reviews', 'ideapark-antek' ),
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
			'reviewer_name',
			[
				'label'       => __( 'Reviewer name', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter name', 'ideapark-antek' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'reviewer_occupation',
			[
				'label'       => __( 'Reviewer occupation', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter occupation', 'ideapark-antek' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'reviewer_title',
			[
				'label'       => __( 'Review title', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter review text', 'ideapark-antek' ),
				'separator'   => 'none',
				'show_label'  => false,
			]
		);

		$repeater->add_control(
			'reviewer_text',
			[
				'label'       => __( 'Review text', 'ideapark-antek' ),
				'type'        => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter review text', 'ideapark-antek' ),
				'separator'   => 'none',
				'rows'        => 5,
				'show_label'  => false,
			]
		);

		$repeater->add_control(
			'rating',
			[
				'label'   => __( 'Rating', 'ideapark-antek' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 5,
				'step'    => 0.1,
				'default' => 5,
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
			'review_list',
			[
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'reviewer_name'       => __( 'Name #1', 'ideapark-antek' ),
						'reviewer_occupation' => __( 'Rental Customer', 'ideapark-antek' ),
						'reviewer_title'      => __( 'Consectetur adipiscing elit.', 'ideapark-antek' ),
						'reviewer_text'       => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'rating'              => '5',
					],
					[
						'reviewer_name'       => __( 'Name #2', 'ideapark-antek' ),
						'reviewer_occupation' => __( 'Rental Customer', 'ideapark-antek' ),
						'reviewer_title'      => __( 'Consectetur adipiscing elit.', 'ideapark-antek' ),
						'reviewer_text'       => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'rating'              => '3',
					],
					[
						'reviewer_name'       => __( 'Name #3', 'ideapark-antek' ),
						'reviewer_occupation' => __( 'Rental Customer', 'ideapark-antek' ),
						'reviewer_title'      => __( 'Consectetur adipiscing elit.', 'ideapark-antek' ),
						'reviewer_text'       => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'ideapark-antek' ),
						'rating'              => '4',
					],
				],
				'title_field' => '{{{ reviewer_name }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render reviews widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since  1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="c-ip-reviews">
			<div
				class="c-ip-reviews__wrap c-ip-reviews__wrap--<?php echo $settings['layout']; ?> c-ip-reviews__wrap--<?php echo sizeof( $settings['review_list'] ); ?> <?php if ( $settings['dots'] == 'yes' ) { ?> c-ip-reviews__wrap--dots<?php } ?> <?php if ( $settings['arrows'] == 'yes' ) { ?> c-ip-reviews__wrap--nav<?php } ?>">
				<div
					data-layout="<?php echo $settings['layout']; ?>"
					class="c-ip-reviews__list c-ip-reviews__list--<?php echo $settings['layout']; ?> c-ip-reviews__list--<?php echo sizeof( $settings['review_list'] ); ?> js-reviews-carousel h-carousel h-carousel--flex <?php if ( $settings['arrows'] != 'yes' ) { ?> h-carousel--nav-hide<?php } ?> <?php if ( $settings['dots'] != 'yes' ) { ?> h-carousel--dots-hide<?php } ?>">
					<?php foreach ( $settings['review_list'] as $index => $item ) { ?>
						<?php
						if ( ! empty( $item['link']['url'] ) ) {
							$link_key = 'link_' . $index;
							$this->add_link_attributes( $link_key, $item['link'] );
							$this->add_render_attribute( $link_key, 'class', 'c-ip-reviews__link' );
							$is_link = true;
						} else {
							$is_link = false;
						}

						$textual_rating = $item['rating'] . '/5';
						$icon           = '&#9733;';

						$icon_key = 'icon_wrapper_' . $index;

						$this->add_render_attribute( $icon_key, [
							'class' => 'c-ip-reviews__star-rating',
							'title' => $textual_rating,
						] );

						$stars_element = '<div ' . $this->get_render_attribute_string( $icon_key ) . '>' . $this->render_stars( $icon, $item['rating'] ) . '</div>';
						?>
						<div class="c-ip-reviews__item">
							<i class="ip-quote c-ip-reviews__quote"><!-- --></i>
							<div class="c-ip-reviews__content-wrap">
								<?php if ( $is_link ) {
									echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
								} ?>
								<?php echo ideapark_wrap( $item['reviewer_title'], '<div class="c-ip-reviews__title">', '</div>' ); ?>
								<?php echo ideapark_wrap( $item['reviewer_text'], '<div class="c-ip-reviews__text">', '</div>' ); ?>
								<?php if ( $is_link ) {
									echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
								} ?>
							</div>
							<div class="c-ip-reviews__spacer"></div>
							<div class="c-ip-reviews__user">
								<?php
								if ( ! empty( $item['image']['id'] ) ) {
									if ( $image_meta = ideapark_image_meta( $item['image']['id'], 'thumbnail' ) ) {
										echo ideapark_wrap( ideapark_img( $image_meta, 'c-ip-reviews__thumb' ), '<div class="c-ip-reviews__thumb-wrap">', '</div>' );
									}
								} ?>
								<div class="c-ip-reviews__info">
									<?php echo ideapark_wrap( $item['reviewer_name'], '<div class="c-ip-reviews__name">', ideapark_wrap( $stars_element, '<div class="c-ip-reviews__rating">', '</div>' ) . '</div>' ); ?>
									<?php echo ideapark_wrap( $item['reviewer_occupation'], '<div class="c-ip-reviews__occupation">', '</div>' ); ?>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}

	protected function render_stars( $icon, $rating ) {
		$floored_rating = (int) $rating;
		$stars_html     = '';

		for ( $stars = 1; $stars <= 5; $stars ++ ) {
			if ( $stars <= $floored_rating ) {
				$stars_html .= '<i class="c-ip-reviews__star-full">' . $icon . '</i>';
			} elseif ( $floored_rating + 1 === $stars && $rating !== $floored_rating ) {
				$stars_html .= '<i class="c-ip-reviews__star-' . ( $rating - $floored_rating ) * 10 . '">' . $icon . '</i>';
			} else {
				$stars_html .= '<i class="c-ip-reviews__star-empty">' . $icon . '</i>';
			}
		}

		return $stars_html;
	}
}
