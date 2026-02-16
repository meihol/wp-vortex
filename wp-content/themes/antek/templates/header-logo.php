<div class="c-header__main-row-item">
	<div
		class="c-header__logo c-header__logo--desktop">
		<?php if ( ! is_front_page() ) { ?><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="c-header__logo-link"><?php } ?>
			<?php
			$dimension = ideapark_mod_image_size( 'logo' );
			if ( ideapark_mod( 'logo' ) ) { ?>
				<img <?php echo ideapark_wrap( $dimension ); ?>
					src="<?php echo esc_url( ideapark_mod( 'logo' ) ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
					class="c-header__logo-img <?php ideapark_svg_logo_class( ideapark_mod( 'logo' ) ); ?> <?php ideapark_class( ideapark_mod( 'logo_mobile' ), 'c-header__logo-img--desktop', 'c-header__logo-img--all' ); ?>"/>
			<?php } else { ?>
				<span
					class="c-header__logo-empty"><?php echo ideapark_truncate_logo_placeholder(); ?></span>
			<?php } ?>
			<?php if ( ! is_front_page() ) { ?></a><?php } ?>
	</div>
</div>
