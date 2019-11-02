<?php
/**
 * Theme functions
 *
 * @package WCUS_Demo
 * @subpackage Headless_Theme
 */

define( 'WCUS_DEMO_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'WCUS_DEMO_MINIMUM_WP_VERSION', '5.0' );
define( 'WCUS_DEMO_MINIMUM_PHP_VERSION', '7.3' );
define( 'WCUS_DEMO_TEMPLATE_URL', get_template_directory_uri() );
define( 'WCUS_DEMO_PATH', trailingslashit( get_template_directory() ) );
define( 'WCUS_DEMO_INC', WCUS_DEMO_PATH . 'inc/' );
define( 'WCUS_DEMO_NAMESPACE', 'Chance_Digital\\WCUS_Demo\\Theme' );
define( 'WCUS_DEMO_SITE_NAME', get_bloginfo( 'name' ) );

// Bail if requirements are not met.
// https://github.com/wprig/wprig/blob/master/functions.php
if (
	version_compare( $GLOBALS['wp_version'], WCUS_DEMO_MINIMUM_WP_VERSION, '<' ) ||
	version_compare( phpversion(), WCUS_DEMO_MINIMUM_PHP_VERSION, '<' )
) {
	require_once WCUS_DEMO_INC . 'functions-back-compat.php';
	return;
}

require_once WCUS_DEMO_INC . 'functions-util.php';
require_once WCUS_DEMO_INC . 'functions-core.php';
require_once WCUS_DEMO_INC . 'functions-cors.php';
require_once WCUS_DEMO_INC . 'functions-admin.php';

call_user_func( WCUS_DEMO_NAMESPACE . '\\Core\\setup' );
