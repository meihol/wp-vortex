<?php if ( trim( ideapark_mod( 'header_other' ) ) ) { ?>
	<li class="c-header__top-row-item c-header__top-row-item--other">
		<i class="<?php echo ( ideapark_mod( 'custom_header_icon_other' ) ?: 'ip-hand' ); ?> c-header__top-row-icon c-header__top-row-icon--other"></i>
		<span class="c-header__top-row-html"><?php echo do_shortcode( trim( ideapark_mod( 'header_other' ) ) ); ?></span>
	</li>
<?php } ?>