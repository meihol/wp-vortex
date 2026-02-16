<?php
/*
 * Plugin Name: Antek Core
 * Version: 4.11
 * Description: Core plugin for Antek theme.
 * Author: parkofideas.com
 * Author URI: http://parkofideas.com
 * Text Domain: ideapark-antek
 * Domain Path: /lang/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'IDEAPARK_ANTEK_FUNC_VERSION', '4.11' );

define( 'IDEAPARK_ANTEK_FUNC_IS_AJAX', function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) );
define( 'IDEAPARK_ANTEK_FUNC_IS_AJAX_HEARTBEAT', IDEAPARK_ANTEK_FUNC_IS_AJAX && ! empty( $_POST['action'] ) && ( $_POST['action'] == 'heartbeat' ) );

$theme_obj = wp_get_theme();

if ( empty( $theme_obj ) || strtolower( $theme_obj->get( 'TextDomain' ) ) != 'antek' && strtolower( $theme_obj->get( 'TextDomain' ) ) != 'antek-child' ) {

	add_filter( 'plugin_row_meta', function ( $links, $file ) {
		if ( $file == plugin_basename( __FILE__ ) ) {
			$row_meta = [
				'warning' => '<b style="vertical-align:middle;display:inline-flex;align-items:center;border:solid 2px #dc3545;padding: 2px 10px;color: #dc3545"><span class="dashicons dashicons-warning" style="margin-right: 5px;"></span>' . esc_html__( 'The Antek theme is not activated! This plugin works only with Antek theme', 'ideapark-antek' ) . '</b>',
			];

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}, 10, 2 );

	return;
}

if ( ! empty( $theme_obj ) && version_compare( IDEAPARK_ANTEK_FUNC_VERSION, $theme_obj->parent() ? $theme_obj->parent()->get( 'Version' ) : $theme_obj->get( 'Version' ), '!=' ) ) {

	add_filter( 'plugin_row_meta', function ( $links, $file ) {
		if ( $file == plugin_basename( __FILE__ ) ) {
			$row_meta = [
				'warning' => '<b style="vertical-align:middle;display:inline-flex;align-items:center;border:solid 2px #dc3545;padding: 2px 10px;color: #dc3545;"><span class="dashicons dashicons-warning" style="margin-right: 5px;"></span>' . sprintf( esc_html__( 'The Antek theme version and the theme core plugin version must be the same. Please update the plugin to version %s', 'ideapark-antek' ), IDEAPARK_VERSION ) . '</b>',
			];

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}, 10, 2 );
}

$ip_dir = dirname( __FILE__ );

$ideapark_default_details = [
	'sale'           => [
		'name'         => __( 'Badge text', 'ideapark-antek' ),
		'show_in_list' => 1,
		'show_on_page' => 1,
	],
	'download'       => [
		'name'         => __( 'Attached File (PDF)', 'ideapark-antek' ),
		'show_in_list' => 1,
		'show_on_page' => 1,
	],
	'price_delivery' => [
		'name'         => __( 'Price for delivery', 'ideapark-antek' ),
		'show_in_list' => 1,
		'show_on_page' => 1,
		'is_numeric'   => 1,
	],
	'price_month'    => [
		'name'         => __( 'Price per month', 'ideapark-antek' ),
		'show_in_list' => 1,
		'show_on_page' => 1,
		'is_numeric'   => 1,
	],
	'price_week'     => [
		'name'         => __( 'Price per week', 'ideapark-antek' ),
		'show_in_list' => 1,
		'show_on_page' => 1,
		'is_numeric'   => 1,
	],
	'price'          => [
		'name'         => __( 'Price per day', 'ideapark-antek' ),
		'show_in_list' => 1,
		'show_on_page' => 1,
		'is_numeric'   => 1,
	],
	'location'       => [
		'name'         => __( 'Location', 'ideapark-antek' ),
		'show_in_list' => 1,
		'show_on_page' => 1,
	],
	'vehicle_type'   => [
		'name'         => __( 'Type', 'ideapark-antek' ),
		'show_in_list' => 0,
		'show_on_page' => 0,
	],
];

$ideapark_minimum_days_conditional_error = '';

require_once( $ip_dir . '/elementor/elementor.php' );
require_once( $ip_dir . '/importer/importer.php' );
require_once( $ip_dir . '/includes/class-ideapark.php' );
require_once( $ip_dir . '/includes/svg-support.php' );
require_once( $ip_dir . '/includes/class-rwmb.php' );
require_once( $ip_dir . '/includes/class-ideapark-post-type.php' );
require_once( $ip_dir . '/includes/class-ideapark-taxonomy.php' );
require_once( $ip_dir . '/includes/mb-settings-page/mb-settings-page.php' );
require_once( $ip_dir . '/includes/meta-box-group/meta-box-group.php' );
require_once( $ip_dir . '/includes/class-ideapark-custom-fonts.php' );

register_activation_hook( __FILE__, 'ideapark_activation' );
register_deactivation_hook( __FILE__, 'ideapark_deactivation' );

function Ideapark_Antek_Elementor() {
	$instance = Ideapark_Elementor::instance( __FILE__, IDEAPARK_ANTEK_FUNC_VERSION );

	return $instance;
}

Ideapark_Antek_Elementor();

/**
 * Returns the main instance of ideapark_foodz to prevent the need to use globals.
 *
 * @return Ideapark_Antek object
 */

function Ideapark_Antek() {
	$instance = Ideapark_Antek::instance( __FILE__, IDEAPARK_ANTEK_FUNC_VERSION );

	return $instance;
}

Ideapark_Antek();

function Ideapark_Antek_Importer() {
	$instance = Ideapark_Importer::instance( __FILE__, IDEAPARK_ANTEK_FUNC_VERSION );

	return $instance;
}

Ideapark_Antek_Importer();

if ( ! function_exists( 'ideapark_get_numeric_fields' ) ) {
	function ideapark_get_numeric_fields( $is_term_output = false ) {
		static $numeric, $numeric_sql;

		if ( $numeric !== null ) {
			if ( $is_term_output ) {
				return $numeric;
			} else {
				return $numeric_sql;
			}
		}

		$terms = get_terms( [
			'taxonomy'   => 'detail',
			'hide_empty' => false,
		] );

		$numeric = [];

		foreach ( $terms as $term ) {
			$meta       = get_term_meta( $term->term_id );
			$term->meta = $meta;
			if ( ! empty( $meta['is_numeric'][0] ) && $term->slug && ! in_array( $term->slug, [
					'price',
					'price_week',
					'price_month',
					'price_delivery'
				] ) ) {
				$numeric[ $term->slug ]     = $term;
				$numeric_sql[ $term->slug ] = esc_sql( $term->slug );
			}
		}

		if ( $is_term_output ) {
			return $numeric;
		} else {
			return $numeric_sql;
		}
	}
}

if ( ! function_exists( 'ideapark_check_plugin_version' ) ) {
	function ideapark_check_plugin_version() {
		global $wpdb;
		$current_version = get_option( 'ideapark_antek_plugin_version', '' );
		if ( ! defined( 'IFRAME_REQUEST' ) && ! IDEAPARK_ANTEK_FUNC_IS_AJAX_HEARTBEAT && ( version_compare( $current_version, IDEAPARK_ANTEK_FUNC_VERSION, '!=' ) ) ) {

			// Check if we are not already running this routine.
			if ( 'yes' === get_transient( 'ideapark_antek_installing' ) ) {
				return;
			}
			set_transient( 'ideapark_antek_installing', 'yes', MINUTE_IN_SECONDS * 10 );

			$wpdb->hide_errors();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$collate = '';

			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}

			$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}antek_order` (
`antek_order_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`vehicle_id` int(11) unsigned NOT NULL,
`date_start` datetime NOT NULL,
`date_end` datetime NOT NULL,
`order_id` int(11) unsigned DEFAULT NULL,
PRIMARY KEY (`antek_order_id`),
KEY `order_id` (`order_id`),
KEY `date_start` (`date_start`),
KEY `vehicle_id` (`vehicle_id`)
) {$collate};";

			dbDelta( $sql );

			$sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}antek_price` (
`vehicle_id` int(11) unsigned NOT NULL,
`price` decimal(15,4) unsigned NULL,
`price_week` decimal(15,4) unsigned NULL,
`price_month` decimal(15,4) unsigned NULL,
`price_delivery` decimal(15,4) unsigned NULL,
PRIMARY KEY (`vehicle_id`),
KEY `vehicle_id` (`vehicle_id`)
) {$collate};";

			dbDelta( $sql );

			$sql = "CREATE TABLE `{$wpdb->prefix}antek_price_cond` (
`vehicle_id` int(11) unsigned NOT NULL,
`condition_id` int(11) unsigned NOT NULL,
`price` decimal(15,4) unsigned NOT NULL,
PRIMARY KEY (`condition_id`,`vehicle_id`),
KEY `vehicle_id` (`vehicle_id`),
KEY `condition_id` (`condition_id`)
) {$collate};";

			dbDelta( $sql );

			if ( ! $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}antek_price_cond" ) ) {
				$wpdb->query( "
				REPLACE INTO {$wpdb->prefix}antek_price_cond (vehicle_id, condition_id, price) 
				SELECT post_id, 0, meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'price'" );
			}

			$sql = "CREATE TABLE `{$wpdb->prefix}antek_filter` (
`vehicle_id` int(11) unsigned NOT NULL,
`field` varchar(32) NOT NULL DEFAULT '',
`value` decimal(15,2) NOT NULL,
KEY `vehicle_id` (`vehicle_id`),
KEY `value` (`value`,`field`)
) {$collate};";

			dbDelta( $sql );


			if (
				/*				version_compare( $current_version, '1.4', '<' ) && version_compare( IDEAPARK_ANTEK_FUNC_VERSION, '1.4', '>=' )
								||*/
				$wpdb->get_var( "SELECT IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$wpdb->prefix}antek_price' AND COLUMN_NAME = 'price' LIMIT 1;" ) == 'NO'
			) {
				$sql = "ALTER TABLE `{$wpdb->prefix}antek_price` CHANGE `price` `price` DECIMAL(15,4) UNSIGNED NULL;";
				$wpdb->query( $sql );
			}

			wp_cache_flush();

			if ( ! $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}antek_price" ) ) {
				$sql = "REPLACE INTO {$wpdb->prefix}antek_price (vehicle_id, price, price_week, price_month, price_delivery) 
SELECT p.ID, m_day.meta_value, m_week.meta_value, m_month.meta_value, m_delivery.meta_value
FROM {$wpdb->posts} p
LEFT JOIN {$wpdb->postmeta} m_day ON m_day.meta_key = 'price' AND m_day.post_id = p.ID
LEFT JOIN {$wpdb->postmeta} m_week ON m_week.meta_key = 'price_week' AND m_week.post_id = p.ID
LEFT JOIN {$wpdb->postmeta} m_month ON m_month.meta_key = 'price_month' AND m_month.post_id = p.ID
LEFT JOIN {$wpdb->postmeta} m_delivery ON m_delivery.meta_key = 'price_delivery' AND m_delivery.post_id = p.ID
WHERE p.post_type='catalog'
GROUP BY p.ID";
				$wpdb->query( $sql );
			}

			if ( ! $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}antek_filter" ) ) {

				if ( $numeric = ideapark_get_numeric_fields() ) {
					$sql = "REPLACE INTO {$wpdb->prefix}antek_filter (vehicle_id, field, value) 
SELECT p.ID, m.meta_key, CAST(m.meta_value AS DECIMAL(15,2))
FROM {$wpdb->posts} p
INNER JOIN {$wpdb->postmeta} m ON m.meta_key IN('" . implode( "','", $numeric ) . "') AND m.post_id = p.ID
WHERE p.post_type='catalog' && m.meta_value IS NOT NULL AND m.meta_value != ''";
					$wpdb->query( $sql );
				}
			}


			$sql = "CREATE TABLE `{$wpdb->prefix}antek_stock` (
`vehicle_id` int(11) unsigned NOT NULL,
`day` date NOT NULL,
`cnt` int(11) unsigned NOT NULL,
PRIMARY KEY (`day`,`vehicle_id`),
KEY `vehicle_id` (`vehicle_id`)
) {$collate};";

			dbDelta( $sql );

			if ( ideapark_mod( 'unlimited_booking' ) && ! $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}antek_stock" ) ) {
				ideapark_recalculate_stock();
			}

			$sql = "CREATE TABLE `{$wpdb->prefix}antek_delivery` (
`vehicle_id` int(11) unsigned NOT NULL,
`location_id` int(11) unsigned NOT NULL,
`price` decimal(15,4) unsigned DEFAULT NULL,
PRIMARY KEY (`vehicle_id`,`location_id`),
KEY `vehicle_id` (`vehicle_id`),
KEY `location_id` (`location_id`)
) {$collate};";

			dbDelta( $sql );

			if ( ! $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}antek_delivery" ) ) {

				if ( ideapark_mod( 'price_type' ) == 'cond' ) {
					$wpdb->query( "
					REPLACE INTO {$wpdb->prefix}antek_delivery (vehicle_id, location_id, price) 
					SELECT post_id, 0, meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'price_delivery'" );
				} else {
					$wpdb->query( "
					REPLACE INTO {$wpdb->prefix}antek_delivery (vehicle_id, location_id, price) 
					SELECT vehicle_id, 0, price_delivery FROM {$wpdb->prefix}antek_price WHERE price_delivery IS NOT NULL" );
				}

				ideapark_save_all_vehicle();
			}

			delete_transient( 'ideapark_antek_installing' );
			update_option( 'ideapark_antek_plugin_version', IDEAPARK_ANTEK_FUNC_VERSION );
		}
	}

	add_action( 'init', 'ideapark_check_plugin_version', 998 );
}

if ( ! function_exists( 'ideapark_activation' ) ) {
	function ideapark_activation() {
		//wp_clear_scheduled_hook( 'ideapark_antek_refresh_reserved_event' );
		//wp_schedule_event( time(), 'daily', 'ideapark_antek_refresh_reserved_event' );
	}
}

if ( ! function_exists( 'ideapark_deactivation' ) ) {
	function ideapark_deactivation() {
		//wp_clear_scheduled_hook( 'ideapark_antek_refresh_reserved_event' );
	}
}

if ( ! function_exists( 'ideapark_clear_stock' ) ) {
	function ideapark_clear_stock() {
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}antek_stock" );
	}
}

if ( ! function_exists( 'ideapark_recalculate_stock' ) ) {
	function ideapark_recalculate_stock( $vehicle_id = 0, $start = null, $end = null ) {
		global $wpdb;

		if ( $start && is_string( $start ) ) {
			$start = new DateTime( $start );
		}

		if ( $end && is_string( $end ) ) {
			$end = new DateTime( $end );
		}

		if ( ! $vehicle_id ) {
			$vehicle_ids = $wpdb->get_col( "SELECT DISTINCT vehicle_id FROM {$wpdb->prefix}antek_order WHERE order_id IS NOT NULL" );
		} else {
			$vehicle_ids = [ $vehicle_id ];
		}

		foreach ( $vehicle_ids as $vehicle_id ) {
			$where = [ $wpdb->prepare( "vehicle_id=%d", $vehicle_id ) ];
			if ( $start ) {
				$where[] = $wpdb->prepare( "day>=%s", $start->format( 'Y-m-d' ) );
			}

			if ( $end ) {
				$where[] = $wpdb->prepare( "day<=%s", $end->format( 'Y-m-d' ) );
			}

			$wpdb->query( "DELETE FROM {$wpdb->prefix}antek_stock WHERE " . implode( ' AND ', $where ) );

			$where = [ 'order_id IS NOT NULL', $wpdb->prepare( "vehicle_id=%d", $vehicle_id ) ];
			if ( $start ) {
				$where[] = $wpdb->prepare( "(date_start>=%s OR date_end>=%s)", $start->format( 'Y-m-d' ), $start->format( 'Y-m-d' ) );
			}

			if ( $end ) {
				$where[] = $wpdb->prepare( "(date_start<=%s OR date_end<=%s)", $end->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) );
			}

			$order_ids = $wpdb->get_col( "SELECT DISTINCT order_id FROM {$wpdb->prefix}antek_order WHERE " . implode( ' AND ', $where ) );

			if ( $wpdb->num_rows ) {
				if ( ! $start ) {
					$start_text = $wpdb->get_var( "SELECT MIN(date_start) FROM {$wpdb->prefix}antek_order WHERE " . implode( ' AND ', $where ) );
					$start      = new DateTime( $start_text );
				}
				if ( ! $end ) {
					$end_text = $wpdb->get_var( "SELECT MAX(date_end) FROM {$wpdb->prefix}antek_order WHERE " . implode( ' AND ', $where ) );
					$end      = new DateTime( $end_text );
				}
				$one_day_interval = new DateInterval( 'P1D' );
				$current          = clone $start;
				$bookings_dates   = [];

				foreach ( $order_ids as $order_id ) {
					/** @var $order WC_Order */
					if ( ( $order = wc_get_order( $order_id ) ) && ! in_array( $order->get_status(), [
							'failed',
							'refunded',
							'cancelled'
						] ) ) {
						$items       = $order->get_items();
						$product_ids = ideapark_get_all_default_products();

						foreach ( $items as $item ) {
							$item_data = $item->get_data();

							if ( in_array( $item_data['product_id'], $product_ids ) ) {
								$item_meta_data = $item->get_meta_data();
								$meta_data      = [];

								foreach ( $item_meta_data as $meta ) {
									$meta_data[ $meta->key ] = $meta->value;
								}

								if ( $meta_data['vehicle_id'] == $vehicle_id ) {
									$bookings_dates[] = [
										'start' => new DateTime( $meta_data['start'] ),
										'end'   => new DateTime( date( 'Y-m-d', strtotime( $meta_data['end'] ) + (int) ideapark_mod( 'cleaning_days' ) * 24 * 60 * 60 ) ),
										'cnt'   => $item_data['quantity'],
									];
								}
							}
						}
					}
				}

				if ( $bookings_dates ) {
					do {
						$cnt = 0;
						foreach ( $bookings_dates as $bookings_date ) {
							if ( $current >= $bookings_date['start'] && ( ideapark_mod( 'booking_type' ) == 'day' ? $current <= $bookings_date['end'] : $current < $bookings_date['end'] ) ) {
								$cnt += $bookings_date['cnt'];
							}
						}
						if ( $cnt ) {
							$wpdb->query( $wpdb->prepare( "REPLACE INTO {$wpdb->prefix}antek_stock (vehicle_id, day, cnt) VALUES (%d, %s, %d)", $vehicle_id, $current->format( 'Y-m-d' ), $cnt ) );
						}
						$current->add( $one_day_interval );
					} while ( $current <= $end );
				}
			}
		}
	}
}

if ( ! function_exists( 'ideapark_check_delivery' ) ) {
	function ideapark_check_delivery() {
		global $wpdb;

		if ( ! function_exists( 'ideapark_mod' ) ) {
			return;
		}

		ideapark_mod_set_temp( '_delivery_on',
			! ! $wpdb->get_var( "SELECT vehicle_id FROM {$wpdb->prefix}antek_delivery WHERE vehicle_id LIMIT 1" )
		);
	}

	add_action( 'wp_loaded', 'ideapark_check_delivery', 20 );
}

if ( ! function_exists( 'ideapark_add_default_details' ) ) {
	function ideapark_add_default_details() {
		global $ideapark_default_details;

		add_filter( 'detail_row_actions', function ( $actions, $tag ) use ( $ideapark_default_details ) {
			if ( array_key_exists( $tag->slug, $ideapark_default_details ) ) {
				unset( $actions['delete'] );
			}

			return $actions;
		}, 100, 2 );

		add_action( 'ideapark_before_import_terms', function () use ( $ideapark_default_details ) {
			$terms = get_terms( [
				'taxonomy'   => 'detail',
				'hide_empty' => false,
			] );

			remove_all_actions( 'delete_term_taxonomy' );
			foreach ( $terms as $term ) {
				if ( array_key_exists( $term->slug, $ideapark_default_details ) ) {
					wp_delete_term( $term->term_id, 'detail' );
				}
			}
		} );

		if ( is_admin() && taxonomy_exists( 'detail' ) ) {
			$hash        = md5( serialize( $ideapark_default_details ) );
			$stored_hash = get_option( 'ideapark_antek_default_details_hash' );

			if ( $hash != $stored_hash ) {

				$terms = get_terms( [
					'taxonomy'   => 'detail',
					'hide_empty' => false,
				] );
				/*
				 * @var $term WP_Term;
				 * */
				$terms_slug = [];
				$terms_id   = [];

				foreach ( $terms as $term ) {
					$terms_slug[] = $term->slug;
					if ( array_key_exists( $term->slug, $ideapark_default_details ) ) {
						$terms_id[] = $term->term_id;
					}
				}

				foreach ( $ideapark_default_details as $slug => $meta ) {
					if ( ! in_array( $slug, $terms_slug ) ) {
						$new_term = wp_insert_term( $meta['name'], 'detail', [
							'description' => '',
							'parent'      => 0,
							'slug'        => $slug,
						] );

						if ( ! is_wp_error( $new_term ) ) {
							$term_id    = $new_term['term_id'];
							$terms_id[] = $term_id;

							foreach ( $meta as $meta_key => $meta_value ) {
								if ( $meta_key != 'name' ) {
									add_term_meta( $term_id, $meta_key, $meta_value, true );
								}
							}

							do_action( 'wpml_register_single_string', 'antek', 'Details - ' . $meta['name'], $meta['name'] );
							if ( ! empty( $meta['unit'] ) ) {
								do_action( 'wpml_register_single_string', 'antek', 'Details - ' . $meta['unit'], $meta['unit'] );
							}
						}
					}
				}

				if ( ! empty( $terms_id ) ) {
					update_option( '_ideapark_antek_protected_terms', $terms_id );
				} else {
					delete_option( '_ideapark_antek_protected_terms' );
				}

				update_option( 'ideapark_antek_default_details_hash', $stored_hash );
			}
		}

		if ( ( $protected_terms = get_option( '_ideapark_antek_protected_terms' ) ) && ( is_array( $protected_terms ) ) ) {

			add_action( 'delete_term_taxonomy', function ( $tt_id ) use ( $protected_terms ) {
				if ( in_array( $tt_id, $protected_terms ) ) {
					wp_die( esc_html__( 'Can`t delete default product details!', 'ideapark-antek' ) );
				}
				if ( ( $term = get_term( $tt_id ) ) && ( $term->taxonomy == 'detail' ) ) {
					delete_option( 'ideapark_antek_default_details_hash' );
				}
			}, 100, 1 );
			add_action( 'admin_head', function () use ( $protected_terms ) {
				if ( get_current_screen()->id == 'edit-detail' ) {
					$classes      = [];
					$classes_lock = [];
					$classes_hide = [];
					foreach ( $protected_terms as $term_id ) {
						$classes[]      = "#the-list #cb-select-{$term_id}";
						$classes[]      = "#the-list #edit-{$term_id}.inline-edit-row .inline-edit-col > label:nth-child(2)";
						$classes_lock[] = "#the-list #tag-{$term_id} .check-column:before";
					}
					echo "<style>" . implode( ', ', $classes ) . "{display: none}" . implode( ', ', $classes_hide ) . "{visibility: hidden}" . implode( ', ', $classes_lock ) . "{content: \"\\f160\"; font-family: dashicons; display: inline-block; line-height: 1; font-weight: 400; font-style: normal; width: 20px; height: 20px; font-size: 20px; vertical-align: top; text-align: center; margin-left: 8px}</style>";
					if ( ! empty( $_GET['tag_ID'] ) && in_array( $_GET['tag_ID'], $protected_terms ) ) {
						echo "<style>#delete-link{display: none}</style>";
					}
				}
			} );

		}
	}

	add_action( 'init', 'ideapark_add_default_details', 999 );
}

if ( ! function_exists( 'ideapark_theme_widgets_init' ) ) {
	function ideapark_theme_widgets_init() {
		$ip_dir = dirname( __FILE__ );
		include_once( $ip_dir . "/widgets/latest-posts-widget.php" );
		include_once( $ip_dir . "/widgets/filter-price-widget.php" );
		include_once( $ip_dir . "/widgets/filter-detail-widget.php" );
		include_once( $ip_dir . "/widgets/filter-types-widget.php" );
		include_once( $ip_dir . "/widgets/filter-reset-widget.php" );
	}

	add_action( 'widgets_init', 'ideapark_theme_widgets_init' );
}

if ( ! function_exists( 'ideapark_init_custom_post_types' ) ) {
	function ideapark_init_custom_post_types() {

		Ideapark_Antek()->register_post_type(
			'catalog',
			esc_html__( 'Catalog', 'ideapark-antek' ),
			esc_html__( 'Item', 'ideapark-antek' ),
			esc_html__( 'Catalog', 'ideapark-antek' ),
			[
				'menu_icon'           => 'dashicons-images-alt2',
				'public'              => true,
				'hierarchical'        => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 4,
				'capability_type'     => 'post',
				'supports'            => [
					'title',
					'editor',
					'excerpt',
					'thumbnail',
					'author',
					'page-attributes'
				],
				'has_archive'         => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => [
					'slug'       => get_option( 'antek_mod_catalog_base', 'catalog' ),
					'with_front' => false
				],
			]
		);

		Ideapark_Antek()->register_post_type(
			'html_block',
			esc_html__( 'HTML Blocks', 'ideapark-antek' ),
			esc_html__( 'HTML Block', 'ideapark-antek' ),
			esc_html__( 'Static HTML blocks for using in widgets and in templates', 'ideapark-antek' ),
			[
				'menu_icon'           => 'dashicons-media-code',
				'public'              => true,
				'hierarchical'        => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'capability_type'     => 'post',
				'supports'            => [
					'title',
					'editor',
				],
				'has_archive'         => false,
				'query_var'           => true,
				'can_export'          => true,
			]
		);

		Ideapark_Antek()->set_sorted_post_types( [ 'catalog' ] );

		Ideapark_Antek()->register_taxonomy(
			'vehicle_type',
			[ 'catalog' ],
			esc_html__( 'Categories', 'ideapark-antek' ),
			esc_html__( 'Category', 'ideapark-antek' ),
			'',
			[
				'hierarchical'       => true,
				'show_in_nav_menus'  => false,
				'show_tagcloud'      => false,
				'show_in_rest'       => false,
				'show_in_quick_edit' => false,
				'meta_box_cb'        => false,
				'publicly_queryable' => true,
				'hide_description'   => true,
				'custom_column'      => [
					'icon_image' => [
						'type'      => 'icon_image',
						'title'     => __( 'Thumbnail', 'ideapark-antek' ),
						'term_meta' => [
							'icon'  => 'font-icon',
							'image' => 'image',
						],
						'position'  => 3,
					]
				],
				'rewrite'            => [ 'slug' => get_option( 'antek_mod_type_base', 'vehicle_type' ) ],
			]
		);

		Ideapark_Antek()->register_taxonomy(
			'extra_option',
			[ 'catalog' ],
			esc_html__( 'Extra options', 'ideapark-antek' ),
			esc_html__( 'Option', 'ideapark-antek' ),
			'',
			[
				'hierarchical'       => false,
				'show_in_nav_menus'  => false,
				'show_tagcloud'      => false,
				'show_in_rest'       => false,
				'show_in_quick_edit' => false,
				'meta_box_cb'        => false,
				'publicly_queryable' => false,
				'hide_description'   => true,
				'hide_slug'          => true,
				'custom_column'      => [
					'type'  => [
						'type'      => 'text',
						'title'     => __( 'Type', 'ideapark-antek' ),
						'term_meta' => 'type',
						'position'  => 2,
					],
					'price' => [
						'type'      => 'text',
						'title'     => __( 'Price', 'ideapark-antek' ) . ( function_exists( 'get_woocommerce_currency_symbol' ) ? ' (' . get_woocommerce_currency_symbol() . ')' : '' ),
						'term_meta' => 'price',
						'position'  => 2,
					],
					'max'   => [
						'type'      => 'text',
						'title'     => __( 'Max', 'ideapark-antek' ),
						'term_meta' => 'max',
						'position'  => 2,
					],
				],
				'sort_notice'        => __( 'You can reorder rows using drag and drop.', 'ideapark-antek' ),
				'rewrite'            => false,
			]
		);

		Ideapark_Antek()->register_taxonomy(
			'detail',
			[ 'catalog' ],
			esc_html__( 'Details', 'ideapark-antek' ),
			esc_html__( 'Detail', 'ideapark-antek' ),
			'',
			[
				'hierarchical'       => false,
				'show_in_nav_menus'  => false,
				'show_tagcloud'      => false,
				'show_in_rest'       => false,
				'show_in_quick_edit' => false,
				'meta_box_cb'        => false,
				'publicly_queryable' => false,
				'hide_description'   => true,
				'hide_slug'          => true,
				'hide_posts'         => true,
				'custom_column'      => [
					'show_in_list' => [
						'type'      => 'yes_no',
						'title'     => __( 'Show in list', 'ideapark-antek' ),
						'term_meta' => 'show_in_list',
						'position'  => 2,
					],
					'show_on_page' => [
						'type'      => 'yes_no',
						'title'     => __( 'Show on details page', 'ideapark-antek' ),
						'term_meta' => 'show_on_page',
						'position'  => 2,
					],
					'is_numeric'   => [
						'type'      => 'yes_no',
						'title'     => __( 'Numeric value', 'ideapark-antek' ),
						'term_meta' => 'is_numeric',
						'position'  => 2,
					],
				],
				'modified'           => function ( $term_id = null, $tt_id = null ) {
					ideapark_details_clear_cache();
					if ( $languages = apply_filters( 'wpml_active_languages', [] ) ) {
						if ( $term = get_term( $term_id ) ) {
							do_action( 'wpml_register_single_string', 'antek', 'Details - ' . $term->name, $term->name );
							$meta = get_term_meta( $term_id );
							if ( ! empty( $meta['unit'][0] ) ) {
								do_action( 'wpml_register_single_string', 'antek', 'Details - ' . $meta['unit'][0], $meta['unit'][0] );
							}
						}
					}
				},
				'sort_notice'        => __( 'You can reorder rows using drag and drop.', 'ideapark-antek' ),
				'rewrite'            => false,
			]
		);

		Ideapark_Antek()->register_taxonomy(
			'location',
			[ 'catalog' ],
			esc_html__( 'Locations', 'ideapark-antek' ),
			esc_html__( 'Location', 'ideapark-antek' ),
			'',
			[
				'hierarchical'       => false,
				'show_in_nav_menus'  => false,
				'show_tagcloud'      => false,
				'show_in_rest'       => false,
				'show_in_quick_edit' => false,
				'meta_box_cb'        => false,
				'publicly_queryable' => false,
				'hide_description'   => true,
				'hide_slug'          => true,
				'rewrite'            => false,
			]
		);

		Ideapark_Antek()->register_taxonomy(
			'condition',
			[ 'catalog' ],
			esc_html__( 'Conditions', 'ideapark-antek' ),
			esc_html__( 'Condition', 'ideapark-antek' ),
			esc_html__( 'Conditional pricing', 'ideapark-antek' ),
			[
				'hierarchical'       => false,
				'show_in_nav_menus'  => false,
				'show_tagcloud'      => false,
				'show_in_rest'       => false,
				'show_in_quick_edit' => false,
				'meta_box_cb'        => false,
				'publicly_queryable' => false,
				'hide_description'   => true,
				'hide_slug'          => true,
				'hide_posts'         => true,
				'sort_notice'        => __( 'You can reorder rows using drag and drop. The rows above are higher priority.', 'ideapark-antek' ),
				'rewrite'            => false,
			]
		);

		Ideapark_Antek()->set_sorted_taxonomies( [
			'extra_option',
			'vehicle_type',
			'detail',
			'condition'
		] );

		Ideapark_Antek()->set_rwmb_validation_taxonomies( [
			'extra_option',
			'vehicle_type',
			'detail',
			'condition'
		] );
	}

	add_action( 'after_setup_theme', 'ideapark_init_custom_post_types' );

	add_action( 'save_post_html_block', function () { //todo-me disable when Elementor fix this bug
		static $is_cleared;
		if ( empty( $is_cleared ) && ideapark_is_elementor() ) {
			$elementor_instance = Elementor\Plugin::instance();
			$elementor_instance->files_manager->clear_cache();
			$is_cleared = true;
		}
	}, 99 );
}

