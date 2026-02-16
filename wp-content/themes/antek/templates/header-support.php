<?php if ( ideapark_mod( 'header_support' ) ) { ?>
	<div class="c-header__main-row-item c-header__main-row-item--support <?php if (ideapark_mod( 'header_support_hide' )) { ?>c-header__main-row-item--support-hide<?php } ?>">
		<div class="c-header__support">
			<i class="<?php echo ( ideapark_mod( 'custom_header_icon_support' ) ?: 'ip-hands-free' ); ?> c-header__support-icon"></i>
			<div class="c-header__support-content">
				<?php echo ideapark_wrap( esc_html(ideapark_mod( 'header_support_title' )), '<div class="c-header__support-title">' ,'</div>' ) ?>
				<?php echo ideapark_phone_wrap( esc_html( ideapark_mod( 'header_support_phone' ) ), '<div class="c-header__support-phone">', '</div>' ); ?>
			</div>
		</div>
	</div>
<?php } ?>
