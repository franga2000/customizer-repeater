<?php
/*
Plugin Name:  Customizer Repeater
Plugin URI:   https://github.com/mrbobbybryant/customizer-repeater
Description:  A Custom Repeater Field for the WordPress Customizer.
Version:      1.0.0
Author:       Bobby Bryant
Author URI:   https://github.com/mrbobbybryant
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH') ) {
	exit;
}

if ( ! defined( 'CUSTOMIZER_REPEATER_DIR' ) ) {
	define( 'CUSTOMIZER_REPEATER_DIR', dirname( __FILE__ ) );
}

if ( ! defined( 'CUSTOMIZER_REPEATER_URL' ) ) {
	define( 'CUSTOMIZER_REPEATER_URL', get_stylesheet_directory_uri() . '/vendor' );
}

if ( ! defined( 'CUSTOMIZER_REPEATER_FILE' ) ) {
	define( 'CUSTOMIZER_REPEATER_FILE', __FILE__ );
}

if ( ! defined( 'CUSTOMIZER_REPEATER_VERSION' ) ) {
	define( 'CUSTOMIZER_REPEATER_VERSION', '1.0.0' );
}

function customizer_repeater_register( $wp_customize ) {
	require_once CUSTOMIZER_REPEATER_DIR . '/class/customizer-repeater-control.php';
}
add_action( 'customize_register', 'customizer_repeater_register' );

require CUSTOMIZER_REPEATER_DIR . '/inc/customizer.php';
