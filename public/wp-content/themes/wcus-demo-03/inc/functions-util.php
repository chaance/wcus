<?php
/**
 * Utility functions.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

namespace Chance_Digital\WCUS_Demo\Theme\Util;

/**
 * Custom var_dump to favor Kint if it is installed.
 *
 * @link https://github.com/kint-php/kint/
 * @param mixed ...$values Values to debug.
 * @return void
 */
function dump( ...$values ) {
	$kint = '\\Kint';
	class_exists( $kint ) ? $kint::dump( ...$values ) : var_dump( ...$values ); // phpcs:ignore
}

/**
 * Get the correct asset file.
 * Falls back to unminified if the minified version cannot be found.
 *
 * @param  string $asset_type Correct asset type. Must be `css` or `js`.
 * @param  string $filename   Name of the file, minus its extension.
 * @return string             File URL.
 */
function get_asset_url( string $asset_type, string $filename ) : string {
	if ( ! in_array( $asset_type, [ 'css', 'js' ], true ) ) {
		throw new \Exception( 'Invalid asset type.' );
	}
	return file_exists( WCUS_DEMO_PATH . "dist/$asset_type/$filename.min.$asset_type" )
		? WCUS_DEMO_TEMPLATE_URL . "/dist/$asset_type/$filename.min.$asset_type"
		: WCUS_DEMO_TEMPLATE_URL . "/dist/$asset_type/$filename.$asset_type";
}

/**
 * Get asset version based on last saved timestamp.
 *
 * @param  string $asset_type Correct asset type. Must be `css` or `js`.
 * @param  string $filename   Name of the file, minus its extension.
 * @return string             File saved timestamp.
 */
function get_asset_version( string $asset_type, string $filename ) : int {
	if ( ! in_array( $asset_type, [ 'css', 'js' ], true ) ) {
		trigger_error(
			esc_html( "`{$asset_type} is not a valid asset. Only `css` or `js` files are allowed;`" ),
			E_USER_WARNING
		);
		return 0;
	}
	return file_exists( WCUS_DEMO_PATH . "dist/$asset_type/$filename.min.$asset_type" )
		? filemtime( WCUS_DEMO_PATH . "dist/$asset_type/$filename.min.$asset_type" )
		: filemtime( WCUS_DEMO_PATH . "dist/$asset_type/$filename.$asset_type" );
}
