<?php
/**
 * Custom template-related functions for this theme.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

namespace Chance_Digital\WCUS_Demo\Theme\Template;

add_filter( 'body_class',       __NAMESPACE__ . '\\body_classes' );
add_filter( 'template_include', __NAMESPACE__ . '\\var_template_include', 1000 );

/**
 * Add a global variable to identify the page template being used in a given view.
 *
 * @param  string $template The template path.
 * @return string           The template filename.
 */
function var_template_include( string $template ) : string {
	$GLOBALS['wcus_current_theme_template'] = basename( $template );
	return $template;
}

/**
 * Get the current template.
 *
 * @return bool|string The template file, if it is defined.
 */
function get_current_template() {
	if ( ! isset( $GLOBALS['wcus_current_theme_template'] ) ) {
		return false;
	}
	return $GLOBALS['wcus_current_theme_template'];
}

/**
 * Filter body classes for pages.
 *
 * @param  array $classes  Class list.
 * @return array           Modified class list.
 */
function body_classes( array $classes ) : array {
	// Default slugs.
	$template_slug = get_current_template();
	$template_slug = strpos( $template_slug, 'page-' ) === 0 ? $template_slug : "page-$template_slug";
	$page_slug     = '';

	// Customizations
	if ( is_page() && ! is_front_page() ) {
		$page_slug     = 'page-' . basename( get_permalink() );
		$template_slug = get_page_template_slug();

		if ( empty( $template_slug ) ) {
			if ( get_post_type() === 'page' ) {
				$template_slug = 'page-default';
			}
		}
	}

	if ( ! empty( $page_slug ) && ! in_array( $page_slug, $classes, true ) ) {
		$classes[] = $page_slug;
	}
	if ( ! empty( $template_slug ) && ! in_array( $template_slug, $classes, true ) ) {
		$classes[] = $template_slug;
	}

	// Clean up class names for custom templates.
	$classes = array_map(
		function ( $class ) {
				return preg_replace( [ '/(\.php)?$/', '/^templates/', '/^page-templates/' ], '', $class );
		}, $classes
	);
	return array_filter( $classes );
}
