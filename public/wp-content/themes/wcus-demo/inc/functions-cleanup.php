<?php
/**
 * Clean up WordPress defaults
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

namespace Chance_Digital\WCUS_Demo\Theme\Cleanup;

add_action( 'after_setup_theme', __NAMESPACE__ . '\\start_cleanup' );

/**
 * Add hooks.
 *
 * @return void
 */
function start_cleanup() : void {
	add_action( 'init',              __NAMESPACE__ . '\\cleanup_head' );
	add_filter( 'the_generator',     __NAMESPACE__ . '\\remove_rss_version' );
	add_filter( 'wp_head',           __NAMESPACE__ . '\\remove_wp_widget_recent_comments_style', 1 );
	add_action( 'wp_head',           __NAMESPACE__ . '\\remove_recent_comments_style', 1 );
	add_filter( 'tiny_mce_plugins',  __NAMESPACE__ . '\\disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', __NAMESPACE__ . '\\disable_emoji_dns_prefetch', 10, 2 );
}

/**
 * Clean up HTML head
 *
 * @return void
 */
function cleanup_head() : void {

	// EditURI link.
	remove_action( 'wp_head', 'rsd_link' );

	// Category feed links.
	remove_action( 'wp_head', 'feed_links_extra', 3 );

	// Post and comment feed links.
	remove_action( 'wp_head', 'feed_links', 2 );

	// Windows Live Writer.
	remove_action( 'wp_head', 'wlwmanifest_link' );

	// Index link.
	remove_action( 'wp_head', 'index_rel_link' );

	// Previous link.
	remove_action( 'wp_head', 'parent_post_rel_link', 10 );

	// Start link.
	remove_action( 'wp_head', 'start_post_rel_link', 10 );

	// Canonical.
	remove_action( 'wp_head', 'rel_canonical', 10 );

	// Shortlink.
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );

	// Links for adjacent posts.
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );

	// WP version.
	remove_action( 'wp_head', 'wp_generator' );

	// Emoji detection script.
	remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

	// Emoji styles.
	remove_action( 'wp_print_styles',    'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );

	// Static image replacement for emojis
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail',          'wp_staticize_emoji_for_email' );
}

/**
 * Remove WP version from RSS.
 *
 * @return string
 */
function remove_rss_version() : string {
	return '';
}

/**
 * Remove injected CSS for recent comments widget.
 *
 * @return void
 */
function remove_wp_widget_recent_comments_style() : void {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

/**
 * Remove injected CSS from recent comments widget.
 *
 * @return void
 */
function remove_recent_comments_style() : void {
	global $wp_widget_factory;
	if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
		remove_action( 'wp_head', [ $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ] );
	}
}

/**
 * Filter function used to remove the TinyMCE emoji plugin.
 *
 * @link https://developer.wordpress.org/reference/hooks/tiny_mce_plugins/
 *
 * @param  array $plugins An array of default TinyMCE plugins.
 * @return array          An array of TinyMCE plugins, without wpemoji.
 */
function disable_emojis_tinymce( array $plugins ) : array {
	if ( is_array( $plugins ) && in_array( 'wpemoji', $plugins, true ) ) {
		return array_diff( $plugins, [ 'wpemoji' ] );
	}
	return $plugins;
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @link https://developer.wordpress.org/reference/hooks/emoji_svg_url/
 *
 * @param  array  $urls          URLs to print for resource hints.
 * @param  string $relation_type The relation type the URLs are printed for.
 * @return array                 Difference betwen the two arrays.
 */
function disable_emoji_dns_prefetch( array $urls, string $relation_type ) : array {
	if ( 'dns-prefetch' === $relation_type ) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		$urls = array_values( array_diff( $urls, [ $emoji_svg_url ] ) );
	}
	return $urls;
}
