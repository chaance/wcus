<?php
/**
 * Theme functions
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

define( 'WCUS_DEMO_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'WCUS_DEMO_MINIMUM_WP_VERSION', '5.0' );
define( 'WCUS_DEMO_MINIMUM_PHP_VERSION', '7.3' );
define( 'WCUS_DEMO_TEMPLATE_URL', get_template_directory_uri() );
define( 'WCUS_DEMO_PATH', trailingslashit( get_template_directory() ) );
define( 'WCUS_DEMO_INC', WCUS_DEMO_PATH . 'inc/' );
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

add_action( 'after_setup_theme',    'wcus_demo_internationalize', 10, 0 );
add_action( 'after_setup_theme',    'wcus_demo_handle_theme_support', 10, 0 );
add_action( 'init',                 'wcus_demo_cleanup_head' );
add_action( 'init',                 'wcus_demo_register_blocks' );
add_action( 'wp_enqueue_scripts',   'wcus_demo_scripts', 10, 0 );
add_action( 'wp_enqueue_scripts',   'wcus_demo_styles', 10, 0 );
add_action( 'wp_enqueue_scripts',   'wcus_demo_detect_js', 0, 0 );
add_action( 'wp_head',              'wcus_demo_remove_recent_comments_style', 1 );

add_filter( 'add_script_attribute', 'wcus_demo_add_script_attribute', 10, 2 );
add_filter( 'body_class',           'wcus_demo_body_classes' );
add_filter( 'template_include',     'wcus_demo_var_template_include', 1000 );
add_filter( 'the_generator',        'wcus_demo_remove_rss_version' );
add_filter( 'tiny_mce_plugins',     'wcus_demo_disable_emojis_tinymce' );
add_filter( 'wp_head',              'wcus_demo_remove_wp_widget_recent_comments_style', 1 );
add_filter( 'wp_resource_hints',    'wcus_demo_disable_emoji_dns_prefetch', 10, 2 );


/**
 * Backwards compatibility for `wp_body_open`. (wp-shims)
 */
if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * Run the wp_body_open action.
	 *
	 * @return void
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

/**
 * Handles JavaScript detection. (core)
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @return void
 */
function wcus_demo_detect_js() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}

/**
 * Makes Theme available for translation. (core)
 * Translations can be added to the /languages directory.
 *
 * @return void
 */
function wcus_demo_internationalize() {
	load_theme_textdomain( 'wcus-demo', WCUS_DEMO_PATH . '/languages' );
}

/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag. (core)
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param  string $tag    The script tag.
 * @param  string $handle The script handle.
 * @return string
 */
function wcus_demo_add_script_attribute( $tag, $handle ) {
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
 * Sets up theme defaults and registers support for various WordPress features. (core)
 *
 * @return void
 */
function wcus_demo_handle_theme_support() {
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'editor.css' );

	// Add featured image sizes.
	// Sizes are optimized and cropped for landscape aspect ratio and
	// optimized for HiDPI displays on 'small' and 'medium' screen sizes.
	// add_image_size( 'featured-small', 640, 9999 );
	// add_image_size( 'featured-medium', 1280, 9999 );
	// add_image_size( 'featured-large', 1440, 9999 );
	// add_image_size( 'featured-xlarge', 1920, 9999 );

	// Register nav menus.
	register_nav_menus( array(
		'main'      => __( 'Main Navigation',      'wcus-demo' ),
		'secondary' => __( 'Secondary Navigation', 'wcus-demo' ),
		'footer'    => __( 'Footer Navigation',    'wcus-demo' ),
		'social'    => __( 'Social Navigation',    'wcus-demo' ),
	) );
}

/**
 * Enqueue scripts for front-end. (core)
 *
 * @return void
 */
