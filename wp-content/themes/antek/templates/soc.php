<?php

if ( ! $content = ideapark_mod( '_soc_cache' ) ) {
	ob_start();
	$soc_count = 0;
	foreach ( ideapark_social_networks() AS $code =>  $name ) {
		if ( ideapark_mod( $code ) ): $soc_count ++; ?>
			<a href="<?php echo esc_url( ideapark_mod( $code ) ); ?>" class="c-soc__link" target="_blank"><i
					class="ip-<?php echo esc_attr( $code ) ?> c-soc__icon c-soc__icon--<?php echo esc_attr( $code ) ?>"></i></a>
		<?php endif;
	}
	$content = ob_get_contents();
	ob_end_clean();
	ideapark_mod_set_temp( '_soc_cache', $content );
}

echo ideapark_wrap( $content, '<div class="c-soc' . ( ! empty( $ideapark_var['class'] ) ? ' ' . esc_attr( $ideapark_var['class'] ) : '' ) . '">', '</div>' ) ?>