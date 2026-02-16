<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head><script></script>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
	<meta name="format-detection" content="telephone=no"/>
	<link rel="profile" href="//gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'templates/header-search' ); ?>

<?php if ( ideapark_mod( 'store_notice' ) == 'top' && function_exists( 'woocommerce_demo_store' ) ) {
	woocommerce_demo_store();
	ideapark_ra( 'wp_footer', 'woocommerce_demo_store' );
} ?>
<?php
ob_start();
$mobile_header_buttons_cnt = 0;
if ( ! ideapark_mod( 'search_disabled_mobile' ) ) {
	get_template_part( 'templates/header-mobile-search-button' );
	$mobile_header_buttons_cnt ++;
}
if ( ! ideapark_mod( 'auth_disabled_mobile' ) ) {
	get_template_part( 'templates/header-mobile-auth' );
	$mobile_header_buttons_cnt ++;
}
if ( ! ideapark_mod( 'cart_disabled_mobile' ) ) {
	get_template_part( 'templates/header-mobile-cart' );
	$mobile_header_buttons_cnt ++;
}
$mobile_header_buttons = trim( ob_get_clean() );
global $ideapark_advert_bar;
?>
<div class="l-wrap">
	<?php if ( $ideapark_advert_bar ) {
		echo ideapark_wrap( $ideapark_advert_bar, '<div class="l-section"><div class="c-header__advert_bar c-header__advert_bar--above">', '</div></div>' );
	} ?>

	<?php if ( ideapark_woocommerce_on() && ideapark_mod( 'popup_cart_layout' ) && !ideapark_mod( 'disable_booking' )) { ?>
		<div class="c-cart-sidebar" id="js-cart-sidebar">
			<div class="c-cart-sidebar__wrap" id="js-cart-sidebar-wrap">
				<div class="c-cart-sidebar__buttons">
					<button type="button" class="h-cb c-cart-sidebar__close"
							id="js-cart-sidebar-close"><i class="ip-close"><!-- --></i></button>
				</div>
				<div class="c-sidebar c-cart-sidebar__content" id="js-cart-sidebar-content">
					<div class="widget_shopping_cart_content"></div>
				</div>
			</div>
		</div>
	<?php } ?>

	<header class="l-section" id="main-header">

		<div
			class="c-header__outer c-header__outer--mobile" id="js-header-outer-mobile">
			<div
				class="c-header c-header--buttons-<?php echo esc_attr( $mobile_header_buttons_cnt ); ?> c-header--mobile" id="js-header-mobile">
				<div class="c-header__row <?php if ( ideapark_mod( 'header_logo_centered_mobile' ) ) { ?>c-header__row--logo-centered<?php } else { ?>c-header__row--logo-left<?php } ?>">
					<?php get_template_part( 'templates/header-mobile-menu-button' ); ?>
					<?php get_template_part( 'templates/header-logo-mobile' ); ?>
					<?php echo ideapark_wrap( $mobile_header_buttons, '<div class="c-header__col-mobile-buttons c-header__col-mobile-buttons--' . esc_attr( $mobile_header_buttons_cnt ) . '">', '</div>' ) ?>
				</div>
			</div>
		</div>

		<div
			class="c-header__menu c-header--mobile" id="js-mobile-menu">
			<div class="c-header__menu-shadow"></div>
			<div class="c-header__menu-buttons">
				<button type="button" class="h-cb h-cb--svg c-header__menu-back" id="js-mobile-menu-back"><i
						class="ip-menu-left c-header__menu-back-svg"></i><?php esc_html_e( 'Back', 'antek' ) ?>
				</button>
				<button type="button" class="h-cb h-cb--svg c-header__menu-close" id="js-mobile-menu-close"><i
						class="ip-close c-header__menu-close-svg"></i></button>
			</div>
			<div class="c-header__menu-content">
				<div class="c-header__menu-wrap" id="js-mobile-menu-wrap"></div>
				<?php get_template_part( 'templates/header-mobile-top-menu' ); ?>
				<?php get_template_part( 'templates/header-mobile-blocks' ); ?>
			</div>
			<div class="c-header__menu-bottom">
				<?php get_template_part( 'templates/header-mobile-search-button' ); ?>
				<?php get_template_part( 'templates/header-mobile-auth' ); ?>
				<?php get_template_part( 'templates/header-mobile-cart' ); ?>
			</div>
		</div>

		<div
			class="c-header c-header--desktop  c-header--<?php echo ideapark_mod( 'header_type' ); ?>">
			<?php if ( ideapark_mod( 'header_type' ) == 'header-type-1' ) { ?>
				<?php get_template_part( 'templates/header-top-row' ); ?>
				<div
					class="c-header__outer c-header__outer--desktop" id="js-header-outer-desktop">
					<div class="c-header__main-row" id="js-header-desktop">
						<?php get_template_part( 'templates/header-logo' ); ?>
						<?php get_template_part( 'templates/header-support' ); ?>
						<?php ideapark_get_template_part( 'templates/header-top-menu', [ 'device' => 'desktop' ] ); ?>
						<?php get_template_part( 'templates/header-search-button' ); ?>
						<?php get_template_part( 'templates/header-cart' ); ?>
					</div>
				</div>
			<?php } ?>
		</div>

	</header>
	<div class="l-inner">