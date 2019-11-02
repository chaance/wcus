<?php
/**
 * Plugin Name: WCUS Demo Blocks
 * Plugin URI:  https://wordpress.org
 * Description: Adds custom editor blocks for the WCUS Demo site.
 * Version:     1.0
 * Author:      Chance Strickland
 * Text Domain: wcus-demo
 *
 * @package WCUS_Demo
 * @subpackage Blocks
 */

namespace Chance_Digital\WCUS_Demo\Blocks;

defined( 'ABSPATH' ) || exit;

define( 'WCUS_DEMO_BLOCKS_DIR', plugin_dir_path( __FILE__ ) );
define( 'WCUS_DEMO_BLOCKS_URL', plugin_dir_url( __FILE__ ) );

add_action( 'init', __NAMESPACE__ . '\\register_blocks' );

function register_blocks() {
	wp_register_script(
		'block-hero',
		plugins_url( 'blocks/hero/index.js', __FILE__ ),
		[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n' ],
		null,
		true
	);
	wp_register_script(
		'block-cta-form',
		plugins_url( 'blocks/cta-form/index.js', __FILE__ ),
		[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n' ],
		null,
		true
	);

	register_block_type( 'wcus-demo/hero',     [ 'editor_script' => 'block-hero' ] );
	register_block_type( 'wcus-demo/cta-form', [ 'editor_script' => 'block-cta-form' ] );
}
