<?php
if ( !defined( 'CUSTOMIZER_REPEATER_DIR' ) ) {
	define( 'CUSTOMIZER_REPEATER_DIR', dirname( __FILE__ ) );
}

if ( !defined( 'CUSTOMIZER_REPEATER_URL' ) ) {
	define( 'CUSTOMIZER_REPEATER_URL', get_stylesheet_directory_uri() . '/vendor' );
}

if ( !defined( 'CUSTOMIZER_REPEATER_FILE' ) ) {
	define( 'CUSTOMIZER_REPEATER_FILE', __FILE__ );
}

if ( !defined( 'CUSTOMIZER_REPEATER_VERSION' ) ) {
	define( 'CUSTOMIZER_REPEATER_VERSION', '0.4.0' );
}

function customizer_repeater_register( $wp_customize ) {
	require_once( CUSTOMIZER_REPEATER_DIR .'/class/customizer-repeater-control.php' );
}
add_action( 'customize_register', 'customizer_repeater_register' );

require CUSTOMIZER_REPEATER_DIR . '/inc/customizer.php';
