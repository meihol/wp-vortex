<aside
	   class="c-catalog-sidebar <?php ideapark_class( ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar' ); ?>" id="js-popup-sidebar">
	<div class="c-catalog-sidebar__shadow"></div>
	<div class="c-catalog-sidebar__wrap" id="js-catalog-sidebar-wrap">
		<div class="c-catalog-sidebar__buttons">
			<button type="button" class="h-cb c-catalog-sidebar__close"
					id="ideapark-shop-sidebar-close"><i class="ip-close"><!-- --></i></button>
		</div>
		<div class="c-sidebar c-catalog-sidebar__content" id="js-catalog-sidebar-content">
			<div class="c-sidebar-wrap">
				<?php dynamic_sidebar( 'shop-sidebar' ); ?>
			</div>
		</div>
	</div>
</aside>