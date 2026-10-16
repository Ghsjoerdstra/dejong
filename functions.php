<?php
/**
 * Bouwgroep DE Jong — theme functions.
 */

defined( 'ABSPATH' ) || exit;

define( 'BDJ_VERSION', '1.3.1' );
define( 'BDJ_DIR', get_template_directory() );
define( 'BDJ_URI', get_template_directory_uri() );

require BDJ_DIR . '/inc/helpers.php';
require BDJ_DIR . '/inc/post-types.php';
require BDJ_DIR . '/inc/fields.php';
require BDJ_DIR . '/inc/blocks.php';
require BDJ_DIR . '/inc/vacature-editor.php';
require BDJ_DIR . '/inc/header-velden.php';
require BDJ_DIR . '/inc/customizer.php';
require BDJ_DIR . '/inc/contact.php';
require BDJ_DIR . '/inc/import.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus( array(
		'hoofdmenu' => 'Hoofdmenu',
	) );

	add_image_size( 'bdj-hero', 2560, 1440 );
	add_image_size( 'bdj-project', 1440, 960, true );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'bdj-fonts', 'https://fonts.googleapis.com/css2?family=Barlow+Semi+Condensed:wght@300;400;500;700&display=swap', array(), null );
	wp_enqueue_style( 'bdj-main', BDJ_URI . '/assets/css/main.css', array( 'bdj-fonts' ), BDJ_VERSION );
	wp_enqueue_script( 'bdj-header', BDJ_URI . '/assets/js/header.js', array(), BDJ_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
} );

// Geen block-library CSS nodig op de voorkant (alleen in de content gebruiken we simpele HTML).
add_action( 'wp_enqueue_scripts', function () {
	wp_dequeue_style( 'global-styles' );
}, 100 );

// Pagina's krijgen een samenvatting (gebruikt als korte tekst in de activiteitenslider).
add_action( 'init', function () {
	add_post_type_support( 'page', 'excerpt' );
} );
