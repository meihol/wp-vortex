<?php get_header(); ?>
<?php get_template_part( 'templates/page-header' ); ?>

<?php global $post;
$with_sidebar = ! is_singular( 'product' ) && ideapark_mod( 'shop_sidebar' ) && is_active_sidebar( 'shop-sidebar' ) && ! ( ideapark_woocommerce_on() && ( is_cart() || is_checkout() || is_account_page() ) );
global $ideapark_category_html_top, $ideapark_category_html_bottom, $ideapark_category_html_top_above;
?>

<?php if ( $ideapark_category_html_top_above ) { ?>
	<?php echo ideapark_wrap( $ideapark_category_html_top, '<div class="c-category-html c-category-html--top">', '</div>' ); ?>
<?php } ?>

<div
	class="c-shop l-section l-section--container <?php ideapark_class( is_singular( 'product' ), 'l-section--margin-120', 'l-section--margin-80' ); ?><?php if ( $with_sidebar ) { ?> l-section--with-sidebar <?php if (ideapark_mod( 'product_sidebar_3_cols' )) { ?> l-section--wide<?php } ?><?php } ?>">
	<?php if ( $with_sidebar ) { ?>
		<div class="c-catalog__filter-show-button">
			<button class="h-cb c-button c-button--outline c-button--full c-button--filter c-button--catalog-vert" id="js-sidebar-button" type="button"><i
					class="ip-filter c-catalog__filter-ico"></i><?php esc_html_e( 'Filter', 'antek' ); ?></button>
		</div>
	<?php } ?>
	<?php if ( $with_sidebar ) { ?>
		<div class="l-section__sidebar l-section__sidebar--left l-section__sidebar--popup">
			<?php get_sidebar( 'woocommerce' ); ?>
		</div>
	<?php } ?>
	<div class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php } ?>">
		<?php if ( ! $ideapark_category_html_top_above ) { ?>
			<?php echo ideapark_wrap( $ideapark_category_html_top, '<div class="c-category-html c-category-html--top">', '</div>' ); ?>
		<?php } ?>
		<div
			class="c-woocommerce <?php ideapark_class( ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar-nearby' ); ?>">
			<?php woocommerce_content(); ?>
		</div><!-- /.c-woocommerce -->
		<?php echo ideapark_wrap( $ideapark_category_html_bottom, '<div class="c-category-html c-category-html--bottom">' , '</div>' ); ?>
	</div><!-- /.c-woocommerce -->
</div>

<?php get_footer(); ?>