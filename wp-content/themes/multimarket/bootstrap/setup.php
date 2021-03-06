<?php

/**
 * Initial setup theme
 *
 * @return void
 * @author tokoo
 **/
add_action( 'after_setup_theme', 'multimarket_setup' );
function multimarket_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 */
	load_theme_textdomain( 'multimarket', get_template_directory() . '/languages' );

	/**
	 * Set the content width based on the theme's design and stylesheet.
	 */
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 768; /* pixels */
	}

	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	$args = array(
		'wp-head-callback'	=> '',
		'flex-height'		=> true,
		'flex-width'		=> true,
		'width'				=> 1600,
		'height'			=> 200,
		'default-image' 	=> MULTIMARKET_THEME_URI . '/assets/img/header-image.jpg',
	);
	add_theme_support( 'custom-logo' );
	add_theme_support( 'custom-header', $args );
	set_post_thumbnail_size( 600, 400 );

	add_theme_support( 'post-formats', array( 'audio', 'video' ) );
	
	$woocommerce_product_image_zoom = get_theme_mod( 'multimarket_enable_single_product_image_zoom', true );
	if ( true == $woocommerce_product_image_zoom ) {
		add_theme_support( 'wc-product-gallery-zoom' );
	}
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_editor_style( 'assets/css/editor-style.css' );
}

/**
 * Register base sidebar
 *
 * @return void
 * @author tokoo
 **/
add_action( 'widgets_init', 'multimarket_register_primary_sidebar' );
function multimarket_register_primary_sidebar() {

	$sidebars = include( get_template_directory() . '/app/config/config-sidebars.php' );

	if ( is_array( $sidebars ) && ! empty( $sidebars ) ) {
		foreach ( $sidebars as $sidebar ) {
			$sidebar['name'] = $sidebar['name'];

			if ( isset( $sidebar['description'] ) ) {
				$sidebar['description'] = $sidebar['description'];
			}

			register_sidebar( $sidebar );
		}
	}

}


/** ======================================================================== *
 *  Load All Configuration 													 *
 ** ======================================================================== */

	new Multimarket_Autoloaders();

/* ================================ END =================================== */


