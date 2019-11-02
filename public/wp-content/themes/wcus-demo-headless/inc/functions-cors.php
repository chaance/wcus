<?php
/**
 * REST API CORS filter.
 *
 * @package WCUS_Demo
 * @subpackage Headless_Theme
 */
/**
 * Allow GET requests from origin
 * Thanks to https://joshpress.net/access-control-headers-for-the-wordpress-rest-api/
 */

namespace Chance_Digital\WCUS_Demo\Theme\CORS;

use function Chance_Digital\WCUS_Demo\Theme\Util\get_frontend_origin;

add_action( 'rest_api_init', __NAMESPACE__ . '\\handle_request', 15 );

function handle_request() {
	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
	add_filter( 'rest_pre_serve_request', function ( $value ) {
		header( 'Access-Control-Allow-Origin: ' . get_frontend_origin() );
		header( 'Access-Control-Allow-Methods: GET' );
		header( 'Access-Control-Allow-Credentials: true' );
		return $value;
	} );
}
