import React from 'react';
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

/**
 * Register: Sample Editor Block.
 * @link https://github.com/ahmadawais/create-guten-block
 *
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'wcus-demo/block-test', {
	title: __( 'block-test - WCUS Demo', 'wcus-demo' ),
	icon: 'shield', // https://developer.wordpress.org/resource/dashicons/.
	category: 'common',
	keywords: [
		__( 'block-test — WCUS Demo', 'wcus-demo' ),
		__( 'Sample Editor Block', 'wcus-demo' ),
	],

	edit: function( { className } ) {
		return (
			<div className={ className }>
				<p>— Hello from the backend.</p>
			</div>
		);
	},

	save: function( { className } ) {
		return (
			<div className={ className }>
				<p>— Hello from the frontend.</p>
			</div>
		);
	},
} );
