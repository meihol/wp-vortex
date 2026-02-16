<?php
defined( 'ABSPATH' ) || exit;

global $theme_home;

$footer_page_id = ( $page = ideapark_get_page_by_title( 'Footer Red', OBJECT, 'html_block' ) ) ? $page->ID : 0;
$home_page_id   = ( $page = ideapark_get_page_by_title( 'Home Red' ) ) ? $page->ID : 0;

$mods                 = [];
$mods['accent_color'] = '#ef3c0f';
$mods['logo']         = trailingslashit( home_url( '/' ) ) . 'wp-content/uploads/2021/04/antek-0302733192.svg';

if ( $footer_page_id ) {
	$mods['footer_page'] = $footer_page_id;
}

$options = [];
if ( $home_page_id ) {
	$options['page_on_front'] = $home_page_id;
}

$theme_home = [
	'title'      => __( 'Red', 'ideapark-antek' ),
	'screenshot' => 'home-2.jpg',
	'url'        => 'https://parkofideas.com/antek/demo/home-2/',
	'mods'       => $mods,
	'options'    => $options,
];