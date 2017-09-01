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

require CUSTOMIZER_REPEATER_DIR . '/customizer-repeater/inc/customizer.php';