function wcus_demo_scripts() {
	// Frontend JS.
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
 * Enqueue styles for front-end. (core)
 *
 * @return void
 */
function wcus_demo_styles() {
	wp_enqueue_style( 'frontend', WCUS_DEMO_TEMPLATE_URL . '/style.css', array(), WCUS_DEMO_VERSION );
}

/**
 * Clean up HTML head. (cleanup)
 *
 * @return void
 */
function wcus_demo_cleanup_head() {

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
 * Remove WP version from RSS. (cleanup)
 *
 * @return string
 */
function wcus_demo_remove_rss_version() {
	return '';
}

/**
 * Remove injected CSS for recent comments widget. (cleanup)
 *
 * @return void
 */
function wcus_demo_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

/**
 * Remove injected CSS from recent comments widget. (cleanup)
 *
 * @return void
 */
function wcus_demo_remove_recent_comments_style() {
	global $wp_widget_factory;
	if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
		remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
	}
}

/**
 * Filter function used to remove the TinyMCE emoji plugin. (cleanup)
 *
 * @link https://developer.wordpress.org/reference/hooks/tiny_mce_plugins/
 *
 * @param  array $plugins An array of default TinyMCE plugins.
 * @return array          An array of TinyMCE plugins, without wpemoji.
 */
function wcus_demo_disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) && in_array( 'wpemoji', $plugins, true ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	}
	return $plugins;
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints. (cleanup)
 *
 * @link https://developer.wordpress.org/reference/hooks/emoji_svg_url/
 *
 * @param  array  $urls          URLs to print for resource hints.
 * @param  string $relation_type The relation type the URLs are printed for.
 * @return array                 Difference betwen the two arrays.
 */
function wcus_demo_disable_emoji_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' === $relation_type ) {
		/** This filter is documented in wp-includes/formatting.php */
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		$urls = array_values( array_diff( $urls, array( $emoji_svg_url ) ) );
	}
	return $urls;
}

/**
 * Add a global variable to identify the page template being used in a given view. (template)
 *
 * @param  string $template The template path.
 * @return string           The template filename.
 */
function wcus_demo_var_template_include( $template ) {
	$GLOBALS['wcus_current_theme_template'] = basename( $template );
	return $template;
}

/**
 * Get the current template. (template)
 *
 * @return bool|string The template file, if it is defined.
 */
function wcus_demo_get_current_template() {
	if ( ! isset( $GLOBALS['wcus_current_theme_template'] ) ) {
		return false;
	}
	return $GLOBALS['wcus_current_theme_template'];
}

/**
 * Filter body classes for pages. (template)
 *
 * @param  array $classes  Class list.
 * @return array           Modified class list.
 */
function wcus_demo_body_classes( $classes ) {
	// Default slugs.
	$template_slug = wcus_demo_get_current_template();
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
				return preg_replace( array( '/(\.php)?$/', '/^templates/', '/^page-templates/' ), '', $class );
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
function wcus_demo_bem_nav_menu( array $args ) {
	if ( ! isset( $args['block_class'] ) || ! is_string( $args['block_class'] ) ) {
		throw new \Exception( '`block_class` string must be set in args when using `wcus_demo_bem_nav_menu`' );
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
		'walker'          => new WCUS_Demo_BEM_Nav_Walker(),
	] ) );
}

/**
 * Register custom GB blocks. (blocks)
 *
 * @return void
 */
function wcus_demo_register_blocks() {
	wp_register_script(
		'block-hero',
		WCUS_DEMO_TEMPLATE_URL . '/blocks/hero.js',
		[ 'wp-blocks', 'wp-components', 'wp-element', 'wp-editor', 'wp-i18n' ],
		null,
		true
	);
	wp_register_script(
		'block-cta-form',
		WCUS_DEMO_TEMPLATE_URL . '/blocks/cta-form.js',
		[ 'wp-blocks', 'wp-components', 'wp-element', 'wp-editor', 'wp-i18n' ],
		null,
		true
	);

	register_block_type( 'wcus-demo/hero',     [ 'editor_script' => 'block-hero' ] );
	register_block_type( 'wcus-demo/cta-form', [ 'editor_script' => 'block-cta-form' ] );
}

/**
 * Custom walker for BEM nav menu.
 */
class WCUS_Demo_BEM_Nav_Walker extends \Walker_Nav_Menu {
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
