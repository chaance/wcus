<?php
/**
 * Core functions.
 *
 * @package WCUS_Demo
 * @subpackage Headless_Theme
 */

namespace Chance_Digital\WCUS_Demo\Theme\Core;

add_action( 'after_setup_theme', __NAMESPACE__ . '\\register_menus' );

/**
 * Register navigation menu.
 *
 * @return void
 */
function register_menus() {
	register_nav_menus(
		[
			'main'      => __( 'Main Navigation',      'wcus-demo' ),
			'secondary' => __( 'Secondary Navigation', 'wcus-demo' ),
			'footer'    => __( 'Footer Navigation',    'wcus-demo' ),
			'social'    => __( 'Social Navigation',    'wcus-demo' ),
		]
	);
}
