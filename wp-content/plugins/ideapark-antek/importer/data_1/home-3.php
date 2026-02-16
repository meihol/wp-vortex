<?php
defined( 'ABSPATH' ) || exit;

global $theme_home;

$footer_page_id  = ( $page = ideapark_get_page_by_title( 'Footer Green', OBJECT, 'html_block' ) ) ? $page->ID : 0;
$home_page_id    = ( $page = ideapark_get_page_by_title( 'Home Green' ) ) ? $page->ID : 0;
$advert_bar_page = ( $page = ideapark_get_page_by_title( 'Offer' ) ) ? $page->ID : 0;

$mods                                 = [];
$mods['accent_color']                 = '#4bb050';
$mods['logo']                         = trailingslashit( home_url( '/' ) ) . 'wp-content/uploads/2021/04/antek-0302896675.svg';
$mods['sticky_header_height_desktop'] = 60;
$mods['header_support']               = false;
$mods['header_blocks_layout']         = 'blocks-last';
$mods['header_blocks']                = 'favorites=1|social=0|other=0|email=0|phone=1|callback=1|address=1|hours=1|auth=1';

if ( $footer_page_id ) {
	$mods['footer_page'] = $footer_page_id;
}

if ( $advert_bar_page ) {
	$mods['footeheader_advert_bar_pager_page'] = $advert_bar_page;
}

$options = [];
if ( $home_page_id ) {
	$options['page_on_front'] = $home_page_id;
}

$theme_home = [
	'title'      => __( 'Green', 'ideapark-antek' ),
	'screenshot' => 'home-3.jpg',
	'url'        => 'https://parkofideas.com/antek/demo/home-3/',
	'mods'       => $mods,
	'options'    => $options,
];