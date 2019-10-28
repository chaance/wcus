<?php

namespace Chance_Digital\WCUS_Demo\Blocks;

use function Chance_Digital\WCUS_Demo\Theme\Util\get_asset_url;
use function Chance_Digital\WCUS_Demo\Theme\Util\get_asset_version;

defined( 'ABSPATH' ) || exit;

add_action( 'init', __NAMESPACE__ . '\\register_blocks' );

function register_blocks() {
	// Block test
	wp_register_script(
		'block-test',
		get_asset_url( 'js', 'block-test' ),
		[ 'wp-blocks', 'wp-i18n', 'wp-element' ],
		get_asset_version( 'js', 'block-test' ),
		true
	);
	register_block_type( 'wcus-demo/block-test', [
		'editor_script' => 'block-test',
	] );
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'block-test', 'wcus-demo' );
	}
}
