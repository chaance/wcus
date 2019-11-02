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

	$hook( 'action', 'after_setup_theme',     'internationalize', 10, 0 );
	$hook( 'action', 'after_setup_theme',     'handle_theme_support', 10, 0 );
	$hook( 'action', 'wp_enqueue_scripts',    'scripts', 10, 0 );
	$hook( 'action', 'wp_enqueue_scripts',    'styles', 10, 0 );
	$hook( 'action', 'wp_enqueue_scripts',    'detect_js', 0, 0 );
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
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'editor.css' );

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
	wp_register_script( 'frontend', WCUS_DEMO_TEMPLATE_URL . '/script.js', array( 'jquery' ), WCUS_DEMO_VERSION, true );
	wp_localize_script(
		'frontend', '__WCUS_DEMO', array(
			'ajaxUrl'  => esc_url( admin_url( 'admin-ajax.php' ) ),
			'themeUrl' => WCUS_DEMO_TEMPLATE_URL,
			'nonce'    => wp_create_nonce( 'wcus_demo_nonce' ),
			'siteUrl'  => site_url(),
		)
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
	wp_enqueue_style( 'frontend', WCUS_DEMO_TEMPLATE_URL . '/style.css', array(), WCUS_DEMO_VERSION );
}
