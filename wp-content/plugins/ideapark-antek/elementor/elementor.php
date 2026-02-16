<?php

defined( 'ABSPATH' ) or die();

class Ideapark_Elementor {

	private static $_instance = null;

	private $_version;
	private $file;
	private $dir;
	private $widgets_dir;
	private $assets_dir;
	private $assets_url;
	private $_token;

	function __construct( $file, $version = '1.0.0' ) {

		$this->_version    = $version;
		$this->file        = $file;
		$this->dir         = dirname( $this->file );
		$this->widgets_dir = trailingslashit( $this->dir ) . 'elementor';
		$this->assets_dir  = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url  = esc_url( rtrim( plugins_url( '/assets/', $this->file ), '/' ) );
		$this->_token      = 'ideapark_antek';

		include( 'wrapper-link-module.php' );
		Ideapark\WrapperLinks::instance();

		include( 'max-width-module.php' );
		Ideapark\MaxWidth::instance();

		add_action( 'elementor/init', [ $this, 'load_elementor_widgets' ] );
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_widget_category' ], 1 );
		add_action( 'elementor/widgets/register', [ $this, 'elementor_widgets_init' ] );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'elementor_add_js' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'elementor_add_css' ] );
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'elementor_add_css_editor' ] );
		add_action( 'wp_ajax_ideapark_product_filter', [ $this, 'ajax_product_filter' ] );
		add_action( 'wp_ajax_nopriv_ideapark_product_filter', [ $this, 'ajax_product_filter' ] );
		add_action( 'wp_insert_post', [ $this, 'set_canvas_template' ], 999, 3 );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_scripts' ], 100 );

		add_filter( 'elementor/files/allow_unfiltered_upload', '__return_true' );
	}


	function is_elementor() {
		return class_exists( 'Elementor\Plugin' );
	}

	public function set_canvas_template( $post_ID, $post, $update ) {
		if ( ! $update && $post->post_type == 'html_block' && $this->is_elementor() ) {
			update_post_meta( $post_ID, '_wp_page_template', 'elementor_canvas' );
		}
	}

	public function frontend_scripts() {
		if ( $GLOBALS['pagenow'] != 'wp-login.php' && ! is_admin() ) {
			if ( function_exists( 'ideapark_add_style' ) ) {
				ideapark_add_style( 'ideapark-elementor', $this->assets_url . '/css/style.min.css', [], $this->mtime( $this->assets_dir . '/css/style.min.css' ), 'all', $this->assets_dir . '/css/style.min.css' );
				ideapark_add_script( 'ideapark-elementor', $this->assets_url . '/js/site.js', [ 'jquery' ], $this->mtime( $this->assets_dir . '/js/site.js' ), true, $this->assets_dir . '/js/site.js' );

			} else {
				wp_enqueue_style( 'ideapark-elementor', $this->assets_url . '/css/style.min.css', [], $this->mtime( $this->assets_dir . '/css/style.min.css' ), 'all' );
				wp_enqueue_script( 'ideapark-elementor', $this->assets_url . '/js/site.js', [ 'jquery' ], $this->mtime( $this->assets_dir . '/js/site.js' ), true );
			}
		}
	}

	public function add_widget_category( $elements_manager ) {

		$elements_manager->add_category(
			'ideapark-elements',
			[
				'title' => esc_html__( 'Antek Widgets', 'ideapark-antek' ),
				'icon'  => 'antek-logo',
			]
		);

	}

	public function load_elementor_widgets() {
		require_once $this->widgets_dir . '/heading.php';
		require_once $this->widgets_dir . '/page-header.php';
		require_once $this->widgets_dir . '/button.php';
		require_once $this->widgets_dir . '/slider.php';
		require_once $this->widgets_dir . '/filter.php';
		require_once $this->widgets_dir . '/categories.php';
		require_once $this->widgets_dir . '/counter.php';
		require_once $this->widgets_dir . '/steps.php';
		require_once $this->widgets_dir . '/icon-list-1.php';
		require_once $this->widgets_dir . '/reviews.php';
		require_once $this->widgets_dir . '/news.php';
		require_once $this->widgets_dir . '/social.php';
		require_once $this->widgets_dir . '/product-carousel.php';
		require_once $this->widgets_dir . '/woocommerce-carousel.php';
		require_once $this->widgets_dir . '/offer-list.php';
		require_once $this->widgets_dir . '/team.php';
		require_once $this->widgets_dir . '/accordion.php';
		require_once $this->widgets_dir . '/shop-categories.php';
	}

	public function elementor_widgets_init() {
		if ( class_exists( 'Ideapark_Elementor_Heading' ) ) {
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Heading() );
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Page_Header() );

			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Button() );
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Slider() );

			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Filter() );
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Categories() );

			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Counter() );
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Steps() );

			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Icon_List_1() );
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Reviews() );

			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_News() );
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Social() );

			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Product_Carousel() );
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Woocommerce_Carousel() );

			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Offer_List() );
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Team() );

			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Accordion() );
			\Elementor\Plugin::instance()->widgets_manager->register( new Ideapark_Elementor_Shop_Categories() );
		}
	}

	public function elementor_add_js() {
		if ( ideapark_is_elementor_preview_mode() ) {
			wp_register_script( $this->_token . '-elementor', esc_url( $this->assets_url ) . '/js/elementor.js', [ 'jquery' ], $this->_version );
			wp_enqueue_script( $this->_token . '-elementor' );
		}
	}

	public function elementor_add_css() {
	}

	public function elementor_add_css_editor() {
		wp_register_style( $this->_token . '-elementor', esc_url( $this->assets_url ) . '/css/elementor.css', [], $this->_version );
		wp_enqueue_style( $this->_token . '-elementor' );
	}

	public function mtime( $file ) {
		/**
		 * @var WP_Filesystem_Base $wp_filesystem
		 */
		global $wp_filesystem;
		if ( ! empty( $file ) ) {
			if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );

				return $wp_filesystem->mtime( $file );
			}
		}

		return '';
	}

	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()


	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ideapark-antek' ), $this->_version );
	} // End __clone ()


	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ideapark-antek' ), $this->_version );
	} // End __wakeup ()

	function ajax_product_filter() {
		if ( isset( $_POST['id'] ) && ( $term_id = absint( $_POST['id'] ) ) &&
		     isset( $_POST['limit'] ) && ( $limit = absint( $_POST['limit'] ) ) &&
		     isset( $_POST['sort'] ) && ( $sort = $_POST['sort'] ) ) {

			$args = [
				'numberposts' => $limit,
				'post_type'   => 'catalog',
			];

			$args['tax_query'] = [
				[
					'taxonomy' => 'vehicle_type',
					'field'    => 'term_id',
					'terms'    => [ $term_id ],
				]
			];

			if ( isset( $_POST['on_sale'] ) && $_POST['on_sale'] == 'yes' ) {
				$args['meta_query'] = [
					[
						'key'     => 'sale',
						'value'   => '',
						'compare' => '!=',
					],
				];
			}

			switch ( $sort ) {
				case 'menu_order':
					break;
				case 'rand':
					$args['orderby'] = 'rand';
					break;
				case 'date':
					$args['orderby']          = 'date';
					$args['order']            = 'DESC';
					$args['suppress_filters'] = true;
					break;
				case 'price':
					$args['meta_key'] = 'price';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'ASC';
					break;
				case 'price-desc':
					$args['meta_key'] = 'price';
					$args['orderby']  = 'meta_value_num';
					$args['order']    = 'DESC';
					break;
			}

			$posts = get_posts( $args );
			global $post;
			foreach ( $posts as $post ) {
				setup_postdata( $post );
				ideapark_get_template_part( 'templates/vehicle', [ 'layout' => 'vert' ] );
			}
		}
		exit;
	}
}