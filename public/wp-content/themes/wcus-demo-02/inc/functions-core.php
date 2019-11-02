<?php
/**
 * Core setup, site hooks and filters.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

namespace Chance_Digital\WCUS_Demo\Theme\Core;

use function Chance_Digital\WCUS_Demo\Theme\Util\get_asset_url;
use function Chance_Digital\WCUS_Demo\Theme\Util\get_asset_version;

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @return void
 */
function setup() : void {
	$hook = function(
		string $hook_type,
		string $hook,
		$function,
		int $priority = 10,
		int $accepted_args = 1
	) {
		$n = function( $function ) {
			$function = __NAMESPACE__ . "\\$function";
			if ( function_exists( $function ) ) {
				return $function;
			}
			return false;
		};
		$function_name = __NAMESPACE__ . "\\$function";
		$function      = $n( $function );
		if ( ! $function ) {
			trigger_error(
				esc_html( "You attempted to call a function `{$function_name}` but it does not exist. Remove the hook or create the missing function to fix this warning;" ),
				E_USER_WARNING
			);
			return;
		}
		if ( ! in_array( $hook_type, [ 'filter', 'action' ], true ) ) {
			trigger_error(
				esc_html( "Hooks must be a filter or an action; {$hook_type} detected. Update or remove the hook to fix this warning;" ),
				E_USER_WARNING
			);
			return;
		}
		call_user_func( "\\add_$hook_type", $hook, $function, $priority, $accepted_args );
	};

	$hook( 'action', 'after_setup_theme',  'internationalize', 10, 0 );
	$hook( 'action', 'after_setup_theme',  'handle_theme_support', 10, 0 );
	$hook( 'action', 'wp_enqueue_scripts', 'scripts', 10, 0 );
	$hook( 'action', 'wp_enqueue_scripts', 'styles', 10, 0 );
	$hook( 'action', 'wp_enqueue_scripts', 'detect_js', 0, 0 );
	$hook( 'filter', 'add_script_attribute',  'add_script_attribute', 10, 2 );
}

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @return void
 */
function detect_js() : void {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}

/**
 * Makes Theme available for translation.
 * Translations can be added to the /languages directory.
 *
 * @return void
 */
function internationalize() : void {
	load_theme_textdomain( 'wcus-demo', WCUS_DEMO_PATH . '/languages' );
}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param  string $tag    The script tag.
 * @param  string $handle The script handle.
 * @return string
 */
function add_script_attribute( string $tag, string $handle ) : string {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );
	if ( ! $script_execution ) {
		return $tag;
	}
	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag;
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	foreach ( wp_scripts()->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}
	return $tag;
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @return void
 */
function handle_theme_support() : void {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		]
	);
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add featured image sizes.
	// Sizes are optimized and cropped for landscape aspect ratio and
	// optimized for HiDPI displays on 'small' and 'medium' screen sizes.
	// add_image_size( 'featured-small', 640, 9999 );
	// add_image_size( 'featured-medium', 1280, 9999 );
	// add_image_size( 'featured-large', 1440, 9999 );
	// add_image_size( 'featured-xlarge', 1920, 9999 );

	// Register nav menus.
	register_nav_menus(
		[
			'main'      => __( 'Main Navigation',      'wcus-demo' ),
			'secondary' => __( 'Secondary Navigation', 'wcus-demo' ),
			'footer'    => __( 'Footer Navigation',    'wcus-demo' ),
			'social'    => __( 'Social Navigation',    'wcus-demo' ),
		]
	);
}

/**
 * Enqueue scripts for front-end.
 *
 * Inspired by https://github.com/10up/theme-scaffold/blob/master/includes/core.php
 *
 * @return void
 */
function scripts() : void {
	// Deregister the jquery version bundled with WordPress.
	wp_deregister_script( 'jquery' );

	// CDN hosted jQuery placed in the header, as some plugins require that jQuery is loaded in the header.
	wp_enqueue_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js', [], '3.2.1', false );

	// Deregister the jquery-migrate version bundled with WordPress.
	wp_deregister_script( 'jquery-migrate' );

	// CDN hosted jQuery migrate for compatibility with jQuery 3.x.
	wp_register_script( 'jquery-migrate', '//code.jquery.com/jquery-migrate-3.0.1.min.js', [ 'jquery' ], '3.0.1', false );
	wp_enqueue_script( 'jquery-migrate' );

	// Frontend JS.
	wp_register_script( 'frontend', get_asset_url( 'js', 'frontend' ), [ 'jquery' ], get_asset_version( 'js', 'frontend' ), true );
	wp_localize_script(
		'frontend', '__WCUS_DEMO', [
			'ajaxUrl'  => esc_url( admin_url( 'admin-ajax.php' ) ),
			'themeUrl' => WCUS_DEMO_TEMPLATE_URL,
			'nonce'    => wp_create_nonce( 'wcus_demo_nonce' ),
			'siteUrl'  => site_url(),
		]
	);
	wp_enqueue_script( 'frontend' );

	// Add the comment-reply library on pages where it is necessary.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/**
 * Enqueue styles for front-end.
 *
 * @return void
 */
function styles() : void {
	wp_enqueue_style( 'frontend', get_asset_url( 'css', 'frontend' ), [], get_asset_version( 'css', 'frontend' ) );
}
