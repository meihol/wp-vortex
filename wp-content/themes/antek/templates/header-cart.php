<?php if ( ideapark_mod( 'header_cart' ) && ideapark_woocommerce_on() ) { ?>
	<div class="c-header__main-row-item c-header__main-row-item--cart">
		<a class="c-header__cart <?php if ( ideapark_mod( 'popup_cart_layout' ) ) { ?> js-cart<?php } ?>" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<i class="<?php echo ideapark_mod( 'custom_header_icon_cart' ) ?: 'ip-cart'; ?>  c-header__cart-icon"><!-- --></i>
			<?php echo ideapark_cart_info(); ?>
		</a>
	</div>
<?php } ?>