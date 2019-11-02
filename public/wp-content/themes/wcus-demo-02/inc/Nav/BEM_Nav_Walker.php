<?php
/**
 * Custom navigation walker for the top-nav menu.
 *
 * Accessibility notes:
 * - http://adrianroselli.com/2017/10/dont-use-aria-menu-roles-for-site-nav.html
 * - https://marcozehe.wordpress.com/2019/05/30/wai-aria-menus-use-with-care/
 *
 *   Avoid adding aria menu roles (menu, menubar, menuitem, etc.) until the menu
 *   is built. Test manually with key tabbing and a screen reader and consider
 *   adding them only if needed to improve the experience.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

namespace Chance_Digital\WCUS_Demo\Theme\Nav;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * Custom walker for BEM nav menu.
 */
class BEM_Nav_Walker extends \Walker_Nav_Menu {
	/**
	 * Start the element output.
	 *
	 * @param  string   $output  Used to append additional content (passed by reference).
	 * @param  \WP_Post $item    The data object.
	 * @param  int      $depth   Depth of the item.
	 * @param  stdClass $args    An array of additional arguments.
	 * @param  int      $id      ID of the current item.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent  = ( $depth ) ? str_repeat( $t, $depth ) : '';
		$lvl     = $depth + 1;
		$classes = empty( $item->classes ) ? [] : (array) $item->classes;

		// BEM stuff.
		if ( ! empty( $args->container_class ) ) {

			// BEM block class.
			$block = $args->container_class;

			// Check for multiple classes and only use first for the block name.
			$block     = strpos( $block, ' ' ) !== false ? explode( ' ', $block )[0] : $block;
			$element   = 'item';
			$classes[] = "{$block}__{$element}";
			if ( 0 !== $id ) {
				$classes[] = "{$block}__{$element}--{$id}";
			}
			if ( $depth > 0 ) {
				$classes[] = "{$block}__{$element}--has-parent";
			}
			if ( $this->has_children ) {
				$classes[] = "{$block}__{$element}--has-child";
			}
			$classes[] = "{$block}__{$element}--l{$lvl}";

			// Filter out the WP generated classes.
			$classes = array_filter(
				$classes, function( $item ) {
					return ! ( strpos( $item, 'menu-item' ) === 0 );
				}
			);

		} else {
			$classes[] = 'menu-item-' . $item->ID;
		}

		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param WP_Post  $item  Menu item data object.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'wcus_demo_nav_menu_bem_item_args', $args, $item, $depth );

		/**
		 * Filters the CSS class(es) applied to a menu item's list item element.
		 *
		 * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'wcus_demo_nav_menu_bem_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'wcus_demo_nav_menu_bem_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts = [];

		// phpcs:disable
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';
		$atts['class']  = ! empty( $block )            ? "{$block}__link"  : '';
		// phpcs:enable

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'wcus_demo_nav_menu_bem_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** Original filter (the_title) is documented in wp-includes/post-template.php */
		$title = apply_filters( 'wcus_demo_bem_the_title', $item->title, $item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @param string   $title The menu item's title.
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'wcus_demo_nav_menu_bem_item_title', $title, $item, $args, $depth );

		$item_output  = $args->before;
		$item_output .= "<a{$attributes}>";
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filters a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @param string    $item_output The menu item's starting HTML output.
		 * @param \WP_Post  $item        Menu item data object.
		 * @param int       $depth       Depth of menu item. Used for padding.
		 * @param stdClass  $args        An object of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'wcus_demo_walker_nav_menu_bem_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @param  string   $output  Used to append additional content (passed by reference).
	 * @param  int      $depth   Depth of the item.
	 * @param  stdClass $args    An array of additional arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = [] ) {
		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = str_repeat( $t, $depth );
		$level  = $depth + 2;

		// BEM it.
		if ( ! empty( $args->container_class ) ) {
			$block   = $args->container_class;
			$block   = strpos( $block, ' ' ) !== false ? explode( ' ', $block )[0] : $block;
			$classes = [ "{$block}__submenu" ];
			if ( 0 <= $depth ) {
				$classes[] = "{$block}__submenu--l{$level}";
			}
		} else {
			// Default class name.
			$classes = [ 'sub-menu' ];
		}
		$class_names = join( ' ', apply_filters( 'wcus_demo_nav_menu_bem_submenu_css_class', $classes, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$output     .= "{$n}{$indent}<ul{$class_names}>{$n}";
	}
}
