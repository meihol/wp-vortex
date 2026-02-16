<?php get_header(); ?>
<?php get_template_part( 'templates/page-header' ); ?>
<?php
global $wp_query, $ideapark_category_html_top, $ideapark_category_html_bottom, $ideapark_category_html_top_above;

$is_favorites_list = isset( $_REQUEST['favorites'] );
$with_sidebar      = ideapark_mod( 'sidebar_catalog' ) && is_active_sidebar( 'catalog-sidebar' ) && ! $is_favorites_list && ! is_search();

$layout            = ideapark_mod( 'catalog_layout' );
$with_subcat       = ideapark_mod( 'catalog_categories' ) == 'content' && ideapark_core_plugin_on();
$params            = ideapark_get_cookie_params();
$sort_options      = ideapark_parse_checklist( ideapark_mod( 'catalog_order_list' ) );
$sort_options_html = [];

foreach ( $sort_options as $index => $enabled ) {
	if ( $enabled ) {
		$name = '';
		switch ( $index ) {
			case 'newest':
				$name = __( 'Newest first', 'antek' );
				break;
			case 'low_price':
				$name = __( 'Low price first', 'antek' );
				break;
			case 'high_price':
				$name = __( 'High price first', 'antek' );
				break;
			case 'menu_order':
				$name = __( 'Default sorting', 'antek' );
				break;
		}
		if ( $name ) {
			ob_start(); ?>
			<option
				value="<?php echo esc_attr( $index ); ?>" <?php selected( $index, $params['sort'] ); ?>><?php echo esc_html( $name ); ?></option>
			<?php
			$sort_options_html[] = ob_get_clean();
		}
	}
}

$with_block_above = false;

if ( $with_subcat ) { ?>
	<?php $with_block_above |= ideapark_catalog_categories(); ?>
<?php }

if ( $ideapark_category_html_top_above && $ideapark_category_html_top ) {
	echo ideapark_wrap( $ideapark_category_html_top, '<div class="c-html-block c-html-block--top">', '</div>' );
	$with_block_above |= true;
} ?>
	<div
		class="c-catalog
		c-catalog--<?php echo ideapark_mod( 'catalog_layout' ); ?>
		<?php ideapark_class( $with_block_above, 'c-catalog--block-above' ); ?>
		<?php ideapark_class( $ideapark_category_html_bottom, 'c-catalog--block-bottom' ); ?>
		l-section
		<?php ideapark_class( $layout == 'vert', 'l-section--container', 'l-section--container-wide' ); ?>
		<?php ideapark_class( $with_sidebar, 'l-section--with-sidebar', 'l-section--no-sidebar' ); ?>">
		<?php if ( $with_sidebar ) { ?>
			<div class="c-catalog__filter-show-button">
				<button
					class="h-cb c-button c-button--outline c-button--full c-button--filter c-button--catalog-<?php echo ideapark_mod( 'catalog_layout' ); ?>" id="js-sidebar-button"
					type="button"><i
						class="ip-filter c-catalog__filter-ico"></i><?php esc_html_e( 'Filter', 'antek' ); ?></button>
			</div>
		<?php } ?>
		<?php if ( $with_sidebar ) { ?>
			<div class="l-section__sidebar l-section__sidebar--left l-section__sidebar--popup">
				<?php get_sidebar( 'catalog' ); ?>
			</div>
		<?php } ?>
		<div class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php } ?>">
			<?php if ( $with_sidebar && ideapark_mod( 'sticky_sidebar' ) ) { ?>
			<div class="js-sticky-sidebar-nearby"><?php } ?>
				<?php if ( ! $ideapark_category_html_top_above && $ideapark_category_html_top ) { ?>
					<?php echo ideapark_wrap( $ideapark_category_html_top, '<div class="c-html-block c-html-block--top">', '</div>' ); ?>
				<?php } ?>
				<?php if ( is_search() ) {
					echo '<div class="c-catalog__search-form">';
					get_search_form();
					echo '</div>';
				} ?>
				<div class="c-catalog-ordering">
					<div class="c-catalog-ordering__col c-catalog-ordering__col--result">
						<?php echo sprintf( wp_kses_post( __( 'Your search results: <b>%s</b>', 'antek' ) ), $wp_query->found_posts ); ?>
					</div>
					<?php if ( sizeof( $sort_options_html ) > 1 ) { ?>
						<div class="c-catalog-ordering__col c-catalog-ordering__col--sort">
							<select class="c-catalog-ordering__select styled js-ordering-sort">
								<?php echo implode( '', $sort_options_html ); ?>
							</select>
						</div>
					<?php } ?>
				</div>

				<?php if ( have_posts() ) { ?>
					<div
						class="c-catalog__list c-catalog__list--<?php echo esc_attr( $layout ); ?>">
						<?php while ( have_posts() ) : the_post(); ?>
							<?php ideapark_get_template_part( 'templates/vehicle' ); ?>
						<?php endwhile; ?>
					</div>
					<?php ideapark_corenavi();
				} else { ?>
					<?php if ( $is_favorites_list ) { ?>
						<div class="c-cart-empty">
							<div class="c-cart-empty__image-wrap">
								<?php if ( ideapark_mod( 'cart_empty_favorites' ) ) { ?>
									<img src="<?php echo esc_url( ideapark_mod( 'cart_empty_favorites' ) ); ?>"
										 alt="<?php esc_html_e( 'Your favorites list is empty', 'antek' ); ?>"
										 class="c-cart-empty__image"/>
								<?php } else { ?>
									<i class="ip-star-empty c-cart-empty__svg"></i>
								<?php } ?>
							</div>
							<h2 class="c-cart-empty__header"><?php esc_html_e( 'Your favorites list is empty', 'antek' ); ?></h2>
							<a class="c-form__button c-cart-empty__backward"
							   href="<?php echo esc_url( get_post_type_archive_link( 'catalog' ) ); ?>">
								<?php esc_html_e( 'Return to catalog', 'antek' ) ?>
							</a>
						</div>
					<?php } else { ?>
						<p class="c-catalog__nothing"><?php esc_html_e( 'Sorry, no items were found.', 'antek' ); ?></p>
					<?php } ?>
				<?php } ?>
				<?php if ( $with_sidebar && ideapark_mod( 'sticky_sidebar' ) ) { ?>
			</div><?php } ?>
		</div>
	</div>

<?php if ( $ideapark_category_html_bottom ) {
	echo ideapark_wrap( $ideapark_category_html_bottom, '<div class="c-html-block c-html-block--bottom">', '</div>' );
} ?>

<?php get_footer(); ?>