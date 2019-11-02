<?php
/**
 * Custom template-related functions for this theme.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

namespace Chance_Digital\WCUS_Demo\Theme\Template;

use Chance_Digital\WCUS_Demo\Theme\Nav\BEM_Nav_Walker;

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

/**
 * Use a wp_nav_menu with our custom BEM menu walker.
 *
 * @param  array $args - Arguments to pass to wp_nav_menu.
 *                       Accepts a required `block_class` argument used to construct the
 *                       BEM class structure.
 * @return void
 */
function bem_nav_menu( array $args ) {
	if ( ! isset( $args['block_class'] ) || ! is_string( $args['block_class'] ) ) {
		throw new \Exception( '`block_class` string must be set in args when using `' . __NAMESPACE__ . '\\bem_nav_menu`' );
	}
	$container_class = $args['block_class'];
	$menu_class      = $args['block_class'] . '__menu';

	if ( isset( $args['container_class'] ) ) {
		$container_class = $container_class . ' ' . $args['container_class'];
		unset( $args['container_class'] );
	}

	if ( isset( $args['menu_class'] ) ) {
		$menu_class = $menu_class . ' ' . $args['menu_class'];
		unset( $args['menu_class'] );
	}

	if ( isset( $args['items_wrap'] ) ) {
		unset( $args['items_wrap'] );
	}

	wp_nav_menu( array_merge( $args, [
		'container_class' => $container_class,
		'menu_class'      => $menu_class,
		'walker'          => new BEM_Nav_Walker(),
	] ) );
}
