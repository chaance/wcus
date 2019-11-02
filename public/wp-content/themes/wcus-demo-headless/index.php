<?php
/**
 * Redirect frontend requests to REST API.
 *
 * @package WCUS_Demo
 * @subpackage Headless_Theme
 */

// Redirect individual posts to the REST API endpoint.
if ( is_singular() ) {
	header(
		sprintf(
			'Location: /wp-json/wp/v2/%s/%s',
			get_post_type_object( get_post_type() )->rest_base,
			get_post()->ID
		)
	);
} else {
	header( 'Location: /wp-json/' );
}
