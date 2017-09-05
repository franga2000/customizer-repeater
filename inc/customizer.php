<?php

function customizer_repeater_sanitize($input){
	$input_decoded = json_decode($input,true);

	if(!empty($input_decoded)) {
		foreach ($input_decoded as $boxk => $box ){
			if ( is_array( $box ) ) {
				foreach ($box as $key => $value){

					$input_decoded[$boxk][$key] = wp_kses_post( force_balance_tags( $value ) );

				}
			}
		}
		return json_encode($input_decoded);
	}
	return $input;
}

function get_customizer_values( $settings ) {
	$settings = get_theme_mod( $settings );

	if ( empty( $settings ) ) {
		return false;
	}

	return array_map( function( $settings ) {
		return array_filter( get_object_vars( $settings ), function( $item ) {
			if ( 'undefined' !== $item ) {
				return $item;
			}
		} );
	}, json_decode( $settings ) );

}
