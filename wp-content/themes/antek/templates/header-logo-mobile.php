<div
	class="c-header__logo c-header__logo--mobile">
	<?php if ( ! is_front_page() ) { ?><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="c-header__logo-link"><?php } ?>
		<?php
		$dimension = ideapark_mod_image_size( 'logo' );
		$logo_url  = ideapark_mod( 'logo' );

		if ( ideapark_mod( 'logo_mobile' ) && $logo_url ) { ?>
			<img <?php echo ideapark_mod_image_size( 'logo_mobile' ); ?>
				src="<?php echo esc_url( ideapark_mod( 'logo_mobile' ) ); ?>"
				alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				class="c-header__logo-img c-header__logo-img--mobile <?php ideapark_svg_logo_class( ideapark_mod( 'logo_mobile' ) ); ?>"/>
		<?php } elseif ( $logo_url ) { ?>
			<img <?php echo ideapark_wrap( $dimension ); ?>
				src="<?php echo esc_url( $logo_url ); ?>"
				alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
				class="c-header__logo-img c-header__logo-img--all <?php ideapark_svg_logo_class( $logo_url ); ?>"/>
		<?php } else { ?>
			<span
				class="c-header__logo-empty"><?php echo ideapark_truncate_logo_placeholder(); ?></span>
		<?php } ?>

	<?php if ( ! is_front_page() ) { ?></a><?php } ?>
</div>