if ( ! function_exists( 'ideapark_set_details_transient' ) ) {
	function ideapark_set_details_transient( $return = '', $is_force = false ) {

		$lang = apply_filters( 'wpml_current_language', null );
		if ( $lang ) {
			$lang = '_' . $lang;
		}

		if ( $return == 'list' ) {
			if ( ! $is_force && ( $details_list = get_transient( 'ideapark_details_list' . $lang ) ) !== false && is_array( $details_list ) ) {
				return $details_list;
			}
		} elseif ( $return == 'page' ) {
			if ( ! $is_force && ( $details_page = get_transient( 'ideapark_details_page' . $lang ) ) !== false && is_array( $details_page ) ) {
				return $details_page;
			}
		}

		$details_list = [];
		$details_page = [];
		$terms        = get_terms( [
			'taxonomy'   => 'detail',
			'hide_empty' => false,
		] );
		foreach ( $terms as $term ) {
			$meta = get_term_meta( $term->term_id );

			$args = [
				'name' => $term->name,
				'unit' => isset( $meta['unit'][0] ) ? $meta['unit'][0] : '',
			];
			if ( ! empty( $meta['show_in_list'][0] ) ) {
				$details_list[ $term->slug ] = $args;
			}
			if ( ! empty( $meta['show_on_page'][0] ) ) {
				$details_page[ $term->slug ] = $args;
			}
		}

		set_transient( 'ideapark_details_list' . $lang, $details_list );
		set_transient( 'ideapark_details_page' . $lang, $details_page );

		if ( $return == 'list' ) {
			return $details_list;
		} elseif ( $return == 'page' ) {
			return $details_page;
		}
	}
}

if ( ! function_exists( 'ideapark_custom_plugin_meta_boxes' ) ) {
	function ideapark_custom_plugin_meta_boxes( $meta_boxes ) {
		global $wp_locale;

		if ( ! function_exists( 'ideapark_mod' ) ) {
			return $meta_boxes;
		}

		$fonts_info = get_option( 'ideapark_fonts_info' );
		$icons      = [];

		if ( ! empty( $fonts_info['fonts'] ) ) {

			foreach ( $fonts_info['fonts'] as $_font_name => $_font ) {
				foreach ( $_font['unicodes'] as $class_name => $code ) {
					$icons[ $class_name ] = $class_name;
				}
			}
		}

		// ==================================== Extra option fields ====================================

		$fields = [
			[
				'name' => esc_html__( 'Description (tooltip)', 'ideapark-antek' ),
				'id'   => 'description',
				'type' => 'textarea',
				'size' => 40
			],
			[
				'name'    => esc_html__( 'Type', 'ideapark-antek' ),
				'id'      => 'type',
				'type'    => 'radio',
				'inline'  => true,
				'std'     => 'day',
				'options' => [
					'day'   => esc_html__( 'Day', 'ideapark-antek' ),
					'total' => esc_html__( 'Total', 'ideapark-antek' ),
				],
			],
			[
				'name'  => esc_html__( 'Price', 'ideapark-antek' ) . ( function_exists( 'get_woocommerce_currency_symbol' ) ? ' (' . get_woocommerce_currency_symbol() . ')' : '' ),
				'id'    => 'price',
				'type'  => 'text',
				'size'  => 2,
				'class' => 'ideapark-short-input',
			],
			[
				'name'              => esc_html__( 'Conditional price', 'ideapark-antek' ),
				'id'                => 'price_cond',
				'label_description' => '<a href="' . admin_url( 'edit-tags.php?taxonomy=condition&post_type=catalog' ) . '">' . esc_html__( 'Manage conditions', 'ideapark-antek' ) . '</a>',
				'type'              => 'fieldset_condition',
				'size'              => 2,
				'options'           => [
					'price'     => esc_html__( 'Price', 'ideapark-antek' ) . ( function_exists( 'get_woocommerce_currency_symbol' ) ? ' (' . get_woocommerce_currency_symbol() . ')' : '' ),
					'condition' => esc_html__( 'Condition', 'ideapark-antek' ),
				],
				'clone'             => true,
				'class'             => 'ideapark-antek-condition',
			],
			[
				'name' => esc_html__( 'Always included', 'ideapark-antek' ),
				'id'   => 'always_included',
				'type' => 'checkbox',
				'std'  => 0,
			],
			[
				'name'  => esc_html__( 'Maximum quantity in one order', 'ideapark-antek' ),
				'id'    => 'max',
				'type'  => 'number',
				'std'   => 1,
				'min'   => 1,
				'step'  => 1,
				'size'  => 1,
				'class' => 'ideapark-short-input',
			],

		];

		$meta_boxes[] = [
			'title'      => '',
			'taxonomies' => 'extra_option',
			'fields'     => $fields,
			'validation' => [
				'rules' => [
					'type'  => [
						'required' => true,
					],
					'max'   => [
						'required' => true,
					],
					'price' => [
						'required' => true,
						'number'   => true,
					],
				],
			]
		];

		// ==================================== Vehicle type fields ====================================

		$fields[] = [
			'type' => 'heading',
			'name' => esc_html__( 'Categories widget options', 'ideapark-antek' ),
		];

		$fields = [
			[
				'name'             => esc_html__( 'Image', 'ideapark-antek' ),
				'id'               => 'image',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
				'max_status'       => false,
			],
		];

		if ( $icons ) {
			$fields[] = [
				'name'            => esc_html__( 'Icon', 'ideapark-antek' ),
				'desc'            => esc_html__( 'Select an Icon if the Image is not set', 'ideapark-antek' ),
				'id'              => 'font-icon',
				'type'            => 'select_advanced',
				'options'         => $icons,
				'multiple'        => false,
				'select_all_none' => false,
				'js_options'      => [
					'templateResult' => 'ideaparkSelectWithIcons',
				],
			];
		}

		$fields[] = [
			'name'    => esc_html__( 'Short Description', 'ideapark-antek' ),
			'id'      => 'short_description',
			'type'    => 'textarea',
			'options' => [
				'textarea_rows' => 4
			],
		];

		$meta_boxes[] = [
			'title'      => '',
			'taxonomies' => 'vehicle_type',
			'fields'     => $fields,
		];

		$fields       = [
			[
				'name'             => '',
				'id'               => 'header_bg_image',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
				'force_delete'     => false,
				'max_status'       => false,
				'image_size'       => 'thumbnail',
			],
		];
		$meta_boxes[] = [
			'title'      => __( 'Header background image', 'ideapark-antek' ),
			'context'    => 'side',
			'post_types' => [ 'page', 'post', 'catalog', 'product' ],
			'priority'   => 'low',
			'fields'     => $fields
		];

		$fields[0]['name'] = __( 'Header background image', 'ideapark-antek' );

		$meta_boxes[] = [
			'title'      => '',
			'taxonomies' => [ 'category', 'post_tag', 'product_cat', 'vehicle_type' ],
			'fields'     => $fields,
		];

		// ==================================== HTML Blocks ====================================

		$fields       = [
			[
				'name'              => __( 'HTML block (top)', 'ideapark-antek' ),
				'label_description' => '<a target="_blank" href="' . esc_url( admin_url( 'edit.php?post_type=html_block' ) ) . '">' . esc_html__( 'Manage html blocks', 'ideapark-antek' ) . '</a>',
				'id'                => 'html_block_top',
				'type'              => 'post',
				'post_type'         => 'html_block',
				'field_type'        => 'select_advanced',
				'placeholder'       => __( 'Select a HTML block', 'ideapark-antek' ),
				'query_args'        => [
					'post_status'    => [ 'publish' ],
					'posts_per_page' => - 1,
				],
			],
			[
				'name'              => __( 'HTML block (bottom)', 'ideapark-antek' ),
				'label_description' => '<a target="_blank" href="' . esc_url( admin_url( 'edit.php?post_type=html_block' ) ) . '">' . esc_html__( 'Manage html blocks', 'ideapark-antek' ) . '</a>',
				'id'                => 'html_block_bottom',
				'type'              => 'post',
				'post_type'         => 'html_block',
				'field_type'        => 'select_advanced',
				'placeholder'       => __( 'Select a HTML block', 'ideapark-antek' ),
				'query_args'        => [
					'post_status'    => [ 'publish' ],
					'posts_per_page' => - 1,
				],
			],
			[
				'name'      => esc_html__( 'Show HTML blocks only on the first page ', 'ideapark-antek' ),
				'id'        => 'html_block_first_page',
				'type'      => 'switch',
				'style'     => 'rounded',
				'on_label'  => 'Yes',
				'off_label' => 'No',
			],
			[
				'name'      => esc_html__( 'Show top block above the sidebar', 'ideapark-antek' ),
				'id'        => 'top_block_above',
				'type'      => 'switch',
				'style'     => 'rounded',
				'on_label'  => 'Yes',
				'off_label' => 'No',
			]
		];
		$meta_boxes[] = [
			'title'      => '',
			'taxonomies' => [ 'product_cat', 'vehicle_type' ],
			'fields'     => $fields,
		];

		// ==================================== Detail fields ====================================

		$fields = [];

		$fields[] = [
			'name'      => esc_html__( 'Show in list', 'ideapark-antek' ),
			'id'        => 'show_in_list',
			'type'      => 'switch',
			'style'     => 'rounded',
			'on_label'  => 'Yes',
			'off_label' => 'No',
		];

		$fields[] = [
			'name'      => esc_html__( 'Show on details page', 'ideapark-antek' ),
			'id'        => 'show_on_page',
			'type'      => 'switch',
			'style'     => 'rounded',
			'on_label'  => 'Yes',
			'off_label' => 'No',
		];

		$fields[] = [
			'name'      => esc_html__( 'Numeric value', 'ideapark-antek' ),
			'id'        => 'is_numeric',
			'type'      => 'switch',
			'style'     => 'rounded',
			'on_label'  => 'Yes',
			'off_label' => 'No',
		];

		$fields[] = [
			'name' => esc_html__( 'Unit of measurement', 'ideapark-antek' ),
			'desc' => esc_html__( 'For numeric value only', 'ideapark-antek' ),
			'id'   => 'unit',
			'type' => 'text',
			'size' => 2
		];

		$meta_boxes[] = [
			'title'      => '',
			'taxonomies' => 'detail',
			'fields'     => $fields,
		];

		// ==================================== Location fields ====================================

		$fields = [
			[
				'name' => esc_html__( 'Address', 'ideapark-antek' ),
				'id'   => 'address',
				'type' => 'textarea',
				'size' => 40
			],
		];

		$meta_boxes[] = [
			'title'      => '',
			'taxonomies' => 'location',
			'fields'     => $fields,
		];

		// ==================================== Condition fields ====================================

		$days = [];
		for ( $i = 1; $i <= 31; $i ++ ) {
			$days[ $i ] = $i;
		}

		$years = [];
		for ( $i = date( 'Y' ); $i < (int) date( 'Y' ) + 10; $i ++ ) {
			$years[ $i ] = $i;
		}

		$fields = [
			[
				'name'              => esc_html__( 'Selected days (from)', 'ideapark-antek' ),
				'label_description' => esc_html__( 'The condition is triggered if the user has selected a given number of days or more in the calendar', 'ideapark-antek' ),
				'id'                => 'days_from',
				'class'             => 'ideapark-antek-cond-days',
				'type'              => 'number',
				'std'               => 1,
				'min'               => 1,
				'step'              => 1,
				'size'              => 2,
			],
			[
				'name'              => esc_html__( 'Selected days (to)', 'ideapark-antek' ),
				'label_description' => esc_html__( 'This is not used if nothing is entered', 'ideapark-antek' ),
				'id'                => 'days_to',
				'class'             => 'ideapark-antek-cond-days',
				'type'              => 'number',
				'std'               => '',
				'min'               => 1,
				'step'              => 1,
				'size'              => 2,
			],
			[
				'name'              => esc_html__( 'From date', 'ideapark-antek' ),
				'label_description' => esc_html__( 'This is not used if nothing is selected', 'ideapark-antek' ),
				'id'                => 'from_date',
				'type'              => 'date',
				'js_options'        => [
					'dateFormat'      => 'yy-mm-dd',
					'showButtonPanel' => false,
				],
				'inline'            => false,
				'timestamp'         => false,
				'size'              => 3,
			],
			[
				'name'              => esc_html__( 'To date', 'ideapark-antek' ),
				'label_description' => esc_html__( 'This is not used if nothing is selected', 'ideapark-antek' ),
				'id'                => 'to_date',
				'type'              => 'date',
				'js_options'        => [
					'dateFormat'      => 'yy-mm-dd',
					'showButtonPanel' => false,
				],
				'inline'            => false,
				'timestamp'         => false,
				'size'              => 3,
			],
			[
				'name'              => esc_html__( 'First days', 'ideapark-antek' ),
				'label_description' => esc_html__( 'Triggered for a specified number of days from the beginning of the selected range. This is not used if nothing is entered.', 'ideapark-antek' ),
				'id'                => 'days_first',
				'type'              => 'number',
				'std'               => '',
				'min'               => 1,
				'step'              => 1,
				'size'              => 2,
			],
			[
				'name'              => esc_html__( 'Fixed price', 'ideapark-antek' ),
				'label_description' => esc_html__( 'The price of all days meeting this condition will be fixed, regardless of the number of days. Together with the "First days" option, it can be used to set a fixed cost for the first N days of the rent. This condition only works for rented products.', 'ideapark-antek' ),
				'id'                => 'fixed_price',
				'type'              => 'switch',
				'style'             => 'rounded',
				'on_label'          => 'Yes',
				'off_label'         => 'No',
			],
			[
				'name'              => esc_html__( 'Weekdays', 'ideapark-antek' ),
				'label_description' => esc_html__( 'The weekdays filter is not used if nothing is selected', 'ideapark-antek' ),
				'id'                => 'weekdays',
				'type'              => 'checkbox_list',
				'options'           => $wp_locale->weekday,
				'inline'            => true,
				'select_all_none'   => true,
			],
			[
				'name'              => esc_html__( 'Days', 'ideapark-antek' ),
				'label_description' => esc_html__( 'The days filter is not used if nothing is selected', 'ideapark-antek' ),
				'id'                => 'days',
				'class'             => 'ideapark-antek-cond-days',
				'type'              => 'checkbox_list',
				'options'           => $days,
				'inline'            => true,
				'select_all_none'   => true,
			],
			[
				'name'              => esc_html__( 'Months', 'ideapark-antek' ),
				'label_description' => esc_html__( 'The months filter is not used if nothing is selected', 'ideapark-antek' ),
				'id'                => 'months',
				'type'              => 'checkbox_list',
				'options'           => $wp_locale->month,
				'inline'            => true,
				'select_all_none'   => true,
			],
			[
				'name'              => esc_html__( 'Years', 'ideapark-antek' ),
				'label_description' => esc_html__( 'The years filter is not used if nothing is selected', 'ideapark-antek' ),
				'id'                => 'years',
				'type'              => 'checkbox_list',
				'options'           => $years,
				'inline'            => true,
				'select_all_none'   => true,
			],
			[
				'name'              => esc_html__( 'Pickup location', 'ideapark-antek' ),
				'label_description' => esc_html__( 'The location filter is not used if nothing is selected', 'ideapark-antek' ),
				'id'                => 'location',
				'type'              => 'taxonomy_advanced',
				'taxonomy'          => 'location',
				'field_type'        => 'checkbox_list',
				'select_all_none'   => true,
			],
			[
				'name'              => esc_html__( 'Categories', 'ideapark-antek' ),
				'label_description' => esc_html__( 'The categories filter is not used if nothing is selected', 'ideapark-antek' ),
				'id'                => 'type',
				'type'              => 'taxonomy_advanced',
				'taxonomy'          => 'vehicle_type',
				'field_type'        => 'checkbox_list',
				'select_all_none'   => true,
			]
		];

		$meta_boxes[] = [
			'title'      => '',
			'taxonomies' => 'condition',
			'fields'     => $fields,
			'validation' => [
				'rules' => [
					'days_from' => [
						'number' => true,
						'min'    => 1,
					],
					'days_to'   => [
						'number' => true,
						'min'    => 1,
					],
					'from_date' => [
						'date'    => true,
						'pattern' => '\d{4}-\d{2}-\d{2}'
					],
					'to_date'   => [
						'date'    => true,
						'pattern' => '\d{4}-\d{2}-\d{2}'
					]
				]
			]
		];

		// ==================================== Catalog fields ====================================

		$meta_boxes[] = [
			'title'      => esc_html__( 'Booking', 'ideapark-antek' ),
			'post_types' => 'catalog',
			'fields'     => [
				[
					'name'       => '',
					'id'         => 'ideapark-antek-booking',
					'class'      => 'ideapark-antek-booking-wrap',
					'type'       => 'text',
					'attributes' => [
						'disabled' => true
					],
				],
				[
					'name'  => '',
					'std'   => esc_html__( 'Reserve range', 'ideapark-antek' ),
					'id'    => 'ideapark-antek-booking-reserve',
					'type'  => 'button',
					'class' => 'ideapark-antek-inline-button'
				],
				[
					'name'  => '',
					'std'   => esc_html__( 'Remove reservation', 'ideapark-antek' ),
					'id'    => 'ideapark-antek-booking-clear',
					'type'  => 'button',
					'class' => 'ideapark-antek-inline-button'
				],
			]
		];

		if ( ideapark_mod( 'unlimited_booking' ) ) {
			$fields_pricing = [
				[
					'name' => esc_html__( 'Stock quantity', 'ideapark-antek' ),
					'desc' => esc_html__( 'Empty → unlimited booking', 'ideapark-antek' ),
					'id'   => 'stock',
					'type' => 'text',
					'size' => 1,
				]
			];
			$meta_boxes[]   = [
				'title'      => esc_html__( 'Stock management', 'ideapark-antek' ),
				'post_types' => 'catalog',
				'fields'     => $fields_pricing,
				'validation' => [
					'rules' => [
						'stock' => [
							'required' => false,
							'number'   => true,
						],
					],
				]
			];
		}

		$fields           = [];
		$fields_pricing   = [];
		$validation_rules = [];

		$terms = get_terms( [
			'taxonomy'   => 'detail',
			'hide_empty' => false,
		] );

		/*
		 * @var $term WP_Term;
		 * */

		foreach ( $terms as $term ) {
			$slug = $term->slug;
			if ( $lang = apply_filters( 'wpml_current_language', null ) ) {
				$slug = preg_replace( '~-' . $lang . '$~', '', $slug );
			}

			$name      = $term->name;
			$term_meta = get_term_meta( abs( $term->term_id ) );

			switch ( $slug ) {
				case 'vehicle_type':
				case 'location':
					break;

				case 'price':

					$validation_rules[ $term->slug ] = [
						'required' => ideapark_mod( 'price_type' ) == 'cond',
						'number'   => true,
					];

					$fields_pricing[] = [
						'name'  => esc_html__( 'Price on request', 'ideapark-antek' ),
						'id'    => 'price_on_request',
						'type'  => 'checkbox',
						'std'   => 0,
						'class' => 'ideapark-antek-price-on-request',
					];
					$fields_pricing[] = [
						'name'              => esc_html__( 'Custom price', 'ideapark-antek' ),
						'label_description' => esc_html__( 'The text that will be displayed instead of the price in the product grid', 'ideapark-antek' ),
						'id'                => 'custom_price_text',
						'type'              => 'text',
						'class'             => 'ideapark-antek-price',
					];

					if ( ideapark_woocommerce_on() ) {
						$fields_pricing[] = [
							'type'  => 'custom_html',
							'std'   => '<a target="_blank" href="' . esc_url( admin_url( 'customize.php?autofocus[control]=price_type' ) ) . '">' . ( ideapark_mod( 'price_type' ) == 'cond' ? esc_html__( 'Change price type to Day/Week/Month', 'ideapark-antek' ) : esc_html__( 'Change price type to Conditional', 'ideapark-antek' ) ) . '</a>',
							'class' => 'ideapark-antek-price',
						];
						$fields_pricing[] = [
							'name'  => $name . ( function_exists( 'get_woocommerce_currency_symbol' ) ? ' (' . get_woocommerce_currency_symbol() . ')' : '' ),
							'id'    => 'price',
							'type'  => 'text',
							'size'  => 3,
							'class' => 'ideapark-antek-price ideapark-antek-price--' . ideapark_mod( 'price_type' ),
						];
						if ( ideapark_mod( 'price_type' ) == 'cond' ) {
							$fields_pricing[] = [
								'name'              => esc_html__( 'Conditional price', 'ideapark-antek' ),
								'id'                => 'price_cond',
								'label_description' => '<a href="' . admin_url( 'edit-tags.php?taxonomy=condition&post_type=catalog' ) . '">' . esc_html__( 'Manage conditions', 'ideapark-antek' ) . '</a>',
								'type'              => 'fieldset_condition',
								'size'              => 3,
								'options'           => [
									'price'     => $name . ( function_exists( 'get_woocommerce_currency_symbol' ) ? ' (' . get_woocommerce_currency_symbol() . ')' : '' ),
									'condition' => esc_html__( 'Condition', 'ideapark-antek' ),
								],
								'clone'             => true,
								'class'             => 'ideapark-antek-condition ideapark-antek-price',
							];
						}
					}

					break;

				case 'price_week':
				case 'price_month':
				case 'price_delivery':
					if ( ideapark_woocommerce_on() && ( ideapark_mod( 'price_type' ) != 'cond' || $slug == 'price_delivery' ) ) {
						$validation_rules[ $term->slug ] = [
							'required' => false,
							'number'   => true,
						];
						$fields_pricing[]                = [
							'name'  => $name . ( function_exists( 'get_woocommerce_currency_symbol' ) ? ' (' . get_woocommerce_currency_symbol() . ')' : '' ),
							'id'    => $term->slug,
							'type'  => 'text',
							'size'  => 3,
							'desc'  => $slug == 'price_delivery' ? esc_html__( 'Empty → without delivery, 0 → free delivery', 'ideapark-antek' ) : '',
							'class' => $slug == 'price_delivery' ? '' : 'ideapark-antek-price',
						];

						if ( $slug == 'price_delivery' ) {
							$fields_pricing[] = [
								'name'              => ideapark_wp_kses( __( 'Custom delivery price<br>depending on location', 'ideapark-antek' ) ),
								'label_description' => esc_html__( '0 → free delivery,', 'ideapark-antek' ),
								'id'                => 'price_delivery_loc',
								'type'              => 'fieldset_delivery',
								'size'              => 3,
								'options'           => [
									'price'    => esc_html__( 'Price', 'ideapark-antek' ) . ( function_exists( 'get_woocommerce_currency_symbol' ) ? ' (' . get_woocommerce_currency_symbol() . ')' : '' ),
									'location' => esc_html__( 'Location', 'ideapark-antek' ),
								],
								'clone'             => true,
								'class'             => 'ideapark-antek-condition',
							];
						}

						if ( $slug == 'price_delivery' && ideapark_mod( 'unlimited_booking' ) ) {
							$fields_pricing[] = [
								'name'    => esc_html__( 'Calculation of delivery', 'ideapark-antek' ),
								'id'      => 'price_delivery_alg',
								'type'    => 'radio',
								'inline'  => true,
								'std'     => '',
								'options' => [
									''       => esc_html__( '(rent + delivery) * quantity', 'ideapark-antek' ),
									'type-2' => esc_html__( 'rent * quantity + delivery', 'ideapark-antek' ),
								],
							];
						}
					}
					break;

				case 'download':
					$fields[] = [
						'name'             => $name,
						'id'               => $term->slug,
						'type'             => 'file_advanced',
						'max_file_uploads' => 1,
						'max_status'       => false,
					];
					break;

				default:

					if ( ! empty( $term_meta['is_numeric'][0] ) ) {
						$validation_rules[ $term->slug ] = [
							'required' => false,
							'number'   => true,
						];
					}

					$fields[] = [
						'name' => $name,
						'id'   => $term->slug,
						'type' => 'text',
						'size' => 40
					];

					if ( $slug == 'sale' ) {
						$fields[] = [
							'name' => esc_html__( 'Badge custom color', 'ideapark-antek' ),
							'id'   => 'sale_color',
							'type' => 'color'
						];
					}
			};
		}

		$meta_boxes[] = [
			'title'      => esc_html__( 'Pricing', 'ideapark-antek' ),
			'post_types' => 'catalog',
//		'context'    => 'side',
			'fields'     => $fields_pricing,
			'validation' => [
				'rules' => [
					'price'          => [
						'required' => false,
						'number'   => true,
					],
					'price_week'     => [
						'required' => false,
						'number'   => true,
					],
					'price_month'    => [
						'required' => false,
						'number'   => true,
					],
					'price_delivery' => [
						'required' => false,
						'number'   => true,
					],
				],
			]
		];

		$meta_boxes[] = [
			'title'      => esc_html__( 'Details', 'ideapark-antek' ),
			'post_types' => 'catalog',
			'fields'     => $fields,
			'validation' => [
				'rules' => $validation_rules
			]
		];

		$meta_boxes[] = [
			'title'      => esc_html__( 'HTML block', 'ideapark-antek' ),
			'post_types' => 'catalog',
			'fields'     => [
				[
					'name'        => '',
					'id'          => 'html_block_title',
					'placeholder' => esc_html__( 'Enter the block title or leave empty', 'ideapark-antek' ),
					'type'        => 'text',
				],
				[
					'name'              => '',
					'label_description' => '<a target="_blank" href="' . esc_url( admin_url( 'edit.php?post_type=html_block' ) ) . '">' . esc_html__( 'Manage html blocks', 'ideapark-antek' ) . '</a>',
					'id'                => 'html_block',
					'type'              => 'post',
					'post_type'         => 'html_block',
					'field_type'        => 'select_advanced',
					'placeholder'       => esc_html__( 'Select a HTML block', 'ideapark-antek' ),
					'query_args'        => [
						'post_status'    => [ 'publish' ],
						'posts_per_page' => - 1,
					],
				]
			]
		];

		$meta_boxes[] = [
			'title'      => esc_html__( 'Image Gallery', 'ideapark-antek' ),
			'post_types' => 'catalog',
			'context'    => 'side',
			'priority'   => 'low',
			'fields'     => [
				[
					'name'         => '',
					'id'           => 'image_gallery',
					'type'         => 'image_advanced',
					'force_delete' => false,
					'max_status'   => false,
					'image_size'   => 'thumbnail',
				],
			]
		];

		$meta_boxes[] = [
			'title'      => esc_html__( 'Product Video', 'ideapark-antek' ),
			'post_types' => 'catalog',
			'context'    => 'side',
			'priority'   => 'low',
			'fields'     => [
				[
					'name' => __( 'Video URL', 'ideapark-antek' ),
					'desc' => __( 'Enter the url to product video (Youtube, Vimeo etc.).', 'ideapark-antek' ),
					'id'   => 'video_url',
					'type' => 'text',
				],
				[
					'name'             => __( 'Video Thumbnail', 'ideapark-antek' ),
					'id'               => '_ip_product_video_thumb',
					'desc'             => __( 'Leave blank to use the standard thumbnail from youtube.', 'ideapark-antek' ),
					'type'             => 'image_advanced',
					'max_file_uploads' => 1,
					'force_delete'     => false,
					'max_status'       => false,
				],
			],
			'validation' => [
				'rules' => [
					'video_url' => [
						'url' => true,
					],
				],
			]
		];

		$meta_boxes[] = [
			'title'      => esc_html__( 'Category', 'ideapark-antek' ),
			'post_types' => 'catalog',
			'context'    => 'side',
			'priority'   => 'low',
			'fields'     => [
				[
					'name'       => '',
					'id'         => 'vehicle_type',
					'type'       => 'taxonomy',
					'taxonomy'   => 'vehicle_type',
					'field_type' => 'checkbox_tree',
				]
			],
			'validation' => [
				'rules' => [
					'vehicle_type' => [
						'required' => true,
					],
				],
			]
		];
		$meta_boxes[] = [
			'title'      => esc_html__( 'Location', 'ideapark-antek' ),
			'post_types' => 'catalog',
			'context'    => 'side',
			'priority'   => 'low',
			'fields'     => [
				[
					'name'       => '',
					'id'         => 'location',
					'type'       => 'taxonomy',
					'taxonomy'   => 'location',
					'field_type' => 'checkbox_list'
				]
			],
		];

		$meta_boxes[] = [
			'title'      => esc_html__( 'Extra options', 'ideapark-antek' ),
			'post_types' => 'catalog',
			'context'    => 'side',
			'priority'   => 'low',
			'fields'     => [
				[
					'name'       => '',
					'id'         => 'extra_option',
					'type'       => 'taxonomy',
					'taxonomy'   => 'extra_option',
					'field_type' => 'checkbox_list',
				]
			]
		];


		return $meta_boxes;
	}

	add_filter( 'rwmb_meta_boxes', 'ideapark_custom_plugin_meta_boxes', 999 );
}

if ( ! function_exists( 'ideapark_add_extra_column' ) ) {
	function ideapark_add_extra_column( $columns ) {
		if ( empty( $columns ) && ! is_array( $columns ) ) {
			$columns = [];
		}

		unset( $columns['title'], $columns['comments'], $columns['date'], $columns['author'] );

		$show_columns          = [];
		$show_columns['cb']    = '<input type="checkbox" />';
		$show_columns['thumb'] = '<span class="wc-image tips" data-tip="' . esc_attr__( 'Image', 'ideapark-antek' ) . '">' . __( 'Image', 'ideapark-antek' ) . '</span>';
		$show_columns['title'] = __( 'Name', 'ideapark-antek' );

		if ( ideapark_mod( 'unlimited_booking' ) ) {
			$show_columns['stock'] = __( 'Stock', 'ideapark-antek' );
		}

		$show_columns['rental-price'] = __( 'Price', 'ideapark-antek' );
		$show_columns['delivery']     = __( 'Delivery', 'ideapark-antek' );
		$show_columns['vehicle_type'] = __( 'Categories', 'ideapark-antek' );
		$show_columns['badge']        = __( 'Badge', 'ideapark-antek' );
		$show_columns['date']         = __( 'Date', 'ideapark-antek' );

		return array_merge( $show_columns, $columns );
	}

	add_filter( 'manage_catalog_posts_columns', 'ideapark_add_extra_column' );
}

if ( ! function_exists( 'ideapark_manage_extra_column' ) ) {
	function ideapark_manage_extra_column( $column_name, $post_id ) {
		static $object, $meta;

		/**
		 * @var $object WP_Post
		 */

		if ( empty( $object ) || $object->ID !== $post_id ) {
			$object = get_post( $post_id );
			$meta   = get_post_meta( $post_id );
		}

		if ( $object->post_type === 'product' && $column_name == 'name' ) {
			if ( ideapark_mod( 'wc_integration' ) == 'each' && ( $vehicle_id = ideapark_get_product_vehicle_id( $post_id ) ) ) {
				echo "<a href='" . esc_url( admin_url( 'post.php?post=' . $vehicle_id . '&action=edit' ) ) . "' class='ideapark-rental-badge'>" . esc_html__( 'Rental', 'ideapark-antek' ) . "</a>";
			} elseif ( apply_filters( 'wpml_object_id', $post_id, 'product', true, apply_filters( 'wpml_default_language', null ) ) == get_option( 'ideapark_product_default', false ) ) {
				echo "<span class='ideapark-rental-badge'>" . esc_html__( 'Rental', 'ideapark-antek' ) . "</span>";
			}

			return;
		}

		if ( $object->post_type !== 'catalog' ) {
			return;
		}

		switch ( $column_name ) {
			case 'thumb':
				echo '<a href="' . esc_url( get_edit_post_link( $object->ID ) ) . '">' . get_the_post_thumbnail( $post_id, 'thumbnail' ) . '</a>';
				break;

			case 'stock':
				echo ! empty( $meta['stock'][0] ) ? $meta['stock'][0] : esc_html__( 'Unlimited', 'ideapark-antek' );
				break;

			case 'rental-price':
				if ( ! empty( $meta['custom_price_text'][0] ) ) {
					echo '<div>' . esc_html( $meta['custom_price_text'][0] ) . '</div>';
				}
				if ( ! empty( $meta['price_on_request'][0] ) ) {
					echo ideapark_mod( 'price_on_request_label' ) ?: esc_html__( 'Request', 'ideapark-antek' );
				} else {
					if ( ideapark_mod( 'price_type' ) == 'cond' ) {
						echo ! empty( $meta['price'][0] ) ? $meta['price'][0] : '';
					} else {
						echo ( ! empty( $meta['price'][0] ) ? $meta['price'][0] : '' ) . ' / ' . ( ! empty( $meta['price_week'][0] ) ? $meta['price_week'][0] : '' ) . ' / ' . ( ! empty( $meta['price_month'][0] ) ? $meta['price_month'][0] : '' );
					}
				}
				break;

			case 'delivery':
				echo isset( $meta['price_delivery'][0] ) ? ( $meta['price_delivery'][0] !== '0' ? $meta['price_delivery'][0] : esc_html__( 'free', 'ideapark-antek' ) ) : esc_html__( 'no', 'ideapark-antek' );
				break;

			case 'vehicle_type':
				$terms = get_the_terms( $post_id, 'vehicle_type' );
				if ( ! $terms ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					$termlist = [];
					foreach ( $terms as $term ) {
						$termlist[] = '<a href="' . esc_url( admin_url( 'edit.php?vehicle_type=' . $term->slug . '&post_type=catalog' ) ) . ' ">' . esc_html( $term->name ) . '</a>';
					}

					echo implode( ', ', $termlist );
				}
				break;

			case 'badge':
				echo ! empty( $meta['sale'][0] ) ? esc_html( $meta['sale'][0] ) : '';
				break;
		}
	}

	add_filter( 'manage_posts_custom_column', 'ideapark_manage_extra_column', 10, 2 );
}

if ( ! function_exists( 'ideapark_contact_methods' ) ) {
	function ideapark_contact_methods( $contactmethods ) {

		if ( function_exists( 'ideapark_social_networks' ) ) {
			foreach ( ideapark_social_networks() as $code => $name ) {
				$contactmethods[ $code ] = $name;
			}
		}

		return $contactmethods;
	}

	add_filter( 'user_contactmethods', 'ideapark_contact_methods', 10, 1 );
}

if ( ! function_exists( 'ideapark_product_share' ) ) {
	function ideapark_product_share() {
		global $post;

		$esc_permalink = esc_url( get_permalink() );
		$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), false, '' );

		$share_links = [
			'<a target="_blank" href="//www.facebook.com/sharer.php?u=' . $esc_permalink . '" title="' . esc_html__( 'Share on Facebook', 'ideapark-antek' ) . '"><i class="ip-facebook c-product__share-svg c-product__share-svg--facebook"></i></a>',
			'<a target="_blank" href="//twitter.com/share?url=' . $esc_permalink . '" title="' . esc_html__( 'Share on Twitter', 'ideapark-antek' ) . '"><i class="ip-twitter c-product__share-svg c-product__share-svg--twitter"></i></a>',
			'<a target="_blank" href="//pinterest.com/pin/create/button/?url=' . $esc_permalink . ( $product_image ? '&amp;media=' . esc_url( $product_image[0] ) : '' ) . '&amp;description=' . urlencode( get_the_title() ) . '" title="' . esc_html__( 'Pin on Pinterest', 'ideapark-antek' ) . '"><i class="ip-pinterest c-product__share-svg c-product__share-svg--pinterest"></i></a>',
			'<a target="_blank" href="//wa.me/?text=' . $esc_permalink . '" title="' . esc_html__( 'Share on Whatsapp', 'ideapark-antek' ) . '"><i class="ip-whatsapp c-product__share-svg c-product__share-svg--whatsapp"></i></a>'
		];
		?>

		<div class="c-product__share">
			<span class="c-product__share-title"><?php echo esc_html__( 'Share', 'ideapark-antek' ); ?></span>
			<?php
			foreach ( $share_links as $link ) {
				echo ideapark_wrap( $link );
			}
			?>
		</div>
		<?php
	}

	add_action( 'woocommerce_share', 'ideapark_product_share' );
}

if ( ! function_exists( 'ideapark_disable_gutenberg' ) ) {
	function ideapark_disable_gutenberg( $is_enabled, $post_type ) {
		if ( $post_type === 'catalog' ) {
			return false;
		}

		return $is_enabled;
	}

	add_filter( 'use_block_editor_for_post_type', 'ideapark_disable_gutenberg', 10, 2 );
}

if ( ! function_exists( 'ideapark_vehicle_images' ) ) {
	function ideapark_vehicle_images() {
		global $post;
		$images         = [];
		$attachment_ids = get_post_meta( $post->ID, 'image_gallery', false );
		if ( ! is_array( $attachment_ids ) ) {
			$attachment_ids = [];
		}
		if ( has_post_thumbnail() ) {
			array_unshift( $attachment_ids, get_post_thumbnail_id( $post->ID ) );
		}

		if ( $attachment_ids ) {
			foreach ( $attachment_ids as $attachment_id ) {
				if ( ! wp_get_attachment_url( $attachment_id ) ) {
					continue;
				}

				$image = wp_get_attachment_image( $attachment_id, 'large', false, [
					'alt'   => get_the_title( $attachment_id ),
					'class' => 'c-vehicle-details__gallery-img c-vehicle-details__gallery-img--' . ideapark_mod( 'catalog_image_fit' )
				] );

				$full = ideapark_mod( 'product_modal' ) ? wp_get_attachment_image_src( $attachment_id, 'full' ) : false;

				$thumb = wp_get_attachment_image( $attachment_id, 'thumbnail', false, [
					'alt'   => get_the_title( $post->ID ),
					'class' => 'c-vehicle-details__thumbs-img'
				] );

				$images[] = [
					'attachment_id' => $attachment_id,
					'image'         => $image,
					'full'          => $full,
					'thumb'         => $thumb
				];
			}
		}

		if ( $video_url = get_post_meta( $post->ID, 'video_url', true ) ) {
			if ( $video_thumb_id = get_post_meta( $post->ID, '_ip_product_video_thumb', true ) ) {
				$thumb_url = ( $image = wp_get_attachment_image_src( $video_thumb_id, 'woocommerce_gallery_thumbnail' ) ) ? $image[0] : '';
				$image_url = ( $image = wp_get_attachment_image_src( $video_thumb_id, 'large' ) ) ? $image[0] : '';
			} else {
				$pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
				if ( preg_match( $pattern, $video_url, $match ) ) {
					$image_url = 'https://img.youtube.com/vi/' . $match[1] . '/hqdefault.jpg';
					$thumb_url = 'https://img.youtube.com/vi/' . $match[1] . '/mqdefault.jpg';
				} else {
					$image_url = '';
					$thumb_url = '';
				}
			}
			$images[] = [
				'thumb_url' => $thumb_url,
				'image_url' => $image_url,
				'video_url' => $video_url,
			];
		}

		return $images;
	}
}

if ( ! function_exists( 'ideapark_ajax_vehicle_images' ) ) {
	function ideapark_ajax_vehicle_images() {
		ob_start();
		if ( isset( $_REQUEST['product_id'] ) && ( $product_id = absint( $_REQUEST['product_id'] ) ) ) {

			$images         = [];
			$attachment_ids = get_post_meta( $product_id, 'image_gallery', false );

			if ( ! is_array( $attachment_ids ) ) {
				$attachment_ids = [];
			}

			if ( has_post_thumbnail( $product_id ) ) {
				array_unshift( $attachment_ids, get_post_thumbnail_id( $product_id ) );
			}

			if ( $attachment_ids ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$image    = wp_get_attachment_image_src( $attachment_id, 'full' );
					$images[] = [
						'src' => $image[0],
						'w'   => $image[1],
						'h'   => $image[2],
					];
				}
			}

			if ( $video_url = get_post_meta( $product_id, 'video_url', true ) ) {
				$images[] = [
					'html' => ideapark_wrap( wp_oembed_get( $video_url ), '<div class="pswp__video-wrap">', '</div>' )
				];
			}
			ob_end_clean();
			wp_send_json( [ 'images' => $images ] );
		}
		ob_end_clean();
	}

	add_action( 'wp_ajax_ideapark_vehicle_images', 'ideapark_ajax_vehicle_images' );
	add_action( 'wp_ajax_nopriv_ideapark_vehicle_images', 'ideapark_ajax_vehicle_images' );

}

if ( ! function_exists( 'ideapark_check_dates' ) ) {
	function ideapark_check_dates( $vehicle_id, $start, $end, $exclude_order_id = 0, $start_orig = '' ) {
		global $wpdb;

		if ( is_string( $start ) ) {
			$start = new DateTime( $start );
		} else {
			$start = clone $start;
		}

		if ( is_string( $end ) ) {
			$end = new DateTime( $end );
		} else {
			$end = clone $end;
		}

		/**
		 * @var $start DateTime
		 * @var $end   DateTime
		 */

		if ( ideapark_mod( 'cleaning_days' ) && ! $exclude_order_id ) {
			$end->add( new DateInterval( 'P' . ideapark_mod( 'cleaning_days' ) . 'D' ) );
		}

		$start_text = $start->format( 'Y-m-d' );
		$end_text   = $end->format( 'Y-m-d' );

		$sql = $wpdb->prepare( "
			SELECT vehicle_id 
			FROM {$wpdb->prefix}antek_order 
			WHERE vehicle_id=%d " . ( $exclude_order_id && $start_orig ? $wpdb->prepare( " AND ( !( order_id = %d AND date_start = %s) OR order_id IS NULL)", $exclude_order_id, $start_orig ) : '' ) . ( ideapark_mod( 'unlimited_booking' ) ? " AND order_id IS NULL " : "" ) . " AND " . ideapark_mysql_dates_cond() . " LIMIT 1", $vehicle_id, $start_text, $start_text, $end_text, $end_text, $start_text, $end_text );

		$is_available = ! $wpdb->get_var( $sql );
		if ( $is_available && ideapark_mod( 'unlimited_booking' ) && ( $stock = get_post_meta( $vehicle_id, 'stock', true ) ) && ! $exclude_order_id ) {
			$sql          = $wpdb->prepare( "
			SELECT day 
			FROM {$wpdb->prefix}antek_stock 
			WHERE vehicle_id=%d AND cnt>=%d AND " . ( ideapark_mod( 'booking_type' ) == 'day' ? "day>=%s AND day<=%s" : "day>=%s AND day<%s" ) . " LIMIT 1", $vehicle_id, $stock, $start_text, $end_text );
			$is_available = ! $wpdb->get_var( $sql );
		}

		return $is_available;
	}
}

if ( ! function_exists( 'ideapark_ajax_calc_total' ) ) {
	function ideapark_ajax_calc_total() {
		global $ideapark_minimum_days_conditional_error;

		ob_start();
		$is_vehicle_id_valid = ! empty( $_REQUEST['vehicle_id'] ) && ( $vehicle_id = ideapark_get_orig_catalog_id( $vehicle_id_orig = absint( $_REQUEST['vehicle_id'] ) ) ) && ( $vehicle = get_post( $vehicle_id ) );
		if ( $dates = ideapark_get_filter_dates_range( true ) ) {
			$days                = $dates['diff'];
			$is_price_on_request = get_post_meta( $vehicle_id, 'price_on_request', true );
			$total_price         = ideapark_get_price( $vehicle_id, $dates['start'], $dates['end'] );
			$is_wc_price         = function_exists( 'wc_price' );
			$minimal_days        = ideapark_get_minimum_days( true );
			$maximum_days        = ideapark_mod( 'maximum_days' );
			$pickup_dropoff_days = explode( ',', ideapark_mod( 'pickup_dropoff_days' ) );
			$holidays            = ideapark_get_holidays();
			$quantity            = max( 1, ! empty( $_REQUEST['quantity'] ) ? absint( $_REQUEST['quantity'] ) : 1 );
			$delivery_price      = $dates['delivery'] ? ideapark_get_delivery_price( $vehicle_id, $dates['location_id'], true ) : 0;
			$delivery_alg        = $dates['delivery'] && $delivery_price ? get_post_meta( $vehicle_id_orig, 'price_delivery_alg', true ) : '';
			if ( is_array( $delivery_alg ) ) {
				$delivery_alg = $delivery_alg[0];
			}
		} else {
			ob_end_clean();
			wp_send_json( [] );
		}

		$extra = [];
		if ( function_exists( 'wc_price' ) && ( $extra_options = get_the_terms( $vehicle_id_orig, 'extra_option' ) ) ) {
			foreach ( $extra_options as $i => $extra_option ) {
				$term_meta    = get_term_meta( $extra_option->term_id );
				$option_price = ideapark_get_extra_total( $extra_option->term_id, 1, true, true );
				ob_start();
				if ( $option_price != $term_meta['price'][0] ) { ?>
					<del><?php echo wc_price( $term_meta['price'][0] ); ?></del>
					<?php echo wc_price( $option_price ); ?>
				<?php } else { ?>
					<?php echo wc_price( $term_meta['price'][0] ); ?>
				<?php }
				$extra[ $extra_option->term_id ] = trim( ob_get_clean() );
			}
		}

		if ( $is_price_on_request ) {
			$is_dates_available = ! ! ideapark_check_dates( $vehicle_id, $dates['start'], $dates['end'] );
			ob_end_clean();
			if ( $delivery_price === null ) {
				wp_send_json( [
					'total'            => '',
					'days'             => $days,
					'price_on_request' => 1,
					'not_popup'        => 1,
					'custom_message'   => esc_html__( "Delivery is not available at this location", 'ideapark-antek' ),
				] );
			} elseif ( ! in_array( $dates['start']->format( 'N' ), $pickup_dropoff_days ) || in_array( $dates['start']->format( 'Y-m-d' ), $holidays ) ) {
				wp_send_json( [
					'total'            => '',
					'days'             => $days,
					'price_on_request' => 1,
					'custom_message'   => esc_html( sprintf( __( "%s is not available for pick up", 'ideapark-antek' ), $dates['start']->format( ideapark_date_format() ) ) ),
				] );
			} elseif ( ! in_array( $dates['end']->format( 'N' ), $pickup_dropoff_days ) || in_array( $dates['end']->format( 'Y-m-d' ), $holidays ) ) {
				wp_send_json( [
					'total'            => '',
					'days'             => $days,
					'price_on_request' => 1,
					'custom_message'   => esc_html( sprintf( __( "%s is not available for drop off", 'ideapark-antek' ), $dates['end']->format( ideapark_date_format() ) ) ),
				] );
			} elseif ( $days < $minimal_days || $ideapark_minimum_days_conditional_error ) {
				wp_send_json( [
					'total'            => '',
					'days'             => $days,
					'price_on_request' => 1,
					'minimal_days'     => $minimal_days,
					'custom_message'   => $ideapark_minimum_days_conditional_error,
				] );
			} elseif ( $maximum_days && $days > $maximum_days ) {
				wp_send_json( [
					'total'            => '',
					'days'             => $days,
					'price_on_request' => 1,
					'minimal_days'     => $minimal_days
				] );
			} else {
				if ( ( $errors = ideapark_check_cart_availability( [
						'vehicle_id' => $vehicle_id,
						'start'      => $_s = $dates['start']->format( 'Y-m-d' ),
						'end'        => $_e = $dates['end']->format( 'Y-m-d' ),
						'quantity'   => $quantity,
					] ) ) && ! empty( $errors[ $vehicle_id ]['message'] ) && $errors[ $vehicle_id ]['start'] == $_s && $errors[ $vehicle_id ]['end'] == $_e ) {
					wp_send_json( [
						'total'            => '',
						'days'             => $days,
						'price_on_request' => 1,
						'not_popup'        => 1,
						'custom_message'   => esc_html( $errors[ $vehicle_id ]['message'] ),
					] );
				} else {
					wp_send_json( [
						'total'            => $is_dates_available ? ideapark_mod( 'price_on_request_label' ) : '',
						'price_on_request' => 1,
						'days'             => $days,
						'extra'            => $extra
					] );
				}
			}
		}
		$price         = $is_wc_price ? wc_price( $total_price ) : $total_price;
		$price_per_day = $days ? $total_price / $days : 0;
		$price_per_day = $is_wc_price ? wc_price( $price_per_day ) : round( $price_per_day );

		if ( $delivery_price === null ) {
			wp_send_json( [
				'price'          => $price,
				'price_per_day'  => $price_per_day,
				'days'           => $days,
				'not_popup'      => 1,
				'custom_message' => esc_html__( "Delivery is not available at this location", 'ideapark-antek' ),
			] );
		} elseif ( ! in_array( $dates['start']->format( 'N' ), $pickup_dropoff_days ) || in_array( $dates['start']->format( 'Y-m-d' ), $holidays ) ) {
			wp_send_json( [
				'price'          => $price,
				'price_per_day'  => $price_per_day,
				'days'           => $days,
				'custom_message' => esc_html( sprintf( __( "%s is not available for pick up", 'ideapark-antek' ), $dates['start']->format( ideapark_date_format() ) ) ),
			] );
		} elseif ( ! in_array( $dates['end']->format( 'N' ), $pickup_dropoff_days ) || in_array( $dates['end']->format( 'Y-m-d' ), $holidays ) ) {
			wp_send_json( [
				'price'          => $price,
				'price_per_day'  => $price_per_day,
				'days'           => $days,
				'custom_message' => esc_html( sprintf( __( "%s is not available for drop off", 'ideapark-antek' ), $dates['end']->format( ideapark_date_format() ) ) ),
			] );
		} elseif ( $days < $minimal_days || $ideapark_minimum_days_conditional_error ) {
			ob_end_clean();
			wp_send_json( [
				'price'          => $price,
				'price_per_day'  => $price_per_day,
				'days'           => $days,
				'minimal_days'   => $minimal_days,
				'custom_message' => $ideapark_minimum_days_conditional_error,
			] );
		} elseif ( $maximum_days && $days > $maximum_days ) {
			ob_end_clean();
			wp_send_json( [
				'price'         => $price,
				'price_per_day' => $price_per_day,
				'days'          => $days,
				'maximum_days'  => $maximum_days
			] );
		} else if (
			$is_wc_price &&
			$is_vehicle_id_valid &&
			ideapark_check_dates( $vehicle_id, $dates['start'], $dates['end'] ) &&
			$total_price
		) {

			if ( ( $errors = ideapark_check_cart_availability( [
					'vehicle_id' => $vehicle_id,
					'start'      => $_s = $dates['start']->format( 'Y-m-d' ),
					'end'        => $_e = $dates['end']->format( 'Y-m-d' ),
					'quantity'   => $quantity,
				] ) ) && ! empty( $errors[ $vehicle_id ]['message'] ) && $errors[ $vehicle_id ]['start'] == $_s && $errors[ $vehicle_id ]['end'] == $_e ) {
				wp_send_json( [
					'price'          => $price,
					'price_per_day'  => $price_per_day,
					'days'           => $days,
					'not_popup'      => 1,
					'custom_message' => esc_html( $errors[ $vehicle_id ]['message'] ),
				] );
			}

			$total = $total_price;
			if ( ! empty( $_REQUEST['extra'] ) && is_array( $_REQUEST['extra'] ) ) {
				foreach ( $_REQUEST['extra'] as $extra_id => $extra_qty ) {
					$total += ideapark_get_extra_total( $extra_id, $extra_qty );
				}
			}

			ob_end_clean();
			wp_send_json( [
				'total'         => wc_price( $delivery_alg == 'type-2' ? ( $total * $quantity + $delivery_price ) : ( ( $total + $delivery_price ) * $quantity ) ),
				'price'         => $price,
				'price_per_day' => $price_per_day,
				'days'          => $days,
				'extra'         => $extra
			] );
		} else {
			ob_end_clean();
			wp_send_json( [
				'price'         => $price,
				'price_per_day' => $price_per_day,
				'days'          => $days,
				'extra'         => []
			] );
		}
	}

	add_action( 'wp_ajax_ideapark_calc_total', 'ideapark_ajax_calc_total' );
	add_action( 'wp_ajax_nopriv_ideapark_calc_total', 'ideapark_ajax_calc_total' );

}

if ( ! function_exists( 'ideapark_get_extra_total' ) ) {
	function ideapark_get_extra_total( $extra_id, $extra_qty, $use_conditional = true, $per_day = false ) {
		$total = 0;
		if ( ( $dates = ideapark_get_filter_dates_range( true ) ) && ( $days = $dates['diff'] ) ) {
			if ( $extra_id && $extra_qty && ( $term_meta = get_term_meta( abs( (int) $extra_id ) ) ) ) {
				if ( ! empty( $term_meta['price'][0] ) && ! empty( $term_meta['type'][0] ) ) {
					if ( $use_conditional && ! empty( $term_meta['price_cond'][0] ) && ( $active_conditions = ideapark_get_active_conditions( $dates['start'], $dates['end'] ) ) ) {
						$price_cond    = unserialize( $term_meta['price_cond'][0] );
						$conditions    = [];
						$selected_days = [];
						$is_found      = false;
						for ( $i = 1; $i <= $days; $i ++ ) {
							$selected_days[] = $i;
						}
						foreach ( $price_cond as $cond ) {
							$conditions[ (int) $cond['condition'] ] = (float) $cond['price'];
						}
						foreach ( $active_conditions as $condition_id => $condition_days ) {
							if ( array_key_exists( $condition_id, $conditions ) ) {
								if ( $term_meta['type'][0] == 'day' ) {
									$total += floatval( $conditions[ $condition_id ] ) * $extra_qty * sizeof( array_intersect( $condition_days, $selected_days ) );
								} else {
									$total    += floatval( $conditions[ $condition_id ] ) * $extra_qty;
									$is_found = true;
									break;
								}
								$selected_days = array_diff( $selected_days, $condition_days );
								if ( ! sizeof( $selected_days ) ) {
									break;
								}
							}
						}
						if ( sizeof( $selected_days ) ) {
							if ( $term_meta['type'][0] == 'day' ) {
								$total += floatval( $term_meta['price'][0] ) * $extra_qty * sizeof( $selected_days );
							} elseif ( ! $is_found ) {
								$total += floatval( $term_meta['price'][0] ) * $extra_qty;
							}
						}

					} else {
						if ( $term_meta['type'][0] == 'day' ) {
							$total += floatval( $term_meta['price'][0] ) * $extra_qty * $days;
						} else {
							$total += floatval( $term_meta['price'][0] ) * $extra_qty;
						}
					}
				}
			}
			if ( $per_day && $term_meta['type'][0] == 'day' ) {
				$total = $total / $days;
			}
		}

		return $total;
	}
}

if ( ! function_exists( 'ideapark_get_active_conditions' ) ) {
	function ideapark_get_active_conditions( $start, $end, $vehicle_id = 0, $is_catalog_query = false ) {
		global $wpdb;
		/**
		 * @var $start DateTime
		 * @var $end   DateTime
		 */

		$start = clone $start;
		$end   = clone $end;

		$diff             = abs( (int) $start->diff( $end )->format( '%a' ) ) + ( ideapark_mod( 'booking_type' ) == 'day' ? 1 : 0 );
		$terms            = get_terms( [
			'taxonomy'   => 'condition',
			'hide_empty' => false,
		] );
		$range            = [];
		$one_day_interval = new DateInterval( 'P1D' );
		$pickup           = ! empty( $_REQUEST['pick_up'] ) || ! empty( $_REQUEST['pickup'] ) ? apply_filters( 'wpml_object_id', absint( ! empty( $_REQUEST['pick_up'] ) ? $_REQUEST['pick_up'] : $_REQUEST['pickup'] ), 'location', true, apply_filters( 'wpml_default_language', null ) ) : 0;
		$type             = ! empty( $_REQUEST['type'] ) ? apply_filters( 'wpml_object_id', absint( $_REQUEST['type'] ), 'vehicle_type', true, apply_filters( 'wpml_default_language', null ) ) : 0;

		if ( ! $vehicle_id && is_singular( 'catalog' ) ) {
			global $post;
			$vehicle_id = $post->ID;
		}

		if ( $vehicle_id ) {
			if ( ! $type ) {
				if ( $_terms = get_the_terms( $vehicle_id, 'vehicle_type' ) ) {
					$type = $_terms[0]->term_id;
				}
			}
			if ( ! $pickup ) {
				if ( $_terms = get_the_terms( $vehicle_id, 'location' ) ) {
					$pickup = $_terms[0]->term_id;
				}
			}
		}

		if ( ! $type && is_tax() && ( $queried_object = get_queried_object() ) ) {
			$type = $queried_object->term_id;
		}

		$condition_fields    = array_diff( ideapark_get_condition_fields(), [ 'days_from' ] );
		$conditions_to_check = [];

		if ( $is_catalog_query ) {
			$used_ids = array_filter( $wpdb->get_col( "SELECT DISTINCT condition_id FROM {$wpdb->prefix}antek_price_cond" ) );
		}

		foreach ( $terms as $term ) {
			if ( $is_catalog_query && ! in_array( $term->term_id, $used_ids ) ) {
				continue;
			}
			$meta             = get_term_meta( $term->term_id );
			$cond_days_from   = ! empty( $meta['days_from'] ) ? $meta['days_from'][0] : 0;
			$cond_days_to     = ! empty( $meta['days_to'] ) ? $meta['days_to'][0] : false;
			$cond_days_first  = ! empty( $meta['days_first'] ) ? $meta['days_first'][0] : false;
			$cond_fixed_price = ! empty( $meta['fixed_price'] ) ? ! ! $meta['fixed_price'][0] : false;
			$cond_weekdays    = ! empty( $meta['weekdays'] ) && is_array( $meta['weekdays'] ) ? $meta['weekdays'] : [];
			$cond_days        = ! empty( $meta['days'] ) && is_array( $meta['days'] ) ? $meta['days'] : [];
			$cond_months      = ! empty( $meta['months'] ) && is_array( $meta['months'] ) ? $meta['months'] : [];
			$cond_years       = ! empty( $meta['years'] ) && is_array( $meta['years'] ) ? $meta['years'] : [];
			$cond_locations   = ! empty( $meta['location'] ) && is_array( $meta['location'] ) ? $meta['location'] : [];
			$cond_types       = ! empty( $meta['type'] ) && is_array( $meta['type'] ) ? $meta['type'] : [];
			$cond_from_date   = ! empty( $meta['from_date'] ) ? new DateTime( $meta['from_date'][0] ) : false;
			$cond_to_date     = ! empty( $meta['to_date'] ) ? new DateTime( $meta['to_date'][0] ) : false;

			if ( $cond_fixed_price ) {
				$fixed_price_ids = ideapark_mod( '_fixed_price_ids' );
				if ( ! is_array( $fixed_price_ids ) ) {
					$fixed_price_ids = [];
				}
				$fixed_price_ids[] = $term->term_id;
				ideapark_mod_set_temp( '_fixed_price_ids', $fixed_price_ids );
			}

			$current                 = clone $start;
			$range[ $term->term_id ] = [];
			$index                   = 1;
			do {
				$is_include = null;

				if ( $diff >= $cond_days_from ) {
					$is_include = true;

					if ( $cond_weekdays ) {
						if ( in_array( $current->format( 'w' ), $cond_weekdays ) ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}

					if ( $is_include !== false && $cond_days ) {
						if ( in_array( (int) $current->format( 'd' ), $cond_days ) ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}

					if ( $is_include !== false && $cond_months ) {
						if ( in_array( $current->format( 'm' ), $cond_months ) ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}

					if ( $is_include !== false && $cond_years ) {
						if ( in_array( $current->format( 'Y' ), $cond_years ) ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}

					if ( $is_include !== false && ! empty( $cond_locations[0] ) ) {
						if ( in_array( $pickup, explode( ',', $cond_locations[0] ) ) ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}

					if ( $is_include !== false && ! empty( $cond_types[0] ) ) {
						if ( in_array( $type, explode( ',', $cond_types[0] ) ) ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}

					if ( $is_include !== false && $cond_from_date ) {
						if ( $current >= $cond_from_date ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}

					if ( $is_include !== false && $cond_to_date ) {
						if ( $current <= $cond_to_date ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}

					if ( $is_include !== false && $cond_days_to ) {
						if ( $diff <= $cond_days_to ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}

					if ( $is_include !== false && $cond_days_first ) {
						$_diff = abs( (int) $start->diff( $current )->format( '%a' ) ) + 1;
						if ( $_diff <= $cond_days_first ) {
							$is_include = true;
						} else {
							$is_include = false;
						}
					}
				}

//				 add_filter( 'antek_condition_check', function ( $satisfies_condition, $term_id, $start, $end, $day ){
//					 /**
//					  * @var bool $satisfies_condition The $day satisfies the condition if true
//					  * @var int $term_id Condition ID
//					  * @var DateTime $start Start of the selected range
//					  * @var DateTime $end End of the selected range
//					  * @var DateTime $day The day from the selected range to check
//					  **/
//
//					// filter...
//
//					return $satisfies_condition;
//				}, 10, 5 );

				$is_include = apply_filters( 'antek_condition_check', $is_include, $term->term_id, clone $start, clone $end, clone $current );

				if ( $is_include === true ) {
					$range[ $term->term_id ][] = $index;
				}

				$current->add( $one_day_interval );
				$index ++;
			} while ( ideapark_mod( 'booking_type' ) == 'day' ? ( $current <= $end ) : ( $current < $end ) );

			if ( $diff > 1 && $cond_days_from > 1 && ! $cond_days_to && ! empty( $range[ $term->term_id ] ) ) {
				$meta_check = array_filter( $meta, function ( $key ) use ( $condition_fields ) {
					return in_array( $key, $condition_fields );
				}, ARRAY_FILTER_USE_KEY );
				if ( empty( $meta_check ) ) {
					$hash = 0;
				} else {
					ksort( $meta_check );
					$hash = md5( serialize( $meta_check ) );
				}
				$conditions_to_check[ $hash ][ $term->term_id ] = $cond_days_from;
			}
		}

		$range = array_filter( $range, function ( $item ) {
			return ! empty( $item );
		} );

		if ( $conditions_to_check ) {
			foreach ( $conditions_to_check as $hash => $item ) {
				if ( sizeof( $item ) > 1 ) {
					asort( $item );
					array_pop( $item );
					foreach ( $item as $_term_id => $_days ) {
						unset( $range[ $_term_id ] );
					}
				}
			}
		}

		return $range;
	}
}

if ( ! function_exists( 'ideapark_get_orig_catalog_id' ) ) {
	function ideapark_get_orig_catalog_id( $vehicle_id ) {
		global $sitepress;

		if ( isset( $sitepress ) ) {
			return $sitepress->get_object_id( $vehicle_id, 'any', true, $sitepress->get_default_language() );
		} else {
			return $vehicle_id;
		}
	}
}

if ( ! function_exists( 'ideapark_ajax_date_class' ) ) {
	function ideapark_ajax_date_class( $vehicle_id = false ) {
		global $wpdb;

		$return = ! ! $vehicle_id;
		ob_start();
		$classes             = [];
		$hints               = [];
		$is_admin            = false;
		$date                = false;
		$pickup_dropoff_days = explode( ',', ideapark_mod( 'pickup_dropoff_days' ) );
		$is_frontend         = ! empty( $_POST['frontend'] ) || $return;
		$holidays            = ideapark_get_holidays();

		if ( ! $vehicle_id && ! empty( $_POST['vehicle_id'] ) ) {
			$vehicle_id = absint( $_POST['vehicle_id'] );
		}

		if ( $vehicle_id ) {
			$vehicle_id = ideapark_get_orig_catalog_id( $vehicle_id );
		}

		if ( $return ) {
			$_date = new DateTime( date( 'Y-m-01' ) );
			$_date->sub( new DateInterval( 'P7D' ) );
			$date = $_date->format( 'Y-m-d' );
		} elseif ( ! empty( $_POST['date'] ) && preg_match( '~^\d{4}-\d{2}-\d{2}$~', $_POST['date'] ) ) {
			$date = $_POST['date'];
		}

		if (
			$vehicle_id && $date
		) {
			$is_admin = current_user_can( 'edit_post', $vehicle_id );

			if ( ! ideapark_mod( 'show_booked_days' ) && ! $is_admin ) {
				ob_end_clean();
				wp_send_json( [] );
			}

			$start         = new DateTime( $date );
			$start_request = clone $start;
			$start_text    = $start->format( 'Y-m-d' );

			$end = new DateTime( $date );
			$end->add( new DateInterval( 'P2M15D' ) );
			$end_request = clone $end;
			$end_text    = $end->format( 'Y-m-d' );

			$results = $wpdb->get_results( $wpdb->prepare( "
			SELECT date_start, date_end, order_id
			FROM {$wpdb->prefix}antek_order 
			WHERE vehicle_id=%d AND " . ideapark_mysql_dates_cond() . ( ! $is_admin && ideapark_mod( 'unlimited_booking' ) ? " AND order_id IS NULL " : "" ) . " ORDER BY date_start", $vehicle_id, $start_text, $start_text, $end_text, $end_text, $start_text, $end_text ) );

			if ( ! $is_admin && ideapark_mod( 'unlimited_booking' ) ) {
				$stock = get_post_meta( $vehicle_id, 'stock', true );
				$sql   = $wpdb->prepare( "
				SELECT day 
				FROM {$wpdb->prefix}antek_stock
				WHERE vehicle_id=%d AND cnt>=%d AND day>=%s AND day<=%s
				ORDER BY day
				", $vehicle_id, $stock, $start_text, $end_text );
				if ( $stock && ( $full_booked_days = $wpdb->get_col( $sql ) ) ) {
					foreach ( $full_booked_days as $full_booked_day ) {
						$row             = new stdClass();
						$row->date_start = $full_booked_day;
						$row->date_end   = ideapark_mod( 'booking_type' ) == 'day' ? $full_booked_day : ( new DateTime( $full_booked_day ) )->add( new DateInterval( 'P1D' ) )->format( 'Y-m-d' );
						$row->order_id   = null;
						$results[]       = $row;
					}
					usort( $results, function ( $a, $b ) {
						$a_date = new DateTime( $a->date_start );
						$b_date = new DateTime( $b->date_start );
						if ( $a_date == $b_date ) {
							return 0;
						}

						return ( $a_date < $b_date ) ? - 1 : 1;
					} );
				}
			}

			$current = $start;
			while ( $current <= $end ) {
				$date_text             = $current->format( 'Y-m-d' );
				$classes[ $date_text ] = '';
				$current->add( new DateInterval( 'P1D' ) );
			}

			$reserved_days = [];

			foreach ( $results as $result ) {
				$start = new DateTime( $result->date_start );
				$end   = new DateTime( $result->date_end );
				if ( ideapark_mod( 'cleaning_days' ) ) {
					$start->sub( new DateInterval( 'P' . ideapark_mod( 'cleaning_days' ) . 'D' ) );
				}
				$is_first = true;
				$is_last  = false;
				$current  = clone $start;
				while ( ! $is_last ) {
					if ( $current >= $end ) {
						$is_last = true;
					}

					$date_text = $current->format( 'Y-m-d' );

					if ( ! $result->order_id ) {
						$reserved_days[ $date_text ] = true;
					}

					if ( $is_admin ) {
						if ( ! empty( $classes[ $date_text ] ) ) {
							$classes[ $date_text ] = preg_replace( '~ date-\d+-\d+-\d+~', '', $classes[ $date_text ] );
						}
						if ( empty( $classes[ $date_text ] ) ) {

							if ( $result->order_id ) {
								$classes[ $date_text ] = $is_first ? 'first-order' : ( $is_last ? 'last-order' : 'full-order' );
							} else {
								$classes[ $date_text ] = $is_first ? 'first' : ( $is_last ? 'last' : 'full' );
							}

						} elseif ( $classes[ $date_text ] == ( $is_first ? 'last' : 'first' ) ) {
							if ( $result->order_id ) {
								$classes[ $date_text ] .= $is_first ? ' first-order' : ' last-order';
							} else {
								$classes[ $date_text ] = 'full';
							}
						} elseif ( $classes[ $date_text ] == ( $is_first ? 'last-order' : 'first-order' ) ) {
							if ( ! $result->order_id ) {
								$classes[ $date_text ] .= $is_first ? ' first' : ' last';
							} else {
								$classes[ $date_text ] = 'split-order';
							}
						} else {
							if ( $result->order_id ) {
								if ( $is_first && strpos( $classes[ $date_text ], 'first-order' ) !== false || $is_last && strpos( $classes[ $date_text ], 'last-order' ) !== false ) {
								} else {
									$classes[ $date_text ] = 'full-order';
								}
							} elseif ( strpos( $classes[ $date_text ], '-order' ) === false ) {
								$classes[ $date_text ] = 'full';
							}
						}
						$classes[ $date_text ] .= ' date-' . $date_text;
						if ( is_admin() ) {
							if ( empty( $hints[ $date_text ] ) ) {
								$hints[ $date_text ] = '';
							} else {
								if ( ! $result->order_id && $hints[ $date_text ] == __( 'Reserved', 'ideapark-antek' ) ) {
									$hints[ $date_text ] = '';
								} else {
									$hints[ $date_text ] .= ' / ';
								}
							}
							$hints[ $date_text ] .= ( $result->order_id ?
								'<a href="' . esc_url( get_edit_post_link( $result->order_id ) ) . '">' . __( 'Order #', 'ideapark-antek' ) . $result->order_id . '</a>'
								:
								__( 'Reserved', 'ideapark-antek' ) );
						}

					} else {
						if ( empty( $classes[ $date_text ] ) ) {
							$classes[ $date_text ] = $is_first ? 'first' : ( $is_last ? 'last' : 'full' );
						} elseif ( $classes[ $date_text ] == ( $is_first ? 'last' : 'first' ) ) {
							$classes[ $date_text ] = 'full';
						} elseif ( ! $is_first && ! $is_last ) {
							$classes[ $date_text ] = 'full';
						}
					}

					$current->add( new DateInterval( 'P1D' ) );
					$is_first = false;
				}
			}

			if ( ! empty( $hints ) && $is_admin && ideapark_mod( 'unlimited_booking' ) ) {
				foreach ( $hints as $date_text => $hint ) {
					if ( $hint ) {
						if ( $cnt = $wpdb->get_var( $wpdb->prepare( "SELECT cnt FROM {$wpdb->prefix}antek_stock WHERE vehicle_id=%d AND day=%s", $vehicle_id, $date_text ) ) ) {
							$hints[ $date_text ] = __( 'Booked:', 'ideapark-antek' ) . ' ' . $cnt . ' / ' . $hints[ $date_text ];
						}
					}
				}
			}

			if ( $is_frontend && ideapark_mod( 'unlimited_booking' ) && ideapark_mod( 'show_available' ) && ( $stock = get_post_meta( $vehicle_id, 'stock', true ) ) ) {
				$hints    = [];
				$min_date = new DateTime( ideapark_get_min_date() );
				$current  = $start_request < $min_date ? $min_date : clone $start_request;

				while ( $current <= $end_request ) {
					$date_text = $current->format( 'Y-m-d' );
					if ( empty( $reserved_days[ $date_text ] ) ) {
						$cnt                 = $wpdb->get_var( $wpdb->prepare( "SELECT cnt FROM {$wpdb->prefix}antek_stock WHERE vehicle_id=%d AND day=%s", $vehicle_id, $date_text ) );
						$hints[ $date_text ] = __( 'Available:', 'ideapark-antek' ) . ' ' . ( $stock - $cnt );
					}
					$current->add( new DateInterval( 'P1D' ) );
					if ( ! empty( $classes[ $date_text ] ) ) {
						$classes[ $date_text ] .= ' date-' . $date_text;
					} else {
						$classes[ $date_text ] = ' date-' . $date_text;
					}
				}
			}

			if ( ! $is_frontend && $is_admin && ideapark_mod( 'unlimited_booking' ) && ( $stock = get_post_meta( $vehicle_id, 'stock', true ) ) ) {
				$min_date = new DateTime( ideapark_get_min_date() );
				$current  = $start_request < $min_date ? $min_date : clone $start_request;
				while ( $current <= $end_request ) {
					$date_text = $current->format( 'Y-m-d' );
					if ( empty( $reserved_days[ $date_text ] ) ) {
						$cnt = $wpdb->get_var( $wpdb->prepare( "SELECT cnt FROM {$wpdb->prefix}antek_stock WHERE vehicle_id=%d AND day=%s", $vehicle_id, $date_text ) );
						$av  = (int) ceil( ( $stock - $cnt ) / $stock * 10 );
					} else {
						$av = null;
					}
					$current->add( new DateInterval( 'P1D' ) );
					if ( $av !== null ) {
						if ( ! empty( $classes[ $date_text ] ) ) {
							$classes[ $date_text ] .= ' av av-' . $av;
						} else {
							$classes[ $date_text ] = ' av av-' . $av;
						}
					}
				}
			}

			if ( sizeof( $pickup_dropoff_days ) < 7 || $holidays ) {
				$current = clone $start_request;
				while ( $current <= $end_request ) {
					$date_text = $current->format( 'Y-m-d' );
					$w         = $current->format( 'N' );
					if ( ! in_array( $w, $pickup_dropoff_days ) || in_array( $date_text, $holidays ) ) {
						$classes[ $date_text ] .= ' not-avail';
					}
					$current->add( new DateInterval( 'P1D' ) );
				}
			}

			if ( $is_admin && ideapark_mod( 'cleaning_days' ) ) {
				foreach ( $results as $result ) {
					$start = new DateTime( $result->date_start );
					$end   = new DateTime( $result->date_end );
					if ( ideapark_mod( 'cleaning_days' ) ) {
						$start->sub( new DateInterval( 'P' . ideapark_mod( 'cleaning_days' ) . 'D' ) );
					}
					$diff    = abs( (int) $start->diff( $end )->format( '%a' ) );
					$index   = 0;
					$current = clone $start;
					$is_last = false;
					while ( ! $is_last ) {
						if ( $current >= $end ) {
							$is_last = true;
						}
						$date_text = $current->format( 'Y-m-d' );
						if ( $index < ideapark_mod( 'cleaning_days' ) ) {
							$classes[ $date_text ] .= ' cleaning';
						} elseif ( $result->order_id && $diff - $index < ideapark_mod( 'cleaning_days' ) ) {
							$classes[ $date_text ] .= ' cleaning';
						}

						$current->add( new DateInterval( 'P1D' ) );
						$index ++;
					}
				}
			}
		}
		ob_end_clean();
		$result = [ 'dates' => $classes, 'hints' => $hints ];
		if ( $return ) {
			return $result;
		} else {
			wp_send_json( $result );
		}
	}

	add_action( 'wp_ajax_ideapark_ajax_date_class', 'ideapark_ajax_date_class' );
	add_action( 'wp_ajax_nopriv_ideapark_ajax_date_class', 'ideapark_ajax_date_class' );
}

if ( ! function_exists( 'ideapark_date_format' ) ) {
	function ideapark_date_format() {
		$date_format = 'Y-m-d';
		switch ( ideapark_mod( 'date_format' ) ) {
			case 'MM/DD/YYYY':
				$date_format = 'm/d/Y';
				break;
			case 'DD-MM-YYYY':
				$date_format = 'd-m-Y';
				break;
			case 'DD.MM.YYYY':
				$date_format = 'd.m.Y';
				break;
			case 'YYYY-MM-DD':
				$date_format = 'Y-m-d';
				break;
		}

		return $date_format;
	}
}

if ( ! function_exists( 'ideapark_ajax_date_action' ) ) {
	function ideapark_ajax_date_action() {
		global $wpdb;

		ob_start();
		$classes = [];
		$hints   = [];
		if (
			! empty( $_POST['vehicle_id'] ) && ( $vehicle_id = ideapark_get_orig_catalog_id( absint( $_POST['vehicle_id'] ) ) ) &&
			! empty( $_POST['start'] ) && ( $start = $_POST['start'] ) &&
			! empty( $_POST['end'] ) && ( $end = $_POST['end'] ) &&
			! empty( $_POST['button'] ) && ( $button = $_POST['button'] )
		) {

			$is_admin = current_user_can( 'edit_post', $vehicle_id );
			$is_error = false;

			try {
				$date_format = ideapark_date_format();

				$start = new DateTime( $start );
				$end   = new DateTime( $end );

				$start_text = $start->format( 'Y-m-d' );
				$end_text   = $end->format( 'Y-m-d' );

				$order_id         = ! empty( $_POST['order_id'] ) ? (int) $_POST['order_id'] : 0;
				$order_product_id = ! empty( $_POST['order_product_id'] ) ? (int) $_POST['order_product_id'] : 0;
				$start_orig       = ! empty( $_POST['start_orig'] ) && preg_match( '~^\d{4}-\d{2}-\d{2}$~', $_POST['start_orig'] ) ? $_POST['start_orig'] : '';

				if ( ( ideapark_mod( 'booking_type' ) == 'day' ? ( $start > $end ) : ( $start >= $end ) ) || ( $button == 'reserve' && ! ideapark_check_dates( $vehicle_id, $start, $end ) ) || ( $button == 'change' && ( ! $order_id || ! $start_orig || ! $order_product_id || ! ideapark_check_dates( $vehicle_id, $start, $end, $order_id, $start_orig ) ) ) ) {
					$is_error = true;
				} else {
					if ( $button == 'change' ) {

						if ( $range = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}antek_order WHERE order_id = %d AND vehicle_id = %d AND date_start = %s", $order_id, $vehicle_id, $start_orig ) ) ) {
							if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d AND meta_key = 'orig_dates'", $order_product_id ) ) ) {
								$orig_dates = $wpdb->get_var( $wpdb->prepare( "SELECT CONCAT(
								(SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d AND meta_key = 'start'),
								' — ',
								(SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d AND meta_key = 'end'),
								' (',
								(SELECT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d AND meta_key = 'days'),
								')'
								)", $order_product_id, $order_product_id, $order_product_id ) );
								$wpdb->query( $wpdb->prepare( "
								INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta (order_item_id,meta_key,meta_value) VALUES (%d,'orig_dates',%s) 
								", $order_product_id, $orig_dates ) );
							}

							$booking_start = new DateTime( $range->date_start );
							$booking_end   = new DateTime( $range->date_end );
							$diff          = abs( (int) $booking_start->diff( $booking_end )->format( '%a' ) );

							$end = clone $start;
							$end->add( new DateInterval( 'P' . $diff . 'D' ) );

							$sql = $wpdb->prepare( "UPDATE {$wpdb->prefix}antek_order SET date_start = %s, date_end = %s WHERE order_id = %d AND vehicle_id = %d AND date_start = %s", $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ), $order_id, $vehicle_id, $start_orig );
							$wpdb->query( $sql );

							if ( ideapark_mod( 'unlimited_booking' ) ) {
								$min_date = $booking_start < $start ? clone $booking_start : clone $start;
								$max_date = $booking_end > $end ? clone $booking_end : clone $end;
							}

							if ( ideapark_mod( 'cleaning_days' ) > 0 ) {
								$days = $diff - ideapark_mod( 'cleaning_days' );

								$end = clone $start;
								$end->add( new DateInterval( 'P' . $days . 'D' ) );
							}
							$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}woocommerce_order_itemmeta SET meta_value = %s WHERE order_item_id = %d AND meta_key = %s", $start->format( $date_format ), $order_product_id, 'start' ) );
							$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}woocommerce_order_itemmeta SET meta_value = %s WHERE order_item_id = %d AND meta_key = %s", $end->format( $date_format ), $order_product_id, 'end' ) );

							if ( ideapark_mod( 'unlimited_booking' ) ) {
								ideapark_recalculate_stock( $vehicle_id, $min_date, $max_date );
							}
						}

					} elseif ( $button == 'reserve' ) {
						$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}antek_order (vehicle_id, date_start, date_end) VALUES (%d, %s, %s)", $vehicle_id, $start_text, $end_text );
						$wpdb->query( $sql );
					} elseif ( $button == 'clear' ) {
						$results = $wpdb->get_results( $wpdb->prepare( "
						SELECT * 
						FROM {$wpdb->prefix}antek_order 
						WHERE vehicle_id=%d " . ( ideapark_mod( 'unlimited_booking' ) ? " AND order_id IS NULL " : "" ) . " AND " . ideapark_mysql_dates_cond(), $vehicle_id, $start_text, $start_text, $end_text, $end_text, $start_text, $end_text ) );

						foreach ( $results as $result ) {
							if ( $result->order_id ) {
								$is_error = true;
								break;
							}
						}

						if ( ! $is_error ) {
							foreach ( $results as $result ) {
								$start_result = new DateTime( $result->date_start );
								$end_result   = new DateTime( $result->date_end );
								if ( $start_result >= $start && $end_result <= $end ) {
									$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_order WHERE antek_order_id=%d", $result->antek_order_id ) );
								} elseif ( $start_result >= $start && $end_result > $end ) {
									$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}antek_order SET date_start = %s WHERE antek_order_id=%d", $end_text, $result->antek_order_id ) );
								} elseif ( $start_result < $start && $end_result <= $end ) {
									$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}antek_order SET date_end = %s WHERE antek_order_id=%d", $start_text, $result->antek_order_id ) );
								} else {
									$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}antek_order SET date_end = %s WHERE antek_order_id=%d", $start_text, $result->antek_order_id ) );
									$wpdb->insert( "{$wpdb->prefix}antek_order", [
										'vehicle_id' => $vehicle_id,
										'date_start' => $end_text,
										'date_end'   => $result->date_end,
									] );
								}
							}
						}
					}
				}
			} catch ( Exception $e ) {
				$is_error = true;
			}
		}
		ob_end_clean();
		if ( $is_error ) {
			wp_send_json( [ 'error' => esc_html__( 'Select available range', 'ideapark-antek' ) ] );
		} else {
			wp_send_json( [ 'success' => true ] );
		}
	}

	add_action( 'wp_ajax_ideapark_ajax_date_action', 'ideapark_ajax_date_action' );
}

if ( ! function_exists( 'ideapark_ajax_book' ) ) {
	function ideapark_ajax_book() {
		global $sitepress;
		ob_start();

		$quantity = isset( $_REQUEST['quantity'] ) ? abs( (int) $_REQUEST['quantity'] ) : 1;

		$vehicle_id_orig     = absint( $_REQUEST['vehicle_id'] );
		$is_vehicle_id_valid = ! empty( $_REQUEST['vehicle_id'] ) && ( $vehicle_id = ideapark_get_orig_catalog_id( $vehicle_id_orig ) ) && ( $vehicle = get_post( $vehicle_id_orig ) );

		if ( isset( $_REQUEST['lang'] ) ) {
			do_action( 'wpml_switch_language', $_REQUEST['lang'] );
		}

		if ( $dates = ideapark_get_filter_dates_range( true ) ) {
			$days           = $dates['diff'];
			$rental_price   = (float) ideapark_get_price( $vehicle_id, $dates['start'], $dates['end'] /*, $dates['delivery']*/ );
			$delivery_price = $dates['delivery'] ? (float) ideapark_get_delivery_price( $vehicle_id, $dates['location_id'] ) : 0;
			$is_wc_price    = function_exists( 'wc_price' );
			$pickup         = ! empty( $_REQUEST['pick_up'] ) ? absint( $_REQUEST['pick_up'] ) : 0;
		}

		if ( isset( $_REQUEST['lang'] ) ) {
			do_action( 'wpml_switch_language', $_REQUEST['lang'] );
		}

		if (
			$dates &&
			$is_wc_price &&
			$is_vehicle_id_valid &&
			ideapark_check_dates( $vehicle_id, $dates['start'], $dates['end'] ) &&
			$rental_price
		) {
			$total           = $rental_price + $delivery_price;
			$error           = false;
			$extra_options   = [];
			$extra_total     = 0;
			$is_only_booking = true;

			if ( ! empty( $_REQUEST['extra'] ) && is_array( $_REQUEST['extra'] ) ) {
				foreach ( $_REQUEST['extra'] as $extra_id => $extra_qty ) {
					if ( $extra_id && ( $term = get_term( abs( (int) $extra_id ) ) ) && $extra_qty ) {
						$term_total                 = ideapark_get_extra_total( $extra_id, $extra_qty );
						$total                      += $term_total;
						$extra_total                += $term_total;
						$extra_options[ $extra_id ] = $term->name . ' x ' . $extra_qty . ": " . wc_price( $term_total ) . "";
					}
				}
			}

			$has_filter = isset( $sitepress ) && remove_filter( 'get_term', [ $sitepress, 'get_term_adjust_id' ], 1 );
			if ( $locations = get_the_terms( $vehicle_id_orig, 'location' ) ) {
				$pick_up_term = false;
				foreach ( $locations as $location ) {
					if ( $location->term_id == $pickup ) {
						$pick_up_term = $location;
					}
				}
				if ( ! empty( $locations[0] ) && ! $pick_up_term ) {
					$error = __( 'Pick up location is not available', 'ideapark-antek' );
				}

				if ( ! $error ) {
					$location_drop = get_post_meta( $vehicle_id_orig, 'location_drop' );
					if ( ! empty( $location_drop[0] ) ) {
						$drop_off           = ! empty( $_REQUEST['drop_off'] ) ? absint( $_REQUEST['drop_off'] ) : 0;
						$drop_off_available = explode( ',', $location_drop[0] );
						$drop_off_term      = get_term( $drop_off, 'location' );
						if ( ! in_array( $drop_off, $drop_off_available ) || ! $drop_off_term ) {
							$error = __( 'Drop off location is not available', 'ideapark-antek' );
						}
					}
				}
			}
			if ( $has_filter ) {
				add_filter( 'get_term', [ $sitepress, 'get_term_adjust_id' ], 1, 1 );
			}

			if ( ! $error ) {
				$delivery     = $dates['delivery'] ? ( ( $delivery_price > 0 ) ? wc_price( $delivery_price ) : ideapark_mod( 'delivery_free_value' ) ) : ( ideapark_mod( 'disable_self_pickup' ) ? null : '-' );
				$delivery_alg = $dates['delivery'] && $delivery_price ? get_post_meta( $vehicle_id_orig, 'price_delivery_alg', true ) : '';
				if ( is_array( $delivery_alg ) ) {
					$delivery_alg = $delivery_alg[0];
				}

				$cart_item_data = [
					'ideapark_antek' => [
						'vehicle_id'     => $vehicle_id,
						'name'           => $vehicle->post_title,
						'start'          => (string) $_REQUEST['start'],
						'end'            => (string) $_REQUEST['end'],
						'days'           => $days,
						'pick_up'        => ! empty( $pick_up_term ) ? $pick_up_term->name : '',
						'drop_off'       => ! empty( $drop_off_term ) ? $drop_off_term->name : '',
						'price'          => $total,
						'total'          => $total,
						'delivery'       => $delivery,
						'delivery_price' => $delivery_price,
						'delivery_alg'   => $delivery_alg,
						'rental_price'   => $rental_price,
						'pick_up_id'     => $delivery === '-' && ! empty( $pick_up_term ) ? $pick_up_term->term_id : 0,
						'drop_off_id'    => $delivery === '-' && ! empty( $drop_off_term ) ? $drop_off_term->term_id : 0,
						'extra'          => serialize( $extra_options ),
						'extra_total'    => $extra_total,
						'time_booking'   => time(),
					]
				];

				if ( ideapark_mod( 'pickup_dropoff_time' ) ) {
					$cart_item_data['ideapark_antek']['start_time'] = ( ! empty( $dates['start_time'] ) ? ' ' . $dates['start_time'] : '' );
					$cart_item_data['ideapark_antek']['end_time']   = ( ! empty( $dates['end_time'] ) ? ' ' . $dates['end_time'] : '' );
				}

				if ( $product_id = ideapark_get_default_product( $vehicle_id ) ) {
					if ( ! ideapark_mod( 'multiple_booking' ) ) {
						$product_ids = [ $product_id ];
						if ( isset( $sitepress ) && ( $languages = apply_filters( 'wpml_active_languages', [] ) ) ) {
							foreach ( $languages as $lang_code => $lang ) {
								$product_ids[] = $sitepress->get_object_id( $product_id, 'any', true, $lang_code );
							}
						}
						$product_ids = array_unique( $product_ids );
						if ( ! WC()->cart->is_empty() ) {
							foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
								if ( in_array( $cart_item['product_id'], $product_ids ) ) {
									WC()->cart->remove_cart_item( $cart_item_key );
								}
							}
						}
					}
					if ( ! WC()->cart->add_to_cart( $product_id, $quantity, 0, [], $cart_item_data ) ) {
						$error = __( 'Booking is not available now', 'ideapark-antek' );
					}
				} else {
					$error = __( 'Booking is not available now', 'ideapark-antek' );
				}

				if ( ! ideapark_mod( 'multiple_booking' ) && WC()->cart->get_cart_contents_count() && $product_id ) {
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						if ( ! in_array( $cart_item['product_id'], $product_ids ) ) {
							$is_only_booking = false;
							break;
						}
					}
				} else {
					$is_only_booking = false;
				}
			}

			ob_end_clean();

			if ( $error ) {
				wp_send_json( [ 'error' => esc_html( $error ) ] );
			} elseif ( ideapark_mod( 'disable_redirect_after_booking' ) ) {
				$message = sprintf( '<a href="%s" tabindex="1" class="button wc-forward">%s</a> %s', esc_url( wc_get_cart_url() ), esc_html__( 'View cart', 'woocommerce' ), esc_html( sprintf( __( '%s has been added to your cart.', 'woocommerce' ), "&ldquo;{$vehicle->post_title}&rdquo;" ) ) );
				wp_send_json( [ 'message' => $message ] );
			} else {
				wp_send_json( [ 'redirect' => ! $is_only_booking ? wc_get_cart_url() : wc_get_checkout_url() ] );
			}
		} else {
			wp_send_json( [ 'error' => esc_html__( 'These days are not available for booking', 'ideapark-antek' ) ] );
		}
		ob_end_clean();
	}

	add_action( 'wp_ajax_ideapark_book', 'ideapark_ajax_book' );
	add_action( 'wp_ajax_nopriv_ideapark_book', 'ideapark_ajax_book' );
}

if ( ! function_exists( 'ideapark_get_product_vehicle_id' ) ) {
	function ideapark_get_product_vehicle_id( $product_id ) {
		global $wpdb;

		return $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_linked_product_id' AND meta_value=%d", apply_filters( 'wpml_object_id', (int) $product_id, 'product', true, apply_filters( 'wpml_default_language', null ) ) ) );
	}
}

if ( ! function_exists( 'ideapark_get_all_default_products' ) ) {
	function ideapark_get_all_default_products() {
		static $product_ids;
		global $wpdb;

		if ( $product_ids !== null ) {
			return $product_ids;
		}

		$product_ids = [];
		if ( class_exists( 'WC_Product' ) ) {
			if ( $product_id = get_option( 'ideapark_product_default', false ) ) {
				$product_ids[] = (int) $product_id;
			}
			if ( $rows = $wpdb->get_col( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key='_linked_product_id' AND meta_value!=''" ) ) {
				foreach ( $rows as $product_id ) {
					$product_ids[] = (int) $product_id;
				}
			}
			if ( $languages = apply_filters( 'wpml_active_languages', [] ) ) {
				foreach ( $product_ids as $product_id ) {
					foreach ( $languages as $lang_code => $lang ) {
						$product_ids[] = apply_filters( 'wpml_object_id', $product_id, 'product', true, $lang_code );
					}
				}
			}

			$product_ids = array_unique( $product_ids );
		}

		return $product_ids;
	}
}

if ( ! function_exists( 'ideapark_get_default_product' ) ) {
	function ideapark_get_default_product( $vehicle_id = 0 ) {
		if ( class_exists( 'WC_Product' ) ) {
			if ( ideapark_mod( 'wc_integration' ) == 'each' && ( $vehicle_id = ideapark_get_orig_catalog_id( $vehicle_id ) ) ) {
				static $product_ids = [];
				if ( isset( $product_ids[ $vehicle_id ] ) && ! empty( $product_ids[ $vehicle_id ] ) ) {
					return $product_ids[ $vehicle_id ];
				}
				$product_id      = get_post_meta( $vehicle_id, '_linked_product_id', true );
				$default_product = $product_id ? get_post( $product_id ) : false;
				if ( $default_product && isset( $default_product->post_type ) && $default_product->post_type == 'product' && $default_product->post_status == 'publish' ) {
				} else {
					$product_id = ideapark_create_linked_product( $vehicle_id );
					update_post_meta( $vehicle_id, '_linked_product_id', $product_id );
				}

				return (int) $product_id;
			} else {
				static $product_id;
				if ( isset( $product_id ) && ! empty( $product_id ) ) {
					return $product_id;
				}

				$product_id      = get_option( 'ideapark_product_default', false );
				$default_product = $product_id ? get_post( $product_id ) : false;
				if ( $default_product && isset( $default_product->post_type ) && $default_product->post_type == 'product' && $default_product->post_status == 'publish' ) {
				} else {
					$product_id = ideapark_create_linked_product();
					update_option( 'ideapark_product_default', $product_id );
				}

				return (int) $product_id;
			}
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'ideapark_creat_linked_product' ) ) {
	function ideapark_create_linked_product( $vehicle_id = 0 ) {
		if ( class_exists( 'WC_Product' ) ) {
			$category_id = get_option( 'ideapark_category_default', false );

			if ( ! ( $category_id && ( $term = get_term( $category_id, 'product_cat' ) ) && ! is_wp_error( $term ) ) ) {
				$ret = wp_insert_term(
					__( 'Rentals', 'ideapark-antek' ),
					'product_cat',
					[
						'parent' => 0,
					]
				);
				if ( ! is_wp_error( $ret ) ) {
					update_option( 'ideapark_category_default', $category_id = $ret['term_id'] );
				} else {
					return false;
				}
			}

			if ( $vehicle_id ) {
				$name = get_the_title( ideapark_get_orig_catalog_id( $vehicle_id ) );
			} else {
				$name = __( 'Booking', 'ideapark-antek' );
			}

			$objProduct = new WC_Product();
			$objProduct->set_name( $name );
			$objProduct->set_description( __( 'Product for making a reservation', 'ideapark-antek' ) );
			$objProduct->set_category_ids( [ $category_id ] );
			$objProduct->set_status( "publish" );
			$objProduct->set_catalog_visibility( 'hidden' );
			$objProduct->set_price( 1 );
			$objProduct->set_regular_price( 1 );
			$objProduct->set_manage_stock( false );
			$objProduct->set_stock_quantity( '' );
			$objProduct->set_stock_status( 'instock' );
			$objProduct->set_backorders( 'no' );
			$objProduct->set_reviews_allowed( false );
			$objProduct->set_sold_individually( false );
			$objProduct->set_virtual( true );
			$product_id = $objProduct->save();

			return $product_id;
		}
	}
}

if ( ! function_exists( 'ideapark_wc_integration_changed' ) ) {
	function ideapark_wc_integration_changed( $setting ) {
		/**
		 * @var WP_Customize_Setting $setting
		 */

		ideapark_mod_set_temp( 'wc_integration', $setting->post_value() );

		if ( ideapark_mod( 'wc_integration' ) == 'each' ) {
			$args = [
				'numberposts'      => - 1,
				'post_type'        => 'catalog',
				'suppress_filters' => true,
			];

			$posts = get_posts( $args );

			foreach ( $posts as $post ) {
				ideapark_get_default_product( $post->ID );
			}
		} else {
			ideapark_get_default_product();
		}
	}

	add_action( "customize_save_wc_integration", 'ideapark_wc_integration_changed' );
}

if ( ! function_exists( 'ideapark_vehicle_deletion' ) ) {
	function ideapark_vehicle_deletion( $post_ID ) {
		$type = get_post_type( $post_ID );
		if ( $type == 'catalog' ) {
			if ( $product_id = ideapark_get_default_product( $post_ID ) ) {
				wp_delete_post( $product_id, true );
			}
		}
	}

	add_action( 'wp_trash_post', 'ideapark_vehicle_deletion', 10, 1 );
	add_action( 'before_delete_post', 'ideapark_vehicle_deletion', 10, 1 );
}

if ( ! function_exists( 'ideapark_calc_cart_item_price' ) ) {
	function ideapark_calc_cart_item_price( &$cart_item ) {
		if ( isset( $cart_item['ideapark_antek']['delivery_alg'] ) && $cart_item['ideapark_antek']['delivery_alg'] == 'type-2' && ! empty( $cart_item['ideapark_antek']['delivery_price'] ) && $cart_item['quantity'] ) {
			return $cart_item['ideapark_antek']['rental_price'] + $cart_item['ideapark_antek']['delivery_price'] / $cart_item['quantity'];
		} else {
			return $cart_item['ideapark_antek']['total'];
		}
	}
}

if ( ! function_exists( 'ideapark_wc_custom_price' ) ) {
	function ideapark_wc_custom_price() {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		$product_ids = ideapark_get_all_default_products();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			if ( in_array( $cart_item['data']->get_id(), $product_ids ) && ! empty( $cart_item['ideapark_antek'] ) ) {
				$cart_item['data']->set_price( ideapark_calc_cart_item_price( $cart_item ) );
			}
		}
	}

	add_action( 'woocommerce_before_calculate_totals', 'ideapark_wc_custom_price' );
}

if ( ! function_exists( 'ideapark_wc_cart_item_price' ) ) {
	function ideapark_wc_cart_item_price( $price, $cart_item, $cart_item_key ) {
		if ( ! empty( $cart_item['ideapark_antek'] ) ) {
			if ( isset( $cart_item['ideapark_antek']['delivery_alg'] ) && $cart_item['ideapark_antek']['delivery_alg'] == 'type-2' ) {
				$price = '';
			} else {
				$price = wc_price( ideapark_calc_cart_item_price( $cart_item ) );
			}
		}

		return $price;
	}

	add_filter( 'woocommerce_cart_item_price', 'ideapark_wc_cart_item_price', 99, 3 );
}

if ( ! function_exists( 'ideapark_wc_cart_item_subtotal' ) ) {
	function ideapark_wc_cart_item_subtotal( $product_subtotal, $cart_item, $cart_item_key ) {
		if ( ! empty( $cart_item['ideapark_antek'] ) ) {
			/**
			 * @var $product WC_Product
			 */
			$product  = $cart_item['data'];
			$quantity = $cart_item['quantity'];
			$price    = ideapark_calc_cart_item_price( $cart_item );

			if ( $product->is_taxable() ) {

				if ( WC()->cart->display_prices_including_tax() ) {
					$row_price        = wc_get_price_including_tax( $product, [
						'price' => $price,
						'qty'   => $quantity
					] );
					$product_subtotal = wc_price( $row_price );

					if ( ! wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
						$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
					}
				} else {
					$row_price        = wc_get_price_excluding_tax( $product, [
						'price' => $price,
						'qty'   => $quantity
					] );
					$product_subtotal = wc_price( $row_price );

					if ( wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
						$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
					}
				}
			} else {
				$product_subtotal = wc_price( $price * $quantity );
			}
		}

		return $product_subtotal;
	}

	add_filter( 'woocommerce_cart_item_subtotal', 'ideapark_wc_cart_item_subtotal', 99, 3 );
}

if ( ! function_exists( 'ideapark_cart_item_permalink' ) ) {
	function ideapark_cart_item_permalink( $link, $cart_item, $cart_item_key ) {
		if ( ! empty( $cart_item['ideapark_antek'] ) ) {
			$link = get_permalink( $cart_item['ideapark_antek']['vehicle_id'] );
		}

		return $link;
	}

	add_filter( 'woocommerce_cart_item_permalink', 'ideapark_cart_item_permalink', 10, 3 );
}


if ( ! function_exists( 'ideapark_request_from_phrases' ) ) {
	function ideapark_request_from_phrases() {
		return [
			'name'       => __( 'Name', 'ideapark-antek' ),
			'start'      => __( 'Start date', 'ideapark-antek' ),
			'end'        => __( 'Finish date', 'ideapark-antek' ),
			'start_time' => __( 'Pick-up time', 'ideapark-antek' ),
			'end_time'   => __( 'Drop-off time', 'ideapark-antek' ),
			'days'       => ideapark_mod( 'booking_type' ) == 'day' ? __( 'Days', 'ideapark-antek' ) : __( 'Nights', 'ideapark-antek' ),
			'where'      => __( 'Where', 'ideapark-antek' ),
			'pickup'     => __( 'Pick up', 'ideapark-antek' ),
			'dropoff'    => __( 'Drop off', 'ideapark-antek' ),
			'extra'      => __( 'Extra options', 'ideapark-antek' ),
			'orig_dates' => __( 'Original dates', 'ideapark-antek' ),
			'quantity'   => __( 'Quantity', 'ideapark-antek' ),
		];
	}
}

if ( ! function_exists( 'ideapark_woocommerce_after_order_itemmeta' ) ) {
	function ideapark_woocommerce_after_order_itemmeta( $item_id, $item, $product ) {
		global $wpdb;
		$default_product_ids = ideapark_get_all_default_products();
		/**
		 * @var $product WC_Product
		 * @var $item    WC_Order_Item_Product
		 */

		if ( $product && in_array( $product->get_id(), $default_product_ids ) ) {
			$start            = '';
			$end              = '';
			$vehicle_id       = 0;
			$order_id         = $item->get_order_id();
			$order_product_id = $item->get_id();
			foreach ( $meta_data = $item->get_meta_data() as $data ) {
				switch ( $data->key ) {
					case 'vehicle_id':
						$vehicle_id = (int) $data->value;
						break;
				}
			}
			if ( $range = $wpdb->get_row( $wpdb->prepare( "
			SELECT date_start, date_end 
			FROM {$wpdb->prefix}antek_order 
			WHERE vehicle_id=%d AND order_id=%d", $vehicle_id, $order_id ) ) ) {
				$start = new DateTime( $range->date_start );
				$end   = new DateTime( $range->date_end );
				$diff  = abs( (int) $start->diff( $end )->format( '%a' ) ) + ( ideapark_mod( 'booking_type' ) == 'day' ? 1 : 0 );
			}
			?>
			<?php if ( $vehicle_id && $start && $end ) { ?>
				<div class="ideapark-edit-dates">
					<button
						type="button"
						class="button js-ideapark-edit-dates"
						data-start="<?php echo esc_attr( $start->format( 'Y-m-d' ) ); ?>"
						data-end="<?php echo esc_attr( $end->format( 'Y-m-d' ) ) ?>"
						data-diff="<?php echo esc_attr( $diff ) ?>"
						data-order_id="<?php echo esc_attr( $order_id ); ?>"
						data-order_product_id="<?php echo esc_attr( $order_product_id ); ?>"
						data-vehicle_id="<?php echo esc_attr( $vehicle_id ); ?>"><?php esc_html_e( 'Change start date', 'ideapark-antek' ) ?></button>
				</div>
			<?php } ?>
		<?php }
	}

	add_action( 'woocommerce_after_order_itemmeta', 'ideapark_woocommerce_after_order_itemmeta', 10, 3 );
}

if ( ! function_exists( 'ideapark_wc_cart_add_item_meta' ) ) {
	function ideapark_wc_cart_add_item_meta( $item_data, $cart_item ) {
		if ( ! empty( $cart_item['ideapark_antek'] ) ) {

			if ( ideapark_mod( 'wc_integration' ) !== 'each' ) {
				$item_data[] = [
					'key'   => '<a href="' . esc_url( get_permalink( $cart_item['ideapark_antek']['vehicle_id'] ) ) . '" class="booking-attr-cart">' . esc_html( $cart_item['ideapark_antek']['name'] ) . '</a>',
					'value' => '',
				];
			}

			$item_data[] = [
				'key'   => __( 'Start date', 'ideapark-antek' ),
				'value' => $cart_item['ideapark_antek']['start'] . ( ! empty( $cart_item['ideapark_antek']['start_time'] ) ? ' &mdash; ' . ideapark_time_format( $cart_item['ideapark_antek']['start_time'] ) : '' ),
			];

			$item_data[] = [
				'key'   => __( 'Finish date', 'ideapark-antek' ),
				'value' => $cart_item['ideapark_antek']['end'] . ( ! empty( $cart_item['ideapark_antek']['end_time'] ) ? ' &mdash; ' . ideapark_time_format( $cart_item['ideapark_antek']['end_time'] ) : '' ),
			];

			$item_data[] = [
				'key'   => ideapark_mod( 'booking_type' ) == 'day' ? __( 'Days', 'ideapark-antek' ) : __( 'Nights', 'ideapark-antek' ),
				'value' => $cart_item['ideapark_antek']['days'],
			];

			if ( $cart_item['ideapark_antek']['pick_up'] ) {
				if ( $cart_item['ideapark_antek']['pick_up'] == $cart_item['ideapark_antek']['drop_off'] || empty( $cart_item['ideapark_antek']['drop_off'] ) ) {

					$item_data[] = [
						'key'   => __( 'Where', 'ideapark-antek' ),
						'value' => $cart_item['ideapark_antek']['pick_up'],
					];

				} else {

					$item_data[] = [
						'key'   => __( 'Pick up', 'ideapark-antek' ),
						'value' => $cart_item['ideapark_antek']['pick_up'],
					];

					$item_data[] = [
						'key'   => __( 'Drop off', 'ideapark-antek' ),
						'value' => $cart_item['ideapark_antek']['drop_off'],
					];
				}
			}

			if ( ! empty( $cart_item['ideapark_antek']['rental_price'] ) ) {
				$item_data[] = [
					'key'   => __( 'Rent', 'ideapark-antek' ),
					'value' => wc_price( $cart_item['ideapark_antek']['rental_price'] ),
				];
			}

			if ( ! empty( $cart_item['ideapark_antek']['delivery'] ) ) {
				if ( $cart_item['ideapark_antek']['delivery'] == '-' ) {
					$item_data[] = [
						'key'   => ideapark_mod( 'self_pickup_title_item' ),
						'value' => __( 'Yes', 'ideapark-antek' ),
					];
				} else {
					$item_data[] = [
						'key'   => ideapark_mod( 'delivery_title_item' ),
						'value' => $cart_item['ideapark_antek']['delivery'],
					];
				}
			}

			if ( ! empty( $cart_item['ideapark_antek']['extra'] ) ) {
				if ( ! is_array( $cart_item['ideapark_antek']['extra'] ) ) {
					$cart_item['ideapark_antek']['extra'] = @unserialize( $cart_item['ideapark_antek']['extra'] );
					if ( ! is_array( $cart_item['ideapark_antek']['extra'] ) ) {
						$cart_item['ideapark_antek']['extra'] = [];
					}
				}
				if ( ! empty( $cart_item['ideapark_antek']['extra'] ) ) {
					$item_data[] = [
						'key'   => __( 'Extra options', 'ideapark-antek' ),
						'value' => '<ul class="c-meta-options"><li>' . implode( '</li><li>', $cart_item['ideapark_antek']['extra'] ) . '</li></ul>',
					];
				}
			}
		}

		return $item_data;
	}

	add_filter( 'woocommerce_get_item_data', 'ideapark_wc_cart_add_item_meta', 10, 2 );
}

if ( ! function_exists( 'ideapark_wc_cart_item_thumbnail' ) ) {
	function ideapark_wc_cart_item_thumbnail( $image, $cart_item, $cart_item_key ) {
		if ( ! empty( $cart_item['ideapark_antek'] ) ) {
			$auto_post = get_post( $cart_item['ideapark_antek']['vehicle_id'] );
			$thumb     = get_the_post_thumbnail( $auto_post->ID, 'thumbnail' );
			if ( ! empty( $thumb ) ) {
				return $thumb;
			}
		}

		return $image;
	}

	add_filter( 'woocommerce_cart_item_thumbnail', 'ideapark_wc_cart_item_thumbnail', 10, 3 );
}

if ( ! function_exists( 'ideapark_wc_cart_item_quantity' ) ) {
	function ideapark_wc_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		if ( ! empty( $cart_item['ideapark_antek'] ) ) {
			return ideapark_mod( 'unlimited_booking' ) ? $product_quantity : str_replace( '<input', '<input disabled', $product_quantity );
		}

		return $product_quantity;
	}

	add_filter( 'woocommerce_cart_item_quantity', 'ideapark_wc_cart_item_quantity', 10, 3 );
}

if ( ! function_exists( 'ideapark_wc_admin_order_item_thumbnail' ) ) {
	function ideapark_wc_admin_order_item_thumbnail( $image, $cart_item, $cart_item_key ) {
		global $post;
		$auto_id = $cart_item_key->get_meta( 'vehicle_id', true );
		if ( ! empty( $auto_id ) ) {
			$thumb = get_the_post_thumbnail( $auto_id, 'thumbnail' );
			if ( ! empty( $thumb ) ) {
				return $thumb;
			}
		}

		return $image;
	}

	add_filter( 'woocommerce_admin_order_item_thumbnail', 'ideapark_wc_admin_order_item_thumbnail', 10, 3 );
}

if ( ! function_exists( 'ideapark_wc_order_line' ) ) {
	function ideapark_wc_order_line( $item, $cart_item_key, $values, $order ) {
		if ( ! empty( $values['ideapark_antek'] ) ) {
			foreach ( $values['ideapark_antek'] as $key => $value ) {
				$item->add_meta_data( $key, $value );
			}
		}
	}

	add_action( 'woocommerce_checkout_create_order_line_item', 'ideapark_wc_order_line', 10, 4 );
}

if ( ! function_exists( 'ideapark_wc_formatted_meta_data' ) ) {
	function ideapark_wc_formatted_meta_data( $formatted_meta, $object ) {
		$meta_data = $object->get_meta_data();
		$is_where  = false;
		$pick_up   = '';
		$drop_off  = '';
		$name      = '';
		foreach ( $formatted_meta as $index => $data ) {
			if ( $data->key == 'pick_up' ) {
				$pick_up = $data->value;
			}
			if ( $data->key == 'drop_off' ) {
				$drop_off = $data->value;
			}
			if ( $data->key == 'name' ) {
				$name = $data->value;
			}
		}
		if ( $pick_up && $pick_up == $drop_off || ! $drop_off ) {
			$is_where = true;
		}
		foreach ( $formatted_meta as $index => $data ) {
			switch ( $formatted_meta[ $index ]->key ) {
				case 'name':
				case 'price':
				case 'total':
				case 'extra':
				case 'extra_total':
				case 'time_booking':
				case 'delivery_price':
				case 'delivery_alg':
				case 'rental_price':
					unset( $formatted_meta[ $index ] );
					break;
				case 'vehicle_id':
					$formatted_meta[ $index ]->display_value = '<a href="' . esc_url( is_admin() ? get_edit_post_link( $formatted_meta[ $index ]->value ) : get_permalink( $formatted_meta[ $index ]->value ) ) . '" class="booking-attr-cart">' . esc_html( $name ) . '</a>';
					$formatted_meta[ $index ]->display_key   = __( 'Name', 'ideapark-antek' );
					break;
				case 'start':
					$formatted_meta[ $index ]->display_key = __( 'Start date', 'ideapark-antek' );
					break;
				case 'end':
					$formatted_meta[ $index ]->display_key = __( 'Finish date', 'ideapark-antek' );
					break;
				case 'start_time':
					$formatted_meta[ $index ]->display_key   = __( 'Pick-up time', 'ideapark-antek' );
					$formatted_meta[ $index ]->display_value = ideapark_time_format( $formatted_meta[ $index ]->value );
					break;
				case 'end_time':
					$formatted_meta[ $index ]->display_key   = __( 'Drop-off time', 'ideapark-antek' );
					$formatted_meta[ $index ]->display_value = ideapark_time_format( $formatted_meta[ $index ]->value );
					break;
				case 'orig_dates':
					$formatted_meta[ $index ]->display_key = __( 'Original dates', 'ideapark-antek' );
					break;
				case 'days':
					$formatted_meta[ $index ]->display_key = ideapark_mod( 'booking_type' ) == 'day' ? __( 'Days', 'ideapark-antek' ) : __( 'Nights', 'ideapark-antek' );
					break;
				case 'pick_up':
					$formatted_meta[ $index ]->display_key = $is_where ? __( 'Where', 'ideapark-antek' ) : __( 'Pick up', 'ideapark-antek' );
					break;
				case 'pick_up_id':
					if ( $formatted_meta[ $index ]->value && ( $address = get_term_meta( (int) $formatted_meta[ $index ]->value, 'address', true ) ) ) {
						$formatted_meta[ $index ]->display_key   = __( 'Pick-up address', 'ideapark-antek' );
						$formatted_meta[ $index ]->display_value = preg_match( '~[\r\n]~', trim( $address ) ) ? nl2br( "\n" . esc_html( trim( $address ) ) ) : esc_html( $address );
					} else {
						unset( $formatted_meta[ $index ] );
					}
					break;
				case 'drop_off_id':
					if ( $formatted_meta[ $index ]->value && ( $address = get_term_meta( (int) $formatted_meta[ $index ]->value, 'address', true ) ) ) {
						$formatted_meta[ $index ]->display_key   = __( 'Drop-off address', 'ideapark-antek' );
						$formatted_meta[ $index ]->display_value = preg_match( '~[\r\n]~', trim( $address ) ) ? nl2br( "\n" . esc_html( trim( $address ) ) ) : esc_html( $address );
					} else {
						unset( $formatted_meta[ $index ] );
					}
					break;
				case 'delivery':
					if ( $formatted_meta[ $index ]->value === '-' ) {
						$formatted_meta[ $index ]->display_key   = ideapark_mod( 'self_pickup_title_item' );
						$formatted_meta[ $index ]->display_value = __( 'Yes', 'ideapark-antek' );
					} else {
						$formatted_meta[ $index ]->display_key = ideapark_mod( 'delivery_title_item' );
					}
					break;
				case 'drop_off':
					if ( ! $is_where ) {
						$formatted_meta[ $index ]->display_key = __( 'Drop off', 'ideapark-antek' );
					} else {
						unset( $formatted_meta[ $index ] );
					}
					break;
			}
		}

		foreach ( $meta_data as $meta ) {
			if ( $meta->key == 'extra' && ! empty( $meta->value ) ) {
				if ( ! is_array( $meta->value ) ) {
					$meta->value = @unserialize( $meta->value );
					if ( ! is_array( $meta->value ) ) {
						$meta->value = [];
					}
				}
				if ( ! empty( $meta->value ) ) {
					$object                = new stdClass();
					$object->key           = 'extra';
					$object->value         = serialize( $meta->value );
					$object->display_key   = __( 'Extra options', 'ideapark-antek' );
					$object->display_value = '<ul class="c-meta-options"><li>' . implode( '</li><li>', $meta->value ) . '</li></ul>';
					$formatted_meta[]      = $object;
				}
			}
		}

		return $formatted_meta;
	}

	add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'ideapark_wc_formatted_meta_data', 10, 2 );
}

if ( ! function_exists( 'ideapark_check_cart_availability' ) ) {
	function ideapark_check_cart_availability( $checked_vehicle = [] ) {
		global $wpdb;
		$errors = [];

		$product_ids              = ideapark_get_all_default_products();
		$bookings                 = [];
		$day_cnt                  = [];
		$vehicle_stock            = [];
		$min_date                 = null;
		$max_date                 = null;
		$is_someone_not_available = false;

		$checked_vehicles = [];

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( in_array( $cart_item['data']->get_id(), $product_ids ) && ! empty( $cart_item['ideapark_antek'] ) ) {
				$cart_item['ideapark_antek']['quantity'] = $cart_item['quantity'];
				$checked_vehicles[]                      = $cart_item['ideapark_antek'];
			}
		}

		if ( $checked_vehicle ) {
			$checked_vehicles[] = $checked_vehicle;
		}

		foreach ( $checked_vehicles as $item ) {
			$vehicle_id = $item['vehicle_id'];
			$vehicle    = get_post( $vehicle_id );
			$start      = strtotime( $item['start'] );
			$end        = strtotime( $item['end'] );
			$start_text = date( 'Y-m-d', $start );
			$quantity   = $item['quantity'];

			$is_not_available = false;
			if ( ! ideapark_mod( 'unlimited_booking' ) ) {
				foreach ( $bookings as $booking ) {
					if ( $booking['vehicle_id'] == $vehicle_id && ( $start > $booking['start'] and $start < $booking['end'] or $end > $booking['start'] and $end < $booking['end'] or $start <= $booking['start'] and $end >= $booking['end'] ) ) {
						$is_not_available = true;
						break;
					}
				}
			} else {
				if ( $stock = get_post_meta( $vehicle_id, 'stock', true ) ) {
					if ( ! array_key_exists( $vehicle_id, $vehicle_stock ) ) {
						$vehicle_stock[ $vehicle_id ] = $stock;
					}
					$start_obj = new DateTime( $start_text );
					$end_obj   = new DateTime( date( 'Y-m-d', $end + (int) ideapark_mod( 'cleaning_days' ) * 24 * 60 * 60 ) );
					if ( ! $min_date || $min_date > $start_obj ) {
						$min_date = clone $start_obj;
					}
					if ( ! $max_date || $max_date < $end_obj ) {
						$max_date = clone $end_obj;
					}
					$current          = clone $start_obj;
					$one_day_interval = new DateInterval( 'P1D' );
					do {
						$day = $current->format( 'Y-m-d' );
						if ( ! empty( $day_cnt[ $vehicle_id ] ) && array_key_exists( $day, $day_cnt[ $vehicle_id ] ) ) {
							$day_cnt[ $vehicle_id ][ $day ] += $quantity;
						} else {
							$day_cnt[ $vehicle_id ][ $day ] = $quantity;
						}
						$current->add( $one_day_interval );
					} while ( ideapark_mod( 'booking_type' ) == 'day' ? ( $current <= $end_obj ) : ( $current < $end_obj ) );
				}
			}
			if ( $is_not_available || ! $start || ! $end || $end < $start || (int) date( 'ymd', $start ) < (int) date( 'ymd' ) || ! $vehicle || ! ideapark_check_dates( $vehicle_id, date( 'Y-m-d', $start ), date( 'Y-m-d', $end ) ) ) {
				$_s = date( 'Y-m-d', $start );
				$_e = date( 'Y-m-d', $end );
				if ( $checked_vehicle ) {
					if ( $checked_vehicle['vehicle_id'] == $vehicle_id && $checked_vehicle['start'] == $_s && $checked_vehicle['end'] == $_e ) {
						$is_someone_not_available = true;
						$errors[ $vehicle_id ]    = [
							'message' => sprintf( esc_html__( 'These days %s - %s are not available for booking', 'ideapark-antek' ), $item['start'], $item['end'] ),
							'start'   => $_s,
							'end'     => $_e,
						];
					}
				} else {
					$is_someone_not_available = true;
					$errors[ $vehicle_id ]    = [
						'message' => sprintf( esc_html__( 'These days %s - %s are not available for booking', 'ideapark-antek' ), $item['start'], $item['end'] ),
						'start'   => $_s,
						'end'     => $_e,
					];
				}
			}
			$bookings[] = [
				'vehicle_id' => $vehicle_id,
				'start'      => $start,
				'end'        => $end
			];
		}

		if ( ! $is_someone_not_available && $day_cnt && $vehicle_stock ) {
			foreach ( $vehicle_stock as $vehicle_id => $stock ) {
				if ( $rows = $wpdb->get_results( $wpdb->prepare( "SELECT day, cnt FROM {$wpdb->prefix}antek_stock WHERE vehicle_id=%d AND day>=%s AND day<=%s", $vehicle_id, $min_date->format( 'Y-m-d' ), $max_date->format( 'Y-m-d' ) ) ) ) {
					foreach ( $rows as $row ) {
						$day = date( 'Y-m-d', strtotime( $row->day ) );
						if ( isset( $day_cnt[ $vehicle_id ][ $day ] ) ) {
							$day_cnt[ $vehicle_id ][ $day ] += $row->cnt;
						} else {
							$day_cnt[ $vehicle_id ][ $day ] = $row->cnt;
						}
					}
				}
				if ( ! empty( $day_cnt[ $vehicle_id ] ) ) {
					foreach ( $day_cnt[ $vehicle_id ] as $day => $cnt ) {
						if ( $cnt > $stock ) {
							$_s = date( 'Y-m-d', $start );
							$_e = date( 'Y-m-d', $end );
							$_d = strtotime( $day );
							if ( $checked_vehicle ) {
								if ( $checked_vehicle['vehicle_id'] == $vehicle_id && $_d >= $start && $_d <= $end ) {
									$errors[ $vehicle_id ] = [
										'message' => sprintf( esc_html__( 'The day %s is not available for booking in such quantity', 'ideapark-antek' ), $day ),
										'start'   => $_s,
										'end'     => $_e,
									];
								}
							} else {
								$errors[ $vehicle_id ] = [
									'message' => sprintf( esc_html__( 'The day %s is not available for booking in such quantity', 'ideapark-antek' ), $day ),
									'start'   => $_s,
									'end'     => $_e,
								];
							}
						}
					}
				}
			}
		}

		return $errors;
	}
}

if ( ! function_exists( 'ideapark_wc_check' ) ) {
	function ideapark_wc_check() {
		if ( ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url() ) || function_exists( 'is_cart' ) && is_cart() ) {
			if ( $errors = ideapark_check_cart_availability() ) {
				foreach ( $errors as $error ) {
					wc_add_notice( $error['message'], 'error' );
				}
			}
		}
	}

	add_action( 'template_redirect', 'ideapark_wc_check' );
	add_action( 'woocommerce_checkout_process', 'ideapark_wc_check' );

}

if ( ! function_exists( 'ideapark_wc_new_order' ) ) {
	function ideapark_wc_new_order( $order_id, $posted_data, $order ) {
		ideapark_add_booking( $order_id );
	}

	add_action( 'woocommerce_checkout_order_processed', 'ideapark_wc_new_order', 10, 3 );
}

if ( ! function_exists( 'ideapark_add_booking' ) ) {
	function ideapark_add_booking( $order_id, $status_transition_from = '', $status_transition_to = '', $that = '' ) {
		global $wpdb;

		if ( ! in_array( $status_transition_to, [
				'failed',
				'refunded',
				'cancelled'
			] ) && ! $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}antek_order WHERE order_id=%d", $order_id ) ) ) {

			/** @var $order WC_Order */
			$order       = wc_get_order( $order_id );
			$items       = $order->get_items();
			$product_ids = ideapark_get_all_default_products();

			$stock_rows = [];

			foreach ( $items as $item ) {

				$item_data = $item->get_data();

				if ( in_array( $item_data['product_id'], $product_ids ) ) {
					$item_meta_data = $item->get_meta_data();
					$meta_data      = [];

					foreach ( $item_meta_data as $meta ) {
						$meta_data[ $meta->key ] = $meta->value;
					}

					$start_text = date( 'Y-m-d', strtotime( $meta_data['start'] ) );
					$end_text   = date( 'Y-m-d', strtotime( $meta_data['end'] ) + (int) ideapark_mod( 'cleaning_days' ) * 24 * 60 * 60 );

					$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}antek_order (vehicle_id, date_start, date_end, order_id) VALUES (%d, %s, %s, %d)", $meta_data['vehicle_id'], $start_text, $end_text, $order_id );
					$wpdb->query( $sql );

					$vehicle_id = $meta_data['vehicle_id'];
					$start      = new DateTime( $start_text );
					$end        = new DateTime( $end_text );
					if ( ! array_key_exists( $vehicle_id, $stock_rows ) ) {
						$stock_rows[ $vehicle_id ] = [
							'start' => $start,
							'end'   => $end,
						];
					} else {
						if ( $start < $stock_rows[ $vehicle_id ]['start'] ) {
							$stock_rows[ $vehicle_id ]['start'] = $start;
						}
						if ( $end > $stock_rows[ $vehicle_id ]['end'] ) {
							$stock_rows[ $vehicle_id ]['end'] = $end;
						}
					}
				}
			}

			if ( $stock_rows ) {
				foreach ( $stock_rows as $vehicle_id => $stock_row ) {
					ideapark_recalculate_stock( $vehicle_id, $stock_row['start'], $stock_row['end'] );
				}
			}
		}

		if ( in_array( $status_transition_to, [
			'failed',
			'refunded',
			'cancelled'
		] ) ) {
			ideapark_remove_booking( $order_id );
		}
	}

	add_action( 'woocommerce_order_status_changed', 'ideapark_add_booking', 10, 4 );
}

if ( ! function_exists( 'ideapark_remove_booking' ) ) {
	function ideapark_remove_booking( $order_id ) {
		global $wpdb;
		$rows = false;
		if ( ideapark_mod( 'unlimited_booking' ) ) {
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT vehicle_id, MIN(date_start) start, MAX(date_end) end FROM {$wpdb->prefix}antek_order WHERE order_id=%d GROUP BY vehicle_id", $order_id ) );
		}
		$sql = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_order WHERE order_id=%d", $order_id );
		$wpdb->query( $sql );
		if ( $rows ) {
			foreach ( $rows as $row ) {
				ideapark_recalculate_stock( $row->vehicle_id, $row->start, $row->end );
			}
		}
	}
}

if ( ! function_exists( 'ideapark_order_deletion' ) ) {
	function ideapark_order_deletion( $post_ID ) {
		$type = get_post_type( $post_ID );
		if ( $type == 'shop_order' ) {
			ideapark_remove_booking( $post_ID );
		}
	}

	add_action( 'wp_trash_post', 'ideapark_order_deletion', 10, 1 );
	add_action( 'before_delete_post', 'ideapark_order_deletion', 10, 1 );

}

if ( ! function_exists( 'ideapark_order_untrash' ) ) {
	function ideapark_order_untrash( $post_ID ) {
		$type = get_post_type( $post_ID );
		if ( $type == 'shop_order' ) {
			ideapark_add_booking( $post_ID );
		}
	}

	add_action( 'untrashed_post', 'ideapark_order_untrash', 10, 1 );
}

if ( ! function_exists( 'ideapark_admin_enqueue_scripts' ) ) {
	function ideapark_admin_enqueue_scripts( $hook ) {
		if ( $hook == 'post.php' ) {
			$assets_url = esc_url( trailingslashit( plugins_url( '/assets/', __FILE__ ) ) );
			wp_enqueue_style( 'daterangepicker', $assets_url . 'css/daterangepicker.css', [], '3.0.5' );
			wp_enqueue_script( 'moment-with-locales', $assets_url . 'js/moment-with-locales.min.js', [ 'jquery' ], '2.24.0', true );
			wp_enqueue_script( 'daterangepicker', $assets_url . 'js/daterangepicker.min.js', [ 'jquery' ], '3.0.5', true );
			wp_enqueue_script( 'ideapark-lib', IDEAPARK_URI . '/assets/js/site-lib.js', [ 'jquery' ], ideapark_mtime( IDEAPARK_DIR . '/assets/js/site-lib.js' ), true );
			wp_enqueue_script( 'ideapark-antek-calendar', $assets_url . 'js/calendar.js', [
				'jquery',
				'moment-with-locales',
				'daterangepicker',
				'ideapark-lib'
			], IDEAPARK_ANTEK_FUNC_VERSION, true );
			wp_localize_script( 'ideapark-antek-calendar', 'ideapark_calendar_vars', [
				'locale'             => strtolower( get_locale() ),
				'dateFormat'         => ideapark_mod( 'date_format' ),
				'bookingType'        => ideapark_mod( 'booking_type' ),
				'selectRangeMessage' => esc_html__( 'Select available range', 'ideapark-antek' ),
				'showAvailable'      => ideapark_mod( 'unlimited_booking' ),
			] );
		}
	}

	add_action( 'admin_enqueue_scripts', 'ideapark_admin_enqueue_scripts', 99, 1 );
}

if ( ! function_exists( 'ideapark_get_holidays' ) ) {
	function ideapark_get_holidays( $date_in_keys = false ) {
		$holidays = get_option( 'ideapark_holidays', [] );
		if ( $holidays && $date_in_keys ) {
			$result = [];
			foreach ( $holidays as $date ) {
				$result[ $date ] = true;
			}

			return $result;
		} else {
			return $holidays;
		}
	}
}

if ( ! function_exists( 'ideapark_set_holidays' ) ) {
	function ideapark_set_holidays( $holidays ) {
		(array) update_option( 'ideapark_holidays', $holidays );
	}
}

if ( ! function_exists( 'ideapark_holidays_scripts' ) ) {
	function ideapark_holidays_scripts() {

		$dateClass = [];
		$holidays  = ideapark_get_holidays();
		foreach ( $holidays as $date ) {
			$dateClass[ $date ] = 'holiday';
		}

		$assets_url = esc_url( trailingslashit( plugins_url( '/assets/', __FILE__ ) ) );
		$assets_dir = trailingslashit( dirname( __FILE__ ) ) . 'assets/';
		wp_enqueue_style( 'daterangepicker', $assets_url . 'css/daterangepicker.css', [], '3.0.5' );
		wp_enqueue_script( 'moment-with-locales', $assets_url . 'js/moment-with-locales.min.js', [ 'jquery' ], '2.24.0', true );
		wp_enqueue_script( 'daterangepicker', $assets_url . 'js/daterangepicker.min.js', [ 'jquery' ], '3.0.5', true );
		wp_enqueue_script( 'ideapark-admin-holidays', $assets_url . 'js/admin-holidays.js', [
			'jquery',
			'customize-controls',
			'daterangepicker'
		], ideapark_mtime( $assets_dir . 'js/admin-holidays.js' ), true );
		wp_localize_script( 'ideapark-admin-holidays', 'ideapark_holidays_vars', [
			'locale'     => strtolower( get_locale() ),
			'dateFormat' => ideapark_mod( 'date_format' ),
			'dateClass'  => $dateClass,
		] );
	}

	add_action( 'customize_controls_enqueue_scripts', 'ideapark_holidays_scripts' );
}

if ( ! function_exists( 'ideapark_ajax_holiday_action' ) ) {
	function ideapark_ajax_holiday_action() {
		ob_start();
		$is_success = false;
		if (
			! empty( $_POST['date'] ) && ( $date = $_POST['date'] ) &&
			! empty( $_POST['button'] ) && ( $button = $_POST['button'] )
		) {
			try {
				$holidays = ideapark_get_holidays();
				switch ( $button ) {
					case 'add':
						if ( ! array_key_exists( $date, $holidays ) ) {
							$holidays[] = $date;
							ideapark_set_holidays( $holidays );
							$is_success = true;
							break;
						}
					case 'remove':
						$holidays = \array_diff( $holidays, [ $date ] );
						ideapark_set_holidays( $holidays );
						$is_success = true;
						break;
				}

			} catch ( Exception $e ) {
			}
		}
		ob_end_clean();
		wp_send_json( [ 'success' => $is_success ] );
	}

	add_action( 'wp_ajax_ideapark_ajax_holiday_action', 'ideapark_ajax_holiday_action' );
}

if ( ! function_exists( 'ideapark_save_vehicle' ) ) {
	function ideapark_save_vehicle( $post_id = 0 ) {
		global $wpdb;
		static $saved = [];

		if ( ! $post_id ) {
			return;
		}

		if ( in_array( $post_id, $saved ) ) {
			return;
		}
		$saved[] = $post_id;

		if ( isset( $_POST['_inline_edit'] ) && wp_verify_nonce( $_POST['_inline_edit'], 'inlineeditnonce' ) ) {
			return;
		}


		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( $post_type == 'catalog' ) {

			$price            = get_post_meta( $post_id, 'price', true );
			$price_week       = get_post_meta( $post_id, 'price_week', true );
			$price_month      = get_post_meta( $post_id, 'price_month', true );
			$price_delivery   = get_post_meta( $post_id, 'price_delivery', true );
			$price_on_request = get_post_meta( $post_id, 'price_on_request', true );
			$price            = ! empty( $price ) && ! $price_on_request ? $price : 0;

			if ( ideapark_mod( 'wc_integration' ) == 'each' ) {
				if ( $product_id = ideapark_get_default_product( $post_id ) ) {
					$title = get_the_title( $post_id );
					wp_update_post( [
						'ID'         => $product_id,
						'post_title' => $title,
					] );
				}
			}

			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_delivery WHERE vehicle_id=%d", $post_id ) );
			$price_delivery_loc = get_post_meta( $post_id, 'price_delivery_loc', true );
			$price_delivery_loc = ! empty( $price_delivery_loc ) ? $price_delivery_loc : [];
			if ( $price_delivery != null ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}antek_delivery (vehicle_id, location_id, price) VALUES (%d, %d, %f)", $post_id, 0, (float) $price_delivery ) );
			}
			$price_delivery_loc_ids = [];
			$is_change              = false;
			foreach ( $price_delivery_loc as $i => $cond ) {
				if ( (int) $cond['location'] > 0 && ( (float) $cond['price'] > 0 || $cond['price'] === '0' ) && ! in_array( $cond['location'], $price_delivery_loc_ids ) ) {
					$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}antek_delivery (vehicle_id, location_id, price) VALUES (%d, %d, %f)", $post_id, (int) $cond['location'], (float) $cond['price'] );
					$wpdb->query( $sql );
					$price_delivery_loc_ids[] = $cond['location'];
				} else {
					unset( $price_delivery_loc[ $i ] );
					$is_change = true;
				}
			}
			if ( $is_change ) {
				delete_post_meta( $post_id, 'price_delivery_loc' );
				update_post_meta( $post_id, 'price_delivery_loc', $price_delivery_loc );
			}

			if ( get_option( 'antek_mod_price_type' ) != 'cond' ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_price WHERE vehicle_id=%d", $post_id ) );
				$wpdb->insert( "{$wpdb->prefix}antek_price", [
					'vehicle_id'     => $post_id,
					'price'          => $price ? (float) $price : ( $price_on_request ? 0 : null ),
					'price_week'     => $price_week ? (float) $price_week : ( $price_on_request ? 0 : null ),
					'price_month'    => $price_month ? (float) $price_month : ( $price_on_request ? 0 : null ),
					'price_delivery' => $price_delivery != null ? (float) $price_delivery : null,
				] );
			} else {
				$price_cond = get_post_meta( $post_id, 'price_cond', true );
				$price_cond = ! empty( $price_cond ) && ! $price_on_request ? $price_cond : [];
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_price_cond WHERE vehicle_id=%d", $post_id ) );
				$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}antek_price_cond (vehicle_id, condition_id, price) VALUES (%d, %d, %f)", $post_id, 0, (float) $price );
				$wpdb->query( $sql );
				$condition_ids = [];
				$is_change     = false;
				foreach ( $price_cond as $i => $cond ) {
					if ( (int) $cond['condition'] > 0 && ( (float) $cond['price'] > 0 || $cond['price'] === '0' ) && ! in_array( $cond['condition'], $condition_ids ) ) {
						$sql = $wpdb->prepare( "INSERT INTO {$wpdb->prefix}antek_price_cond (vehicle_id, condition_id, price) VALUES (%d, %d, %f)", $post_id, (int) $cond['condition'], (float) $cond['price'] );
						$wpdb->query( $sql );
						$condition_ids[] = $cond['condition'];
					} else {
						unset( $price_cond[ $i ] );
						$is_change = true;
					}
				}
				if ( $is_change ) {
					delete_post_meta( $post_id, 'price_cond' );
					update_post_meta( $post_id, 'price_cond', $price_cond );
				}
			}

			if ( $numeric = ideapark_get_numeric_fields() ) {
				foreach ( $numeric as $field_slug => $field_mysql ) {
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_filter WHERE vehicle_id=%d AND field=%s", $post_id, $field_slug ) );
					$value = get_post_meta( $post_id, $field_slug, true );

					if ( $value !== null && $value !== '' ) {
						$wpdb->insert( "{$wpdb->prefix}antek_filter", [
							'vehicle_id' => $post_id,
							'field'      => $field_slug,
							'value'      => (float) $value,
						] );
					}
				}
			}
		}
	}

	add_action( 'save_post', 'ideapark_save_vehicle', 999, 1 );
	add_action( 'wpml_after_copy_custom_field', function ( $post_id_from, $post_id_to, $meta_key ) {
		if ( $meta_key == 'price' ) {
			ideapark_save_vehicle( $post_id_to );
		}
	}, 999, 4 );
	add_action( 'wpml_pro_translation_completed', function ( $new_post_id ) {
		ideapark_save_vehicle( $new_post_id );
	}, 999, 1 );
}

if ( ! function_exists( 'ideapark_save_all_vehicle' ) ) {
	function ideapark_save_all_vehicle() {
		$args  = [
			'numberposts' => - 1,
			'post_type'   => 'catalog',
		];
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			ideapark_save_vehicle( $post->ID );
		}
	}

	add_action( 'import_end', 'ideapark_save_all_vehicle' );
}

if ( ! function_exists( 'ideapark_post_duplicator' ) ) {
	function ideapark_post_duplicator( $original_id, $duplicate_id ) {
		ideapark_save_vehicle( $duplicate_id );
	}

	add_action( 'mtphr_post_duplicator_created', 'ideapark_post_duplicator', 100, 2 );
}

if ( ! function_exists( 'ideapark_trash_post' ) ) {
	function ideapark_trash_post( $post_id ) {
		global $wpdb;
		if ( 'catalog' == get_post_type( $post_id ) ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_price WHERE vehicle_id=%d", $post_id ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_price_cond WHERE vehicle_id=%d", $post_id ) );
		}
	}

	add_action( 'trashed_post', 'ideapark_trash_post', 10, 1 );
}

if ( ! function_exists( 'ideapark_delete_condition' ) ) {
	function ideapark_delete_condition( $term_id ) {
		global $wpdb;
		$vehicle_ids = $wpdb->get_col( $wpdb->prepare( "SELECT vehicle_id FROM {$wpdb->prefix}antek_price_cond WHERE condition_id=%d", $term_id ) );
		if ( $vehicle_ids ) {
			foreach ( $vehicle_ids as $vehicle_id ) {
				$price_cond = get_post_meta( $vehicle_id, 'price_cond', true );
				foreach ( $price_cond as $i => $cond ) {
					if ( (int) $cond['condition'] == $term_id ) {
						unset( $price_cond[ $i ] );
					}
				}
				update_post_meta( $vehicle_id, 'price_cond', $price_cond );
			}
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_price_cond WHERE condition_id=%d", $term_id ) );
		}
	}

	add_action( 'delete_condition', 'ideapark_delete_condition', 10, 1 );
}

if ( ! function_exists( 'ideapark_get_price' ) ) {
	function ideapark_get_price( $vehicle_id, $start, $end, $delivery = null, $location_id = 0 ) {
		global $wpdb;

		$sql_price   = ideapark_get_range_sql( $start, $end, $delivery, $vehicle_id, false, $location_id );
		$price_total = $wpdb->get_var( "SELECT {$sql_price}" );

		return $price_total;
	}
}

if ( ! function_exists( 'ideapark_get_minimum_days' ) ) {

	function ideapark_get_minimum_days( $return = false, $dates = null ) {

		global $ideapark_minimum_days_conditional_error;

		$ideapark_minimum_days_conditional_error = '';

		$condition_id_1 = ideapark_mod( 'condition_minimum_days' );
		$condition_id_2 = ideapark_mod( 'condition_minimum_days_2' );
		$condition_id_3 = ideapark_mod( 'condition_minimum_days_3' );
		$condition_id_4 = ideapark_mod( 'condition_minimum_days_4' );

		$days_1 = ideapark_mod( 'conditional_minimum_days' );
		$days_2 = ideapark_mod( 'conditional_minimum_days_2' );
		$days_3 = ideapark_mod( 'conditional_minimum_days_3' );
		$days_4 = ideapark_mod( 'conditional_minimum_days_4' );

		$is_inc_1 = ideapark_mod( 'condition_minimum_days_included' );
		$is_inc_2 = ideapark_mod( 'condition_minimum_days_included_2' );
		$is_inc_3 = ideapark_mod( 'condition_minimum_days_included_3' );
		$is_inc_4 = ideapark_mod( 'condition_minimum_days_included_4' );

		$err_msg_1 = ideapark_mod( 'condition_minimum_days_error' );
		$err_msg_2 = ideapark_mod( 'condition_minimum_days_error_2' );
		$err_msg_3 = ideapark_mod( 'condition_minimum_days_error_3' );
		$err_msg_4 = ideapark_mod( 'condition_minimum_days_error_4' );

		$default_err = ideapark_minimum_days_error_message();

		if ( ! ( $condition_id_1 && $days_1 || $condition_id_2 && $days_2 || $condition_id_3 && $days_3 || $condition_id_4 && $days_4 ) ) {
			$days = ideapark_mod( 'minimum_days' );
		} else {
			$dates         = $dates ? $dates : ideapark_get_filter_dates_range();
			$conditions    = ideapark_get_active_conditions( $dates['start'], $dates['end'] );
			$condition_ids = array_keys( $conditions );
			if ( in_array( $condition_id_4, $condition_ids ) ) {
				$days = $days_4;
				if ( $is_inc_4 && sizeof( $conditions[ $condition_id_4 ] ) < $days_4 ) {
					$ideapark_minimum_days_conditional_error = $err_msg_4 ? $err_msg_4 : ( $default_err . ' ' . $days );
				}
			} elseif ( in_array( $condition_id_3, $condition_ids ) ) {
				$days = $days_3;
				if ( $is_inc_3 && sizeof( $conditions[ $condition_id_3 ] ) < $days_3 ) {
					$ideapark_minimum_days_conditional_error = $err_msg_3 ? $err_msg_3 : ( $default_err . ' ' . $days );
				}
			} elseif ( in_array( $condition_id_2, $condition_ids ) ) {
				$days = $days_2;
				if ( $is_inc_2 && sizeof( $conditions[ $condition_id_2 ] ) < $days_2 ) {
					$ideapark_minimum_days_conditional_error = $err_msg_2 ? $err_msg_2 : ( $default_err . ' ' . $days );
				}
			} elseif ( in_array( $condition_id_1, $condition_ids ) ) {
				$days = $days_1;
				if ( $is_inc_1 && sizeof( $conditions[ $condition_id_1 ] ) < $days_1 ) {
					$ideapark_minimum_days_conditional_error = $err_msg_1 ? $err_msg_1 : ( $default_err . ' ' . $days );
				}
			} else {
				$days = ideapark_mod( 'minimum_days' );
			}
		}

		if ( $return ) {
			return $days;
		} else {
			$return = [ 'minimum_days' => $days, 'maximum_days' => (int) ideapark_mod( 'maximum_days' ) ];
			if ( $ideapark_minimum_days_conditional_error ) {
				$return['error'] = $ideapark_minimum_days_conditional_error;
			}
			wp_send_json( $return );
		}
	}

	add_action( 'wp_ajax_ideapark_minimum', 'ideapark_get_minimum_days' );
	add_action( 'wp_ajax_nopriv_ideapark_minimum', 'ideapark_get_minimum_days' );
}

if ( ! function_exists( 'ideapark_get_range_sql' ) ) {
	function ideapark_get_range_sql( $start, $end, $delivery, $vehicle_id = '', $suppress_cache = false, $location_id = 0 ) {
		global $wpdb, $sitepress;
		static $cache = [];

		$key = md5( $start->format( 'Y-m-d' ) . $end->format( 'Y-m-d' ) . '-' . $delivery . '-' . $vehicle_id . '-' . $location_id );

		$default_lang = isset( $sitepress ) ? apply_filters( 'wpml_default_language', '' ) : '';

		if ( $suppress_cache ) {
			$cache = [];
		}

		if ( array_key_exists( $key, $cache ) ) {
			return $cache[ $key ];
		}

		$sql = '0';

		if ( ! (int) $vehicle_id ) {
			$vehicle_id = $default_lang ? "(SELECT tridt_p_2.element_id FROM {$wpdb->prefix}icl_translations tridt_p_2 INNER JOIN {$wpdb->prefix}icl_translations tridt_p ON tridt_p_2.trid = tridt_p.trid WHERE tridt_p.element_type='post_catalog' AND tridt_p_2.language_code='" . esc_sql( $default_lang ) . "' AND tridt_p.element_id={$wpdb->posts}.ID LIMIT 1)" : "{$wpdb->posts}.ID";
		} else {
			$vehicle_id = (int) $vehicle_id;
		}

		$delivery_sql = '';
		if ( $delivery || ideapark_mod( 'disable_self_pickup' ) && $delivery !== null ) {
			$delivery_sql = $wpdb->prepare( "(SELECT price FROM {$wpdb->prefix}antek_delivery WHERE vehicle_id={$vehicle_id} AND location_id=%d)", 0 );
			if ( $location_id ) {
				$delivery_sql = $wpdb->prepare( "(SELECT price FROM {$wpdb->prefix}antek_delivery WHERE vehicle_id={$vehicle_id}  AND location_id=%d)", $location_id ) . ',' . $delivery_sql;
			} else {
				$delivery_sql = $delivery_sql . ',' . "(SELECT MIN(price) FROM {$wpdb->prefix}antek_delivery WHERE vehicle_id={$vehicle_id})";
			}
			$delivery_sql = ( ideapark_mod( 'disable_self_pickup' ) && $delivery !== null ? " + COALESCE($delivery_sql,0)" : ( $delivery ? " + COALESCE($delivery_sql)" : "" ) );
		}

		if ( ideapark_mod( 'price_type' ) != 'cond' ) {
			$vars     = [];
			$sql_list = [];
			$diff     = $start->diff( $end );
			$days     = abs( (int) $diff->format( '%a' ) ) + ( ideapark_mod( 'booking_type' ) == 'day' ? 1 : 0 );

			if ( $days > 0 ) {
				$m  = abs( (int) $diff->format( '%m' ) ) + abs( (int) $diff->format( '%y' ) ) * 12;
				$d  = abs( (int) $diff->format( '%d' ) + ( ideapark_mod( 'booking_type' ) == 'day' ? 1 : 0 ) );
				$w  = (int) floor( $d / 7 );
				$_d = $d - $w * 7;

				$w_all = round( ceil( $days / 7 ) );
				$m_all = $m + ( $d ? 1 : 0 );

				$w_w = floor( $days / 7 );
				$d_w = $days - $w_w * 7;

				$vars[] = [
					'days' => $days,
				];

				if ( $w_w ) {
					$vars[] = [
						'days'  => $d_w,
						'weeks' => $w_w,
					];
					if ( ! $m ) {
						$vars[] = [
							'months' => 1
						];
					}
				}

				$vars[] = [
					'weeks' => $w_w + ( $d_w ? 1 : 0 ),
				];

				if ( $m ) {
					$vars[] = [
						'days'   => $d,
						'months' => $m,
					];

					$vars[] = [
						'days'   => $_d,
						'weeks'  => $w,
						'months' => $m,
					];

					$vars[] = [
						'weeks'  => $w + ( $_d ? 1 : 0 ),
						'months' => $m,
					];

					$vars[] = [
						'months' => $m + ( $d ? 1 : 0 ),
					];
				}

				if ( ideapark_mod( 'calc_algorithm' ) == 'simple' ) {

					usort( $vars, function ( $a, $b ) {
						$_a = sizeof( $a ) * 1000 + ( isset( $a['months'] ) ? 1 : 0 ) + ( isset( $a['weeks'] ) ? 10 : 0 ) + ( isset( $a['days'] ) ? 100 : 0 );
						$_b = sizeof( $b ) * 1000 + ( isset( $b['months'] ) ? 1 : 0 ) + ( isset( $b['weeks'] ) ? 10 : 0 ) + ( isset( $b['days'] ) ? 100 : 0 );
						if ( $_a == $_b ) {
							return 0;
						}

						return ( $_a < $_b ) ? 1 : - 1;
					} );
					$items = [];
					foreach ( $vars as $var ) {
						$params = [];
						foreach ( $var as $key => $val ) {
							switch ( $key ) {
								case 'days':
									$params[] = 'price*' . $val;
									break;
								case 'weeks':
									$params[] = 'price_week*' . $val;
									break;
								case 'months':
									$params[] = 'price_month*' . $val;
									break;
							}
						}

						$items[] = implode( ' + ', $params );
					}

					$items[]    = "{$days} * price";
					$items[]    = "{$w_all} * price_week";
					$items[]    = "{$m_all} * price_month";
					$sql_list[] = "COALESCE(" . implode( ', ', $items ) . ")";
				} else {
					foreach ( $vars as $var ) {
						$params = [];
						foreach ( $var as $key => $val ) {
							switch ( $key ) {
								case 'days':
									$params[] = 'price*' . $val;
									break;
								case 'weeks':
									$params[] = 'price_week*' . $val;
									break;
								case 'months':
									$params[] = 'price_month*' . $val;
									break;
							}
						}

						$sql_list[] = "COALESCE(" . implode( ' + ', $params ) . ", {$days} * price, {$w_all} * price_week, {$m_all} * price_month)";
					}
				}

			}

			if ( ! $sql_list ) {
				$sql = "0" . $delivery_sql;
			} elseif ( sizeof( $sql_list ) == 1 ) {
				$sql = "(SELECT {$sql_list[0]} FROM {$wpdb->prefix}antek_price WHERE vehicle_id={$vehicle_id})" . $delivery_sql;
			} else {
				$sql = "(SELECT LEAST(" . implode( ',', $sql_list ) . ") FROM {$wpdb->prefix}antek_price WHERE vehicle_id={$vehicle_id})" . $delivery_sql;
			}
		} else {
			$range           = ideapark_get_active_conditions( $start, $end, is_int( $vehicle_id ) ? $vehicle_id : 0, true );
			$fixed_price_ids = ideapark_mod( '_fixed_price_ids' );
			$diff            = abs( (int) $start->diff( $end )->format( '%a' ) ) + ( ideapark_mod( 'booking_type' ) == 'day' ? 1 : 0 );

			$sql_list = [];

			$range = array_filter( $range, function ( $item ) {
				return ! empty( $item );
			} );

			if ( empty( $range ) ) {
				$sql = "(SELECT price*{$diff} FROM {$wpdb->prefix}antek_price_cond WHERE (vehicle_id={$vehicle_id} AND condition_id=0)) " . $delivery_sql;

				return $sql;
			}

			$term_ids_reverse = array_keys( $range );

			$index = $diff + 1;

			for ( $i = 1; $i < $index; $i ++ ) {
				$range[0][] = $i;
			}

			$max = pow( 2, sizeof( $term_ids_reverse ) ) - 1;
			for ( $i = $max; $i >= 0; $i -- ) {
				$bin = sprintf( "%0" . sizeof( $term_ids_reverse ) . "s", decbin( $i ) );
				if ( $i ) {
					$seq = array_filter( $term_ids_reverse, function ( $index ) use ( $bin ) {
						return $bin[ $index ] == '1';
					}, ARRAY_FILTER_USE_KEY );
				} else {
					$seq = [];
				}
				$seq[]  = 0;
				$base   = $range[0];
				$from   = [];
				$params = [];
				$where  = [];
				foreach ( $seq as $term_id ) {
					$days_count = sizeof( array_intersect( $base, $range[ $term_id ] ) );
					if ( $days_count ) {
						$j    = '_' . $term_id;
						$base = array_diff( $base, $range[ $term_id ] );
						if ( is_array( $fixed_price_ids ) && in_array( $term_id, $fixed_price_ids ) ) {
							$params[] = "ap{$j}.price";
						} else {
							$params[] = "ap{$j}.price*{$days_count}";
						}
						$from[]  = "{$wpdb->prefix}antek_price_cond ap{$j}";
						$where[] = "(ap{$j}.vehicle_id={$vehicle_id} AND ap{$j}.condition_id={$term_id})";
					}
				}
				if ( $from ) {
					$sql_list[] = "(SELECT " . implode( ' + ', $params ) . "\n FROM " . implode( ', ', $from ) . "\n WHERE " . implode( ' AND ', $where ) . ")";
				}
			}

			$sql = "COALESCE(" . implode( ',', $sql_list ) . ")" . $delivery_sql;
		}

		$cache[ $key ] = $sql;

		return $sql;
	}
}

if ( ! function_exists( 'ideapark_get_min_date' ) ) {
	function ideapark_get_min_date( $date_format = 'Y-m-d' ) {
		return (int) ideapark_mod( 'hours_before_booking' ) ? ( new DateTime() )->add( new DateInterval( 'PT' . ideapark_mod( 'hours_before_booking' ) . 'H' ) )->format( $date_format ) : date( $date_format );
	}
}

if ( ! function_exists( 'ideapark_get_filter_dates_range' ) ) {
	function ideapark_get_filter_dates_range( $is_check_dates = false, $is_check_min_max = false, $is_set_min_days = false ) {
		static $range;

		$cache_key = ( $is_check_dates ? 'C' : '' ) . '-' . ( $is_check_min_max ? 'M' : '' ) . '-' . ( $is_set_min_days ? 'D' : '' );

		if ( isset( $range[ $cache_key ] ) ) {
			return $range[ $cache_key ];
		}

		if ( ideapark_mod( 'disable_booking' ) ) {
			$start = new DateTime();
			$start->settime( 0, 0 );
			$end = clone $start;
			if ( ideapark_mod( 'booking_type' ) == 'night' ) {
				$end->add( new DateInterval( 'P1D' ) );
			}
		} else {
			$is_default = true;

			if ( ! empty( $_REQUEST['start'] ) && ! empty( $_REQUEST['end'] ) ) {
				try {
					$today = new DateTime( ideapark_get_min_date() );
					$today->settime( 0, 0 );
					$start = new DateTime( $_REQUEST['start'] );
					$start->settime( 0, 0 );
					$end = new DateTime( $_REQUEST['end'] );
					$end->settime( 0, 0 );
					$is_default = ( ideapark_mod( 'booking_type' ) == 'day' ? $end < $start : $end <= $start ) || $start < $today;
					if ( $is_check_min_max ) {
						$minimal_days = ideapark_get_minimum_days( true );
						$maximum_days = ideapark_mod( 'maximum_days' );
						$days         = abs( (int) $start->diff( $end )->format( '%a' ) ) + ( ideapark_mod( 'booking_type' ) == 'day' ? 1 : 0 );
						if ( $maximum_days && $days > $maximum_days ) {
							$is_default = true;
						}
						if ( $days < $minimal_days ) {
							$is_default = true;
						}
					}
				} catch ( Exception $e ) {
					$is_default = true;
				}
			}

			if ( $is_check_dates && $is_default ) {
				$range[ $cache_key ] = false;

				return $range[ $cache_key ];
			}

			if ( $is_default ) {
				$start = new DateTime( ideapark_get_min_date() );
				$start->settime( 0, 0 );
				$_end = clone $start;
				if ( ideapark_mod( 'booking_type' ) == 'night' ) {
					$_end->add( new DateInterval( 'P1D' ) );
				}
				if ( $is_set_min_days ) {
					$end          = clone $start;
					$minimal_days = ideapark_get_minimum_days( true, [
						'start' => $start,
						'end'   => $_end
					] );
					if ( ideapark_mod( 'booking_type' ) == 'day' ) {
						$minimal_days --;
					}
					if ( $minimal_days > 0 ) {
						$end->add( new DateInterval( 'P' . $minimal_days . 'D' ) );
					}
				} else {
					$end = $_end;
				}
			}
		}

		$range[ $cache_key ] = [
			'start'       => $start,
			'end'         => $end,
			'diff'        => abs( (int) $start->diff( $end )->format( '%a' ) ) + ( ideapark_mod( 'booking_type' ) == 'day' ? 1 : 0 ),
			'delivery'    => ideapark_mod( '_delivery_on' ) && ! empty( $_REQUEST['delivery'] ) ? 1 : 0,
			'location_id' => ! empty( $_REQUEST['pick_up'] ) || ! empty( $_REQUEST['pickup'] ) ? apply_filters( 'wpml_object_id', absint( ! empty( $_REQUEST['pick_up'] ) ? $_REQUEST['pick_up'] : $_REQUEST['pickup'] ), 'location', true, apply_filters( 'wpml_default_language', null ) ) : 0
		];

		if ( ideapark_mod( 'pickup_dropoff_time' ) && ! ideapark_mod( 'disable_booking' ) ) {
			$range[ $cache_key ]['start_time'] = ideapark_validate_time( ! empty( $_REQUEST['start_time'] ) ? $_REQUEST['start_time'] : '', 'pickup' );
			$range[ $cache_key ]['end_time']   = ideapark_validate_time( ! empty( $_REQUEST['end_time'] ) ? $_REQUEST['end_time'] : '', 'dropoff' );
		}

		return $range[ $cache_key ];
	}
}

if ( ! function_exists( 'ideapark_filter_price_fields' ) ) {
	function ideapark_filter_price_fields( $sql ) {
		remove_filter( 'posts_fields', 'ideapark_filter_price_fields', 99 );
		$dates_range = ideapark_get_filter_dates_range();

		return $sql . ', ' . ideapark_get_range_sql( $dates_range['start'], $dates_range['end'], $dates_range['delivery'], '', false, $dates_range['location_id'] ) . ' AS price_total';
	}
}

if ( ! function_exists( 'ideapark_filter_price_where' ) ) {
	function ideapark_filter_price_where( $where ) {
		global $wpdb;
		remove_filter( 'posts_where', 'ideapark_filter_price_where', 99 );
		$dates_range = ideapark_get_filter_dates_range();
		$price_sql   = ideapark_get_range_sql( $dates_range['start'], $dates_range['end'], $dates_range['delivery'], '', false, $dates_range['location_id'] );

		if ( ! empty( $_REQUEST['min_price'] ) ) {
			$where .= $wpdb->prepare( ' AND ' . $price_sql . ' >= %d', $_REQUEST['min_price'] );
		}
		if ( ! empty( $_REQUEST['max_price'] ) ) {
			$where .= $wpdb->prepare( ' AND ' . $price_sql . ' <= %d', $_REQUEST['max_price'] );
		}

		return $where;
	}
}

if ( ! function_exists( 'ideapark_filter_price_orderby_low' ) ) {
	function ideapark_filter_price_orderby_low( $orderby ) {
		remove_filter( 'posts_orderby', 'ideapark_filter_price_orderby_low', 99 );
		$dates_range = ideapark_get_filter_dates_range();
		$price_sql   = ideapark_get_range_sql( $dates_range['start'], $dates_range['end'], $dates_range['delivery'], '', false, $dates_range['location_id'] );
		$orderby     = $price_sql . ' ASC';

		return $orderby;
	}
}

if ( ! function_exists( 'ideapark_filter_price_orderby_high' ) ) {
	function ideapark_filter_price_orderby_high( $orderby ) {
		remove_filter( 'posts_orderby', 'ideapark_filter_price_orderby_high', 99 );
		$dates_range = ideapark_get_filter_dates_range();
		$price_sql   = ideapark_get_range_sql( $dates_range['start'], $dates_range['end'], $dates_range['delivery'], '', false, $dates_range['location_id'] );
		$orderby     = $price_sql . ' DESC';

		return $orderby;
	}
}

if ( ! function_exists( 'ideapark_filter_groupby' ) ) {
	function ideapark_filter_groupby( $groupby ) {
		global $wpdb;
		remove_filter( 'posts_groupby', 'ideapark_filter_groupby', 99 );

		return $wpdb->posts . '.ID HAVING price_total IS NOT NULL ';
	}
}

if ( ! function_exists( 'ideapark_filter' ) ) {
	function ideapark_filter( $query ) {
		/** @var $query WP_Query */

		if ( ! is_admin() && $query->is_main_query() && ( $query->is_post_type_archive( 'catalog' ) || $query->is_tax( 'vehicle_type' ) ) ) {

			$query->set( 'posts_per_page', max( ideapark_mod( 'catalog_per_page' ), 1 ) );

			if ( ideapark_is_favorites_list() ) {
				$ids = ideapark_get_favorites();
				$query->set( 'post__in', $ids ? $ids : [ 0 ] );

				return;
			}

			if ( ! empty( $_REQUEST['start'] ) && ! empty( $_REQUEST['end'] ) ) {
				add_filter( 'posts_join', 'ideapark_filter_dates_join', 10, 2 );
				add_filter( 'posts_where', 'ideapark_filter_dates_where', 10, 2 );
			}

			add_filter( 'posts_join', 'ideapark_filter_details_join', 10, 3 );
			add_filter( 'posts_fields', 'ideapark_filter_price_fields', 99 );
			add_filter( 'posts_where', 'ideapark_filter_price_where', 99 );
//			add_filter( 'posts_groupby', 'ideapark_filter_groupby', 99 );

			/*if ( ideapark_mod( '_delivery_on' ) && ! empty( $_REQUEST['delivery'] ) ) {
				$current_meta = $query->get( 'meta_query' );
				if ( ! $current_meta ) {
					$current_meta = [];
				}
				$current_meta['relation'] = 'AND';

				$current_meta[] = [
					'key'     => 'price_delivery',
					'type'    => 'UNSIGNED',
					'value'   => 0,
					'compare' => '>='
				];

				$query->set( 'meta_query', [ $current_meta ] );
			}*/

			if ( ! empty( $_REQUEST['pickup'] ) && abs( (int) $_REQUEST['pickup'] ) ) {
				$current_tax = $query->get( 'tax_query' );
				if ( ! $current_tax ) {
					$current_tax = [];
				}
				$current_tax['relation'] = 'AND';
				if ( $term_id = abs( (int) $_REQUEST['pickup'] ) ) {
					$current_tax[] = [
						'taxonomy' => 'location',
						'field'    => 'term_id',
						'terms'    => $term_id,
					];
				}
				$query->set( 'tax_query', [ $current_tax ] );
			}

			$params = ideapark_get_cookie_params();

			switch ( $params['sort'] ) {
				case 'menu_order':
					$query->set( 'orderby', 'menu_order' );
					$query->set( 'order', 'ASC' );
					break;
				case 'newest':
					$query->set( 'orderby', 'date' );
					$query->set( 'order', 'DESC' );
					$query->query['suppress_filters'] = true;
					break;
				case 'low_price':
					add_filter( 'posts_orderby', 'ideapark_filter_price_orderby_low', 99 );
					break;
				case 'high_price':
					add_filter( 'posts_orderby', 'ideapark_filter_price_orderby_high', 99 );
					break;
			}

		} elseif ( $query->is_search() && function_exists( 'ideapark_get_all_default_products' ) ) {
			$current_meta = $query->get( 'post__not_in' );
			if ( $post_ids = ideapark_get_all_default_products() ) {
				$current_meta = array_merge( $current_meta, $post_ids );
				$query->set( 'post__not_in', $current_meta );
			}
		}
	}

	add_action( 'pre_get_posts', 'ideapark_filter' );
}

if ( ! function_exists( 'ideapark_filter_dates_join' ) ) {
	function ideapark_filter_dates_join( $join, $wp_query ) {

		global $wpdb;

		if ( $dates = ideapark_get_filter_dates_range( false, true ) ) {

			if ( ideapark_mod( 'cleaning_days' ) ) {
				$dates['end']->add( new DateInterval( 'P' . ideapark_mod( 'cleaning_days' ) . 'D' ) );
			}

			$start_text = $dates['start']->format( 'Y-m-d' );
			$end_text   = $dates['end']->format( 'Y-m-d' );

			global $sitepress;
			if ( isset( $sitepress ) ) {
				$default_lang      = apply_filters( 'wpml_default_language', '' );
				$sitepress_ids_sql = "(SELECT tridt_2.element_id FROM {$wpdb->prefix}icl_translations tridt_2 INNER JOIN {$wpdb->prefix}icl_translations tridt ON tridt_2.trid = tridt.trid WHERE tridt.element_type='post_catalog' AND tridt.element_id={$wpdb->posts}.ID)";
				$sitepress_id_sql  = "(SELECT tridt_p_2.element_id FROM {$wpdb->prefix}icl_translations tridt_p_2 INNER JOIN {$wpdb->prefix}icl_translations tridt_p ON tridt_p_2.trid = tridt_p.trid WHERE tridt_p.element_type='post_catalog' AND tridt_p_2.language_code='" . esc_sql( $default_lang ) . "' AND tridt_p.element_id={$wpdb->posts}.ID LIMIT 1)";
			}

			if ( isset( $sitepress ) ) {
				$join .= $wpdb->prepare( "
					LEFT JOIN {$wpdb->prefix}antek_order ao ON ( ao.vehicle_id IN ({$sitepress_ids_sql}) " . ( ideapark_mod( 'unlimited_booking' ) ? " AND ao.order_id IS NULL " : "" ) . " AND " . ideapark_mysql_dates_cond() . " )",
					$start_text, $start_text, $end_text, $end_text, $start_text, $end_text );
			} else {
				$join .= $wpdb->prepare( "
					LEFT JOIN {$wpdb->prefix}antek_order ao ON ( ao.vehicle_id={$wpdb->posts}.ID " . ( ideapark_mod( 'unlimited_booking' ) ? " AND ao.order_id IS NULL " : "" ) . " AND " . ideapark_mysql_dates_cond() . " )",
					$start_text, $start_text, $end_text, $end_text, $start_text, $end_text );
			}

			if ( ideapark_mod( 'unlimited_booking' ) ) {
				$join .= " LEFT JOIN {$wpdb->postmeta} m_stock ON m_stock.meta_key = 'stock' AND m_stock.post_id = " . ( isset( $sitepress ) ? $sitepress_id_sql : "{$wpdb->posts}.ID" );
				$join .= $wpdb->prepare( " LEFT JOIN {$wpdb->prefix}antek_stock ast ON ( ast.vehicle_id=" . ( isset( $sitepress ) ? $sitepress_id_sql : "{$wpdb->posts}.ID" ) . " AND ast.cnt >= m_stock.meta_value AND m_stock.meta_value > 0 AND " . ( ideapark_mod( 'booking_type' ) == 'day' ? "ast.day>=%s AND ast.day<=%s" : "ast.day>%s AND ast.day<%s" ) . ")", $start_text, $end_text );
			}
		} else {
			$join .= " INNER JOIN {$wpdb->prefix}antek_order ao ON false ";
		}

		remove_filter( 'posts_join', 'ideapark_filter_dates_join', 10 );

		return $join;
	}
}

if ( ! function_exists( 'ideapark_filter_details_join' ) ) {
	function ideapark_filter_details_join( $join, $wp_query ) {
		global $wpdb;

		if ( $numeric = ideapark_get_numeric_fields() ) {
			$index = 1;
			foreach ( $numeric as $slug => $slug_sql ) {
				if ( ! empty( $_REQUEST[ 'min_' . $slug ] ) || ! empty( $_REQUEST[ 'max_' . $slug ] ) ) {
					$join .= " INNER JOIN {$wpdb->prefix}antek_filter af{$index} ON af{$index}.vehicle_id = {$wpdb->posts}.ID AND af{$index}.field='{$slug_sql}' " .
					         ( ! empty( $_REQUEST[ 'min_' . $slug ] ) ? $wpdb->prepare( " AND af{$index}.value >= %f", $_REQUEST[ 'min_' . $slug ] ) : '' ) .
					         ( ! empty( $_REQUEST[ 'max_' . $slug ] ) ? $wpdb->prepare( " AND af{$index}.value <= %f", $_REQUEST[ 'max_' . $slug ] ) : '' );
				}
				$index ++;
			}
		}

		remove_filter( 'posts_join', 'ideapark_filter_details_join', 10 );

		return $join;
	}
}

if ( ! function_exists( 'ideapark_filter_dates_where' ) ) {
	function ideapark_filter_dates_where( $where, $wp_query ) {

		$where .= " AND ao.vehicle_id IS NULL ";
		if ( ideapark_mod( 'unlimited_booking' ) ) {
			$where .= " AND (m_stock.meta_value IS NULL OR m_stock.meta_value=0 OR ast.vehicle_id IS NULL) ";
		}

		remove_filter( 'posts_where', 'ideapark_filter_dates_where', 10 );

		return $where;
	}
}

if ( ! function_exists( 'ideapark_is_favorites_list' ) ) {
	function ideapark_is_favorites_list() {
		return isset( $_REQUEST['favorites'] ) && is_archive() && ( $queried_object = get_queried_object() ) && ( $queried_object->name == 'catalog' );
	}
}

if ( ! function_exists( 'ideapark_mysql_dates_cond' ) ) {
	function ideapark_mysql_dates_cond() {
		return ideapark_mod( 'booking_type' ) == 'day' ?
			"( %s >= date_start AND %s <= date_end OR %s >= date_start AND %s <= date_end OR %s <= date_start AND %s >= date_end )"
			:
			"( %s > date_start AND %s < date_end OR %s > date_start AND %s < date_end OR %s <= date_start AND %s >= date_end )";
	}
}

if ( ! function_exists( 'ideapark_hide_rentals_category' ) ) {
	function ideapark_hide_rentals_category() {
		if ( ideapark_woocommerce_on() && ( $category_id = get_option( 'ideapark_category_default', false ) ) && ! is_admin() ) {

			$terms = get_terms( [
				'taxonomy' => 'product_cat',
				'parent'   => $category_id
			] );

			$category_ids = [ $category_id ];
			foreach ( $terms as $term ) {
				$category_ids[] = $term->term_id;
			}

			add_action( 'woocommerce_product_query', function ( $q ) use ( $category_ids ) {
				$tax_query   = (array) $q->get( 'tax_query' );
				$tax_query[] = [
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $category_ids,
					'operator' => 'NOT IN'
				];
				$q->set( 'tax_query', $tax_query );

				return $q;
			} );

			add_action( 'woocommerce_shortcode_products_query', function ( $q ) use ( $category_ids ) {
				if ( ! isset( $q['tax_query'] ) || ! is_array( $q['tax_query'] ) ) {
					$q['tax_query'] = [];
				}
				$q['tax_query'][] = [
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $category_ids,
					'operator' => 'NOT IN'
				];

				return $q;
			} );

			add_filter( 'get_terms_args', function ( $params ) use ( $category_ids ) {
				if ( ! is_admin() && $params['taxonomy'] == [ 'product_cat' ] ) {
					$params['exclude'] = implode( ',', $category_ids );
				}

				return $params;
			} );

			add_filter( 'get_the_terms', function ( $terms, $post_ID, $taxonomy ) use ( $category_ids ) {
				if ( is_product() && $taxonomy == "product_cat" ) {
					foreach ( $terms as $key => $term ) {
						if ( in_array( $term->term_id, $category_ids ) ) {
							unset( $terms[ $key ] );
						}
					}
				}

				return $terms;
			}, 11, 3 );
		}
	}

	add_action( 'wp_loaded', 'ideapark_hide_rentals_category', 99 );
}

if ( ! function_exists( 'ideapark_force_404' ) ) {
	function ideapark_force_404() {
		global $wp_query, $post;
		if ( function_exists( 'is_product' ) && is_product() && in_array( $post->ID, ideapark_get_all_default_products() ) ) {
			if ( ideapark_mod( 'wc_integration' ) == 'each' && ( $vehicle_id = ideapark_get_product_vehicle_id( $post->ID ) ) ) {
				wp_redirect( get_post_permalink( $vehicle_id ) );
			} else {
				$wp_query->set_404();
				status_header( 404 );
				nocache_headers();
				include( get_query_template( '404' ) );
			}
			die();
		}
	}

	add_action( 'wp', 'ideapark_force_404' );
}

if ( ! function_exists( 'ideapark_plugin_generator_tag' ) ) {
	function ideapark_plugin_generator_tag( $gen, $type ) {
		switch ( $type ) {
			case 'html':
				$gen .= "\n" . '<meta name="generator" content="Theme Plugin ' . IDEAPARK_ANTEK_FUNC_VERSION . '">';
				break;
			case 'xhtml':
				$gen .= "\n" . '<meta name="generator" content="Theme Plugin ' . IDEAPARK_ANTEK_FUNC_VERSION . '" />';
				break;
		}

		return $gen;
	}

	add_filter( 'get_the_generator_html', 'ideapark_plugin_generator_tag', 10, 2 );
	add_filter( 'get_the_generator_xhtml', 'ideapark_plugin_generator_tag', 10, 2 );
}

if ( ! function_exists( 'ideapark_elementor_post_type' ) ) {
	function ideapark_elementor_post_type() {
		$cpt_support = get_option( 'elementor_cpt_support' );
		if ( ! $cpt_support ) {
			$cpt_support = [ 'page', 'post', 'html_block' ];
			update_option( 'elementor_cpt_support', $cpt_support );
		} else if ( ! in_array( 'html_block', $cpt_support ) ) {
			$cpt_support[] = 'html_block';
			update_option( 'elementor_cpt_support', $cpt_support );
		}
	}

	add_action( 'after_setup_theme', 'ideapark_elementor_post_type' );
}

if ( ! function_exists( 'ideapark_details_clear_cache' ) ) {
	function ideapark_details_clear_cache() {
		delete_transient( 'ideapark_details_list' );
		delete_transient( 'ideapark_details_page' );
		if ( $languages = apply_filters( 'wpml_active_languages', [] ) ) {
			foreach ( $languages as $lang_code => $lang ) {
				delete_transient( 'ideapark_details_list_' . $lang_code );
				delete_transient( 'ideapark_details_page_' . $lang_code );
			}
		}
	}

	add_action( 'ideapark_after_import_finish', 'ideapark_details_clear_cache' );
}

if ( ! function_exists( 'ideapark_get_categories' ) ) {
	function ideapark_get_categories( $slug_as_index = false, $only_top_level = false ) {
		static $category_list_id = null, $category_list_slug = null;

		if ( $slug_as_index ) {
			if ( $category_list_slug !== null ) {
				return $category_list_slug;
			}
		} else {
			if ( $category_list_id !== null ) {
				return $category_list_id;
			}
		}

		$category_list_id   = [];
		$category_list_slug = [];
		if ( taxonomy_exists( 'vehicle_type' ) ) {

			$args = [
				'taxonomy'     => 'vehicle_type',
				'show_count'   => 0,
				'pad_counts'   => 0,
				'hierarchical' => 1,
				'title_li'     => '',
				'hide_empty'   => 0,
			];

			if ( $only_top_level ) {
				$args['parent'] = 0;
			}

			if ( $all_categories = get_categories( $args ) ) {
				$category_name   = [];
				$category_slug   = [];
				$category_parent = [];
				foreach ( $all_categories as $cat ) {
					$category_name[ $cat->term_id ]    = $cat->name;
					$category_slug[ $cat->term_id ]    = $cat->slug;
					$category_parent[ $cat->parent ][] = $cat->term_id;
				}

				$get_category = function ( $parent = 0, $prefix = '' ) use ( $slug_as_index, &$category_list_id, &$category_list_slug, &$category_parent, &$category_name, &$category_slug, &$get_category ) {
					if ( array_key_exists( $parent, $category_parent ) ) {
						$categories = $category_parent[ $parent ];
						foreach ( $categories as $category_id ) {
							$category_list_id[ $category_id ] = $category_list_slug[ $category_slug[ $category_id ] ] = $prefix . $category_name[ $category_id ];
							$get_category( $category_id, $prefix . ' - ' );
						}
					}
				};

				$get_category();
			}
		}

		return $slug_as_index ? $category_list_slug : $category_list_id;
	}
}

if ( ! function_exists( 'ideapark_get_favorites' ) ) {
	function ideapark_get_favorites() {
		static $result;
		if ( $result !== null ) {
			return $result;
		}
		if ( isset( $_COOKIE['ip-favorites'] ) ) {
			$result = json_decode( stripslashes( $_COOKIE['ip-favorites'] ), true );
		} else {
			$result = [];
		}

		return $result;
	}
}

if ( ! function_exists( 'ideapark_validate_time' ) ) {
	function ideapark_validate_time( $time, $type = 'pickup' ) {
		if ( ideapark_mod( 'pickup_dropoff_time' ) ) {
			$range          = explode( ',', ideapark_mod( ( $type == 'pickup' ) ? 'pickup_time_range' : 'dropoff_time_range' ) );
			$pickup_options = [];
			if ( sizeof( $range ) == 2 && $range[0] <= $range[1] ) {
				for ( $h = $range[0]; $h <= $range[1]; $h ++ ) {
					for ( $m = 0; $m < ( $h < $range[1] && ideapark_mod( 'time_increment' ) ? 60 : 0 ) || ( ! $m && $range[0] == $range[1] ); $m += ( ideapark_mod( 'time_increment' ) ? ideapark_mod( 'time_increment' ) : 60 ) ) {
						$pickup_options[] = sprintf( '%02d:%02d', $h, $m );
					}
				}
			}
			if ( in_array( $time, $pickup_options ) ) {
				return $time;
			} elseif ( ! empty( $pickup_options[0] ) ) {
				return $pickup_options[0];
			}
		}

		return '00:00';
	}
}

if ( ! function_exists( 'ideapark_time_format' ) ) {
	function ideapark_time_format( $time ) {
		$e = explode( ':', $time );
		if ( ideapark_mod( 'time_format' ) == '24h' ) {
			if ( $e[0] == 24 ) {
				$e[0] = '00';

				return implode( ':', $e );
			} else {
				return $time;
			}
		} else {
			if ( $e[0] == 0 || $e[0] == 24 ) {
				$e[0]   = '12';
				$suffix = 'AM';
			} elseif ( $e[0] < 12 ) {
				$e[0]   = (int) $e[0];
				$suffix = 'AM';
			} elseif ( $e[0] == 12 ) {
				$suffix = 'PM';
			} else {
				$e[0]   = (int) $e[0] - 12;
				$suffix = 'PM';
			}

			return implode( ':', $e ) . ' ' . $suffix;
		}
	}
}

if ( ! function_exists( 'ideapark_localize_time_vars' ) ) {
	function ideapark_localize_time_vars( &$return ) {
		$pickup_range  = explode( ',', ideapark_mod( 'pickup_time_range' ) );
		$dropoff_range = explode( ',', ideapark_mod( 'dropoff_time_range' ) );
		if ( sizeof( $pickup_range ) == 2 && sizeof( $dropoff_range ) == 2 && $pickup_range[0] <= $pickup_range[1] && $dropoff_range[0] <= $dropoff_range[1] ) {
			$pickup_options = '';
			for ( $h = $pickup_range[0]; $h <= $pickup_range[1]; $h ++ ) {
				for ( $m = 0; $m < ( $h < $pickup_range[1] && ideapark_mod( 'time_increment' ) ? 60 : 0 ) || ( ! $m && $pickup_range[0] == $pickup_range[1] ); $m += ( ideapark_mod( 'time_increment' ) ? ideapark_mod( 'time_increment' ) : 60 ) ) {
					$pickup_options .= sprintf( '<option value="%02d:%02d">%s</option>', $h, $m, ideapark_time_format( sprintf( '%02d:%02d', $h, $m ) ) );
				}
			}
			$dropoff_options = '';
			for ( $h = $dropoff_range[0]; $h <= $dropoff_range[1]; $h ++ ) {
				for ( $m = 0; $m < ( $h < $dropoff_range[1] && ideapark_mod( 'time_increment' ) ? 60 : 0 ) || ( ! $m && $dropoff_range[0] == $dropoff_range[1] ); $m += ( ideapark_mod( 'time_increment' ) ? ideapark_mod( 'time_increment' ) : 60 ) ) {
					$dropoff_options .= sprintf( '<option value="%02d:%02d">%s</option>', $h, $m, ideapark_time_format( sprintf( '%02d:%02d', $h, $m ) ) );
				}
			}
			$return = array_merge( $return, [
				'pickup_dropoff_time'  => ! ! ideapark_mod( 'pickup_dropoff_time' ),
				'pickup_time_options'  => $pickup_options,
				'dropoff_time_options' => $dropoff_options,
				'pickup_time_label'    => esc_html__( 'Pick-up time', 'ideapark-antek' ),
				'dropoff_time_label'   => esc_html__( 'Drop-off time', 'ideapark-antek' ),
				'time_format'          => ideapark_mod( 'time_format' ),
				'time_increment'       => (int) ideapark_mod( 'time_increment' ),
			] );
		}
	}
}

if ( ! function_exists( 'ideapark_saved_detail' ) ) {
	function ideapark_saved_detail( $term_id ) {
		global $wpdb;
		$term = get_term( $term_id );
		if ( $meta = get_term_meta( $term_id ) ) {
			if ( ! empty( $meta['is_numeric'][0] ) ) {
				if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}antek_filter WHERE field=%s", $term->slug ) ) ) {
					$sql = $wpdb->prepare( "REPLACE INTO {$wpdb->prefix}antek_filter (vehicle_id, field, value) 
SELECT p.ID, m.meta_key, CAST(m.meta_value AS DECIMAL(15,2))
FROM {$wpdb->posts} p
INNER JOIN {$wpdb->postmeta} m ON m.meta_key = %s AND m.post_id = p.ID
WHERE p.post_type='catalog' && m.meta_value IS NOT NULL AND m.meta_value != ''", $term->slug );
					$wpdb->query( $sql );
				}
			} else {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}antek_filter WHERE field=%s", $term->slug ) );
			}
		}
	}

	add_action( 'saved_detail', 'ideapark_saved_detail' );
}

if ( ! function_exists( 'ideapark_price_type_changed' ) ) {
	function ideapark_price_type_changed( $settings ) {
		/**
		 * @var WP_Customize_Manager $settings
		 */

		$changed = $settings->changeset_data();
		foreach ( $changed as $key => $val ) {
			if ( preg_match( '~price_type$~', $key ) ) {
				$value = $val['value'];
				ideapark_mod_set_temp( 'price_type', $value );
				$args = [
					'numberposts'      => - 1,
					'post_type'        => 'catalog',
					'suppress_filters' => true,
				];

				$posts = get_posts( $args );

				foreach ( $posts as $post ) {
					ideapark_save_vehicle( $post->ID );
				}
				break;
			}
		}
	}

	add_action( "customize_save_after", 'ideapark_price_type_changed' );
}

if ( ! function_exists( 'ideapark_deliveries' ) ) {
	function ideapark_deliveries( $vehicle_id ) {
		global $wpdb;

		$result = [];
		$query  = $wpdb->get_results( $wpdb->prepare( "SELECT location_id, price FROM {$wpdb->prefix}antek_delivery WHERE vehicle_id=%d", $vehicle_id ) );
		foreach ( $query as $row ) {
			$result[ $row->location_id ] = $row->price;
		}

		return $result;
	}
}

if ( ! function_exists( 'ideapark_get_delivery_price' ) ) {
	function ideapark_get_delivery_price( $vehicle_id, $location_id = 0, $check_availability = false ) {
		global $wpdb;

		$inner_sql = "(" . $wpdb->prepare( "SELECT price FROM {$wpdb->prefix}antek_delivery WHERE vehicle_id=%d AND location_id=%d", $vehicle_id, 0 ) . ")";

		if ( $location_id ) {
			$inner_sql = "(" . $wpdb->prepare( "SELECT price FROM {$wpdb->prefix}antek_delivery WHERE vehicle_id=%d AND location_id=%d", $vehicle_id, $location_id ) . "), " . $inner_sql;
		} else {
			$inner_sql = $inner_sql . ',' . "(SELECT MIN(price) FROM {$wpdb->prefix}antek_delivery WHERE vehicle_id={$vehicle_id})";
		}

		if ( $check_availability ) {
			return $wpdb->get_var( "SELECT COALESCE($inner_sql)" );
		} else {
			return $wpdb->get_var( "SELECT COALESCE($inner_sql,0)" );
		}
	}
}

if ( ! function_exists( 'ideapark_get_cookie_params' ) ) {
	function ideapark_get_cookie_params() {
		static $params;
		if ( empty( $params ) ) {
			$params['sort']     = ! empty( $_COOKIE[ 'sort_' . COOKIEHASH ] ) ? $_COOKIE[ 'sort_' . COOKIEHASH ] : ideapark_mod( 'catalog_order' );
			$sort_options_index = array_keys( array_filter( ideapark_parse_checklist( ideapark_mod( 'catalog_order_list' ) ) ) );
			if ( ! in_array( $params['sort'], $sort_options_index ) ) {
				$params['sort'] = ideapark_mod( 'catalog_order' );
			}

			if ( $params['reset_page'] = ! empty( $_COOKIE[ 'reset_page_' . COOKIEHASH ] ) ) {
				setcookie( 'reset_page_' . COOKIEHASH, '', time() - 3600 * 24, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN );
			}
		}

		return $params;
	}
}

if ( ! function_exists( 'ideapark_base_changed' ) ) {
	function ideapark_base_changed( $setting ) {
		flush_rewrite_rules();
		wp_schedule_single_event( time(), 'woocommerce_flush_rewrite_rules' );
	}

	add_action( "customize_save_catalog_base", 'ideapark_base_changed' );
	add_action( "customize_save_type_base", 'ideapark_base_changed' );
}

if ( ! function_exists( 'ideapark_unlimited_booking_changed' ) ) {
	function ideapark_unlimited_booking_changed( $setting ) {
		/*if ( $setting->post_value() ) {
		}*/
		ideapark_clear_stock();
		ideapark_recalculate_stock();
	}

	add_action( "customize_save_unlimited_booking", 'ideapark_unlimited_booking_changed' );
}

if ( ! function_exists( 'ideapark_catalog_title' ) ) {
	function ideapark_catalog_title( $title ) {
		if ( is_post_type_archive( 'catalog' ) ) {
			$title = ideapark_mod( 'catalog_list_header' ) . ' - ' . get_bloginfo( 'name' );
		}

		return $title;
	}

	add_filter( 'pre_get_document_title', 'ideapark_catalog_title', 99 );
}

if ( ! function_exists( 'ideapark_disable_payment' ) ) {
	function ideapark_disable_payment() {
		if ( ! function_exists( 'ideapark_mod' ) ) {
			return;
		}

		if ( ideapark_mod( 'disable_payment' ) ) {
			add_filter( 'woocommerce_cart_needs_payment', '__return_false' );
		}
	}

	add_action( 'wp_loaded', 'ideapark_disable_payment', 99 );
}

if ( ! function_exists( 'ideapark_woocommerce_on' ) ) {
	function ideapark_woocommerce_on() {
		return class_exists( 'WooCommerce' ) ? 1 : 0;
	}
}

if ( ! function_exists( 'ideapark_fix_woocommerce_activation' ) ) {
	function ideapark_fix_woocommerce_activation() {
		if ( is_admin() && wp_doing_ajax() && ! empty( $_POST['action'] ) && $_POST['action'] == 'ideapark_about_ajax' && empty( $_POST['is_additional'] ) && empty( $_POST['is_core_update'] ) && empty( $_POST['plugins'] ) ) {

			if ( ! defined( 'DOING_CRON' ) ) {
				define( 'DOING_CRON', true );
			}
		}
	}

	add_action( 'plugins_loaded', 'ideapark_fix_woocommerce_activation', 1 );
}

if ( ! function_exists( 'ideapark_fix_condition_data' ) ) {
	function ideapark_fix_condition_data( $value, $field, $old_value, $object_id ) {
		if ( in_array( $field['id'], [
				'weekdays',
				'location',
				'type',
				'years',
				'months',
				'days'
			] ) && $field['type'] == 'checkbox_list' ) {
			if ( sizeof( $field['options'] ) == sizeof( $value ) ) {
				$value = [];
			}
		}

		if ( in_array( $field['id'], [
				'location',
				'type',
			] ) && $field['type'] == 'taxonomy_advanced' ) {
			$terms      = get_terms( [
				'taxonomy'   => $field['id'] == 'location' ? 'location' : 'vehicle_type',
				'hide_empty' => false,
			] );
			$all_values = [];
			foreach ( $terms as $term ) {
				$all_values[] = $term->term_id;
			}
			if ( sizeof( $all_values ) == sizeof( explode( ',', $value ) ) ) {
				$value = '';
			}
		}

		return $value;
	}

	add_filter( 'rwmb_value', 'ideapark_fix_condition_data', 10, 4 );
}

if ( ! function_exists( 'ideapark_get_condition_fields' ) ) {
	function ideapark_get_condition_fields() {
		return [
			'days_from',
			'days_to',
			'from_date',
			'to_date',
			'days_first',
			'fixed_price',
			'weekdays',
			'days',
			'months',
			'years',
			'location',
			'type',
		];
	}
}

if ( ! function_exists( 'ideapark_condition_validate' ) ) {
	function ideapark_condition_validate( $term_id = 0 ) {
		$condition_fields = ideapark_get_condition_fields();

		if ( ! $term_id ) {
			return;
		}

		$meta = get_term_meta( $term_id );
		if ( ! empty( $meta ) ) {
			$meta = array_filter( $meta, function ( $key ) use ( $condition_fields ) {
				return in_array( $key, $condition_fields );
			}, ARRAY_FILTER_USE_KEY );
			ksort( $meta );
			$hash                = md5( serialize( $meta ) );
			$duplicate_term_name = '';

			$terms = get_terms( [
				'taxonomy'   => 'condition',
				'hide_empty' => false,
			] );

			foreach ( $terms as $term ) {
				if ( $term->term_id != $term_id ) {
					$meta = get_term_meta( $term->term_id );
					$meta = array_filter( $meta, function ( $key ) use ( $condition_fields ) {
						return in_array( $key, $condition_fields );
					}, ARRAY_FILTER_USE_KEY );
					ksort( $meta );
					$hash_term = md5( serialize( $meta ) );
					if ( $hash == $hash_term ) {
						$duplicate_term_name = '<a href="' . esc_url( get_edit_term_link( $term->term_id, 'condition' ) ) . '">' . $term->name . '</a>';
						break;
					}
				}
			}

			if ( $duplicate_term_name ) {
				ideapark_add_admin_notice(
					sprintf(
						esc_html__( 'The database already has the same condition "%s". Change the condition parameters, or delete the duplicate.', 'ideapark-antek' ),
						$duplicate_term_name
					), 'error' );
			}
		}
	}

	add_action( 'saved_condition', 'ideapark_condition_validate', 999, 1 );
}

if ( ! function_exists( 'ideapark_insert_condition_validate' ) ) {
	function ideapark_insert_condition_validate( $term_name, $taxonomy ) {
		if ( $taxonomy == 'condition' ) {
			$condition_fields = ideapark_get_condition_fields();
			$meta             = [];

			foreach ( $condition_fields as $condition_field ) {
				if ( ! empty( $_POST[ $condition_field ] ) ) {
					$val                      = $_POST[ $condition_field ];
					$meta[ $condition_field ] = ( is_array( $val ) ) ? $val : [ (string) $val ];
				}
			}

			ksort( $meta );
			$hash  = md5( serialize( $meta ) );
			$terms = get_terms( [
				'taxonomy'   => 'condition',
				'hide_empty' => false,
			] );

			foreach ( $terms as $term ) {
				$meta = get_term_meta( $term->term_id );
				$meta = array_filter( $meta, function ( $key ) use ( $condition_fields ) {
					return in_array( $key, $condition_fields );
				}, ARRAY_FILTER_USE_KEY );
				ksort( $meta );
				$hash_term = md5( serialize( $meta ) );
				if ( $hash == $hash_term ) {
					$duplicate_term_name = '<a href="' . esc_url( get_edit_term_link( $term->term_id, 'condition' ) ) . '">' . $term->name . '</a>';

					return new WP_Error( 'could_not_add', sprintf(
						esc_html__( 'The database already has the same condition "%s". Change the condition parameters, or delete the duplicate.', 'ideapark-antek' ),
						$duplicate_term_name
					) );
				}
			}
		}

		return $term_name;
	}

	add_filter( 'pre_insert_term', 'ideapark_insert_condition_validate', 10, 2 );
}

if ( ! function_exists( 'ideapark_add_admin_notice' ) ) {
	function ideapark_add_admin_notice( $message, $type = 'info' /* info | success  | warning | error */ ) {

		$notices = get_transient( 'ideapark_admin_notices' );
		if ( ! is_array( $notices ) ) {
			$notices = [];
		}

		$notices[] = [
			'type'    => $type,
			'message' => $message
		];

		set_transient( 'ideapark_admin_notices', $notices );
	}
}

if ( ! function_exists( 'ideapark_show_admin_notices' ) ) {
	function ideapark_show_admin_notices() {
		if ( $notices = get_transient( 'ideapark_admin_notices' ) ) {
			foreach ( $notices as $notice ) {
				echo ideapark_wrap( $notice['message'], '<div class="notice notice-' . esc_attr( $notice['type'] ) . '"><p>', '</p></div>' );
			}
			delete_transient( 'ideapark_admin_notices' );
		}
	}

	add_action( 'admin_notices', 'ideapark_show_admin_notices' );
}

if ( ! function_exists( 'ideapark_customize_loaded_components' ) ) {
	function ideapark_customize_loaded_components( $components ) {

		foreach ( [ 'widgets' ] as $key ) {
			$i = array_search( $key, $components );
			if ( false !== $i ) {
				unset( $components[ $i ] );
			}
		}

		return $components;
	}

	add_filter( 'customize_loaded_components', 'ideapark_customize_loaded_components' );
}

if ( ! function_exists( 'ideapark_get_catalog_subcategories' ) ) {
	function ideapark_get_catalog_subcategories( $parent_id = 0 ) {
		$parent_id          = absint( $parent_id );
		$product_categories = get_categories(
			[
				'parent'       => $parent_id,
				'hide_empty'   => 1,
				'hierarchical' => 1,
				'taxonomy'     => 'vehicle_type',
				'pad_counts'   => 1,
			]
		);

		return $product_categories;
	}
}

if ( ! function_exists( 'ideapark_catalog_categories' ) ) {
	function ideapark_catalog_categories( $_parent_id = null ) {
		if ( is_post_type_archive( 'catalog' ) || is_tax( 'vehicle_type' ) || $_parent_id !== null ) {
			$is_parent  = false;
			$count      = 0;
			$current_id = is_tax( 'vehicle_type' ) ? get_queried_object_id() : 0;
			$parent_id  = $_parent_id ?: $current_id;
			$loop_html  = '';
			do {
				$product_categories = ideapark_get_catalog_subcategories( $parent_id );

				if ( $product_categories ) {
					foreach ( $product_categories as $category ) {
						$term = get_term( $category );
						if ( $term && ! is_wp_error( $term ) ) {
							$title = $term->name;
							$link  = apply_filters( 'ideapark_catalog_subcat_link', get_term_link( (int) $term->term_id, 'vehicle_type' ) );
							$meta  = get_term_meta( $term->term_id );
							$image = '';
							if ( ideapark_mod( 'catalog_categories_layout' ) == 'image' && ! empty( $meta['image'][0] ) && ( $type = get_post_mime_type( $meta['image'][0] ) ) ) {
								if ( $type == 'image/svg+xml' ) {
									$image = ideapark_get_inline_svg( $meta['image'][0], 'c-subcat__thumb c-subcat__thumb--svg' );
								} else {
									if ( $image_meta = ideapark_image_meta( $meta['image'][0], 'medium' ) ) {
										$image = ideapark_img( $image_meta, 'c-subcat__thumb c-subcat__thumb--image' );
									}
								}
								$image .= '<span class="c-subcat__overlay"></span><i class="ip-plus c-subcat__plus"></i>';
							} elseif ( ideapark_mod( 'catalog_categories_layout' ) == 'icon' && ! empty( $meta['font-icon'][0] ) ) {
								$image = '<i class="c-subcat__icon ' . esc_attr( $meta['font-icon'][0] ) . '" aria-hidden="true"></i>';
							}

							if ( $title ) {
								$loop_html .= '<div class="c-subcat__item' . ( $current_id == $term->term_id ? ' c-subcat__item--current' : '' ) . '"><a href="' . esc_url( $link ) . '"><span class="c-subcat__thumb-wrap">' . ideapark_wrap( $image ) . '</span><h2 class="c-subcat__title">' . esc_html( $title ) . '</h2></a></div>';
								$count ++;
							}
						}
					}
				}

				if ( ! $loop_html ) {
					if ( $is_parent ) {
						break;
					} elseif ( $parent_id ) {
						$parent_id = get_queried_object()->parent;
						$is_parent = true;
					} else {
						break;
					}
				} elseif ( $parent_id && ! $_parent_id ) {
					$term_id = get_queried_object()->parent;
					$title   = '';
					if ( $term_id ) {
						$term = get_term( $term_id );
						if ( $term && ! is_wp_error( $term ) ) {
							$title = $term->name;
							$link  = get_term_link( (int) $term->term_id );
						}
					} else {
						$title = ideapark_mod( 'catalog_list_header' );
						$link  = get_post_type_archive_link( 'catalog' );
					}
					if ( $title ) {
						$link      = apply_filters( 'ideapark_catalog_subcat_link', $link );
						$loop_html = '<div class="c-subcat__item"><a href="' . esc_url( $link ) . '"><span class="c-subcat__thumb-wrap c-subcat__thumb-wrap--back"><i class="ip-long-arrow c-subcat__back"></i></span><h2 class="c-subcat__title">' . esc_html( $title ) . '</h2></a></div>' . $loop_html;
						$count ++;
					}
				}
			} while ( ! $loop_html );

			$loop_html = apply_filters( 'ideapark_catalog_subcat', $loop_html );

			echo ideapark_wrap( $loop_html, '<div class="c-subcat c-subcat--' . esc_attr( ideapark_mod( 'catalog_categories' ) ) . ' c-subcat--' . esc_attr( ideapark_mod( 'catalog_categories_layout' ) ) . '"><div class="c-subcat__list ' . ( $count > 6 ? ' c-subcat__list--carousel ' : '' ) . ' js-header-subcat h-carousel h-carousel--dots-hide h-carousel--flex">', '</div></div>' );

			ideapark_mod_set_temp( '_with_header_subcat', ! ! $loop_html );

			return ! ! $loop_html;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'ideapark_is_network_activated' ) ) {
	function ideapark_is_network_activated() {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		return is_multisite() && is_plugin_active_for_network( 'ideapark-antek/ideapark-antek.php' );
	}
}

if ( ! function_exists( 'ideapark_theme_notice' ) ) {
	function ideapark_theme_notice() {

		$screen = get_current_screen();
		if ( in_array( $screen->id, [ 'appearance_page_ideapark_about' ], true ) ) {
			return;
		}

		if ( ( $code = ideapark_get_purchase_code() ) && ( $code !== IDEAPARK_SKIP_REGISTER ) ) {
			return;
		}

		$message          = __( 'You have not registered the theme yet! Please <a href="%1$s">enter your purchase code</a> or <a href="%2$s" target="_blank">get a new license here</a>.', 'ideapark-antek' );
		$theme_about_page = admin_url( 'themes.php?page=ideapark_about' );

		echo '<div id="ideapark-notification" class="notice notice-warning is-dismissible"><p><span class="dashicons dashicons-warning" style="color: #f56e28"></span> ', ideapark_wp_kses( sprintf( $message, $theme_about_page, preg_replace( '~#.*$~', '', IDEAPARK_CHANGELOG ) ) ), '</p></div>';

	}

	$admin_notices_hook = ideapark_is_network_activated() ? 'network_admin_notices' : 'admin_notices';
	add_action( $admin_notices_hook, 'ideapark_theme_notice' );
}

if ( ! function_exists( 'ideapark_get_delivered_product_ids' ) ) {
	function ideapark_get_delivered_product_ids() {
		static $result_product_ids;

		if ( $result_product_ids !== null ) {
			return $result_product_ids;
		}

		$product_ids        = ideapark_get_all_default_products();
		$result_product_ids = [];

		if ( isset( WC()->cart ) && WC()->cart->get_cart_contents_count() && $product_ids ) {
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( in_array( $cart_item['product_id'], $product_ids ) && ! empty( $cart_item['ideapark_antek'] ) && $cart_item['ideapark_antek']['delivery'] !== '-' ) {
					$result_product_ids[] = $cart_item['product_id'];
				}
			}
		}

		return $result_product_ids;
	}
}

if ( ! function_exists( 'ideapark_show_shipping_form' ) ) {
	function ideapark_show_shipping_form( $show ) {
		if ( ! $show && ideapark_mod( 'show_shipping_form' ) ) {
			if ( wc_ship_to_billing_address_only() || ! wc_shipping_enabled() ) {
				return false;
			}

			$needs_shipping = false;

			if ( ideapark_get_delivered_product_ids() ) {
				$needs_shipping = true;
			}

			$show = $needs_shipping;
		}

		return $show;
	}

	add_filter( 'woocommerce_cart_needs_shipping_address', 'ideapark_show_shipping_form' );
}


if ( ! function_exists( 'ideapark_show_shipping_methods' ) ) {
	function ideapark_show_shipping_methods( $is_needs_shipping, $product ) {
		if ( ! $is_needs_shipping && ideapark_mod( 'show_shipping_methods' ) ) {
			/**
			 * @var $product WC_Product
			 */
			if ( in_array( $product->get_id(), ideapark_get_delivered_product_ids() ) ) {
				return true;
			}
		}

		return $is_needs_shipping;
	}

	add_filter( 'woocommerce_product_needs_shipping', 'ideapark_show_shipping_methods', 10, 2 );
}

if ( ! function_exists( 'ideapark_wpml_id' ) ) {
	function ideapark_wpml_orig_id( $id, $element_type ) {
		return apply_filters( 'wpml_object_id', absint( $id ), $element_type, true, apply_filters( 'wpml_default_language', null ) );
	}
}

if ( ! function_exists( 'ideapark_get_page_by_title' ) ) {
	function ideapark_get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {
		global $wpdb;

		if ( is_array( $post_type ) ) {
			$post_type           = esc_sql( $post_type );
			$post_type_in_string = "'" . implode( "','", $post_type ) . "'";
			$sql                 = $wpdb->prepare(
				"
			SELECT ID
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type IN ($post_type_in_string)
		",
				$page_title
			);
		} else {
			$sql = $wpdb->prepare(
				"
			SELECT ID
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type = %s
		",
				$page_title,
				$post_type
			);
		}

		$page = $wpdb->get_var( $sql );

		if ( $page ) {
			return get_post( $page, $output );
		}

		return null;
	}
}

add_action( 'admin_init', function () {
	global $rs_admin;
	if ( function_exists( 'ideapark_ra' ) && isset( $rs_admin ) ) {
		ideapark_ra( 'admin_notices', [ $rs_admin, 'add_plugins_page_notices' ] );
	}
} );

add_shortcode( 'ip-button', function ( $atts ) {

	$default_atts = [
		'size'           => 'medium', // small, medium, large
		'type'           => 'primary', // primary, accent, outline, outline-white,  outline-black
		'icon'           => '',
		'text'           => '',
		'link'           => '',
		'href'           => '',
		'target'         => '_self',
		'text_transform' => '',
		'margin'         => '',
		'custom_class'   => '',
		'html_type'      => 'anchor', // anchor, button
	];

	$params = shortcode_atts( $default_atts, $atts );

	$styles = [];

	if ( ! empty( $params['text_transform'] ) ) {
		$styles[] = 'text-transform: ' . $params['text_transform'];
	}

	if ( $params['margin'] !== '' ) {
		$styles[] = 'margin: ' . $params['margin'];
	}

	ob_start();
	?>
	<?php if ( $params['type'] == 'button' ) { ?>
		<button type="button"
		        class="c-button c-button--<?php echo esc_html( $params['type'] ); ?> c-button--<?php echo esc_html( $params['size'] ); ?> <?php if ( $params['custom_class'] ) {
			        esc_attr( $params['custom_class'] );
		        } ?>" <?php if ( $styles ) { ?>style="<?php echo esc_attr( implode( ';', $styles ) ); ?>"<?php } ?>>
			<?php if ( $params['icon'] ) { ?>
				<i class="c-button__icon <?php echo esc_attr( $params['icon'] ); ?>"><!-- --></i>
			<?php } ?>
			<span class="c-button__text"><?php echo esc_html( $params['text'] ); ?></span>
		</button>
	<?php } else { ?>
		<a href="<?php echo esc_url( $params['href'] ? $params['href'] : $params['link'] ); ?>"
		   target="<?php echo esc_attr( $params['target'] ); ?>"
		   class="c-button c-button--<?php echo esc_html( $params['type'] ); ?> c-button--<?php echo esc_html( $params['size'] ); ?> <?php if ( $params['custom_class'] ) {
			   esc_attr( $params['custom_class'] );
		   } ?>" <?php if ( $styles ) { ?>style="<?php echo esc_attr( implode( ';', $styles ) ); ?>"<?php } ?>>
			<?php if ( $params['icon'] ) { ?>
				<i class="c-button__icon <?php echo esc_attr( $params['icon'] ); ?>"><!-- --></i>
			<?php } ?>
			<span class="c-button__text"><?php echo esc_html( $params['text'] ); ?></span>
		</a>
	<?php } ?>
	<?php

	return preg_replace( '~[\r\n]~', '', ob_get_clean() );
} );

add_shortcode( 'ip-post-share', function ( $atts ) {

	global $post;

	$esc_permalink = esc_url( get_permalink() );
	$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), false, '' );

	$share_links = [
		'<a target="_blank" href="//www.facebook.com/sharer.php?u=' . $esc_permalink . '" title="' . esc_html__( 'Share on Facebook', 'ideapark-antek' ) . '"><i class="ip-facebook c-post__share-svg c-post__share-svg--facebook"></i></a>',
		'<a target="_blank" href="//twitter.com/share?url=' . $esc_permalink . '" title="' . esc_html__( 'Share on Twitter', 'ideapark-antek' ) . '"><i class="ip-twitter c-post__share-svg c-post__share-svg--twitter"></i></a>',
		'<a target="_blank" href="//pinterest.com/pin/create/button/?url=' . $esc_permalink . ( $product_image ? '&amp;media=' . esc_url( $product_image[0] ) : '' ) . '&amp;description=' . urlencode( get_the_title() ) . '" title="' . esc_html__( 'Pin on Pinterest', 'ideapark-antek' ) . '"><i class="ip-pinterest c-post__share-svg c-post__share-svg--pinterest"></i></a>',
		'<a target="_blank" href="//wa.me/?text=' . $esc_permalink . '" title="' . esc_html__( 'Share on Whatsapp', 'ideapark-antek' ) . '"><i class="ip-whatsapp c-post__share-svg c-post__share-svg--whatsapp"></i></a>'
	];

	ob_start();
	?>

	<?php
	foreach ( $share_links as $link ) {
		echo ideapark_wrap( $link );
	}
	?>
	<?php

	$content = ob_get_clean();

	return $content;
} );