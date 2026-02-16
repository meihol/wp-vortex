<?php
/**
 * Antek-Child functions and definitions
 *
 * @package antek-child
 */

/** Enqueue the child theme stylesheet **/
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'antek-child-style', get_stylesheet_directory_uri() . '/style.css', PHP_INT_MAX );
}, 100 );