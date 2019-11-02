module.exports = function( api ) {
	const isTestEnv = api.env() === 'test';

	const presets = [
		[
			'@babel/preset-env',
			{
				targets: isTestEnv
					? { node: 'current' }
					: require( '@chancedigital/browserslist-config' ),
			},
		],
		'@babel/preset-react',
	];
	const plugins = [
		'@babel/plugin-proposal-class-properties',
		'@babel/plugin-proposal-optional-chaining',
		[
			'@wordpress/babel-plugin-import-jsx-pragma',
			{
				scopeVariable: 'createElement',
				scopeVariableFrag: 'Fragment',
				source: '@wordpress/element',
				isDefault: false,
			},
		],
		[
			'@babel/plugin-transform-react-jsx',
			{
				pragma: 'createElement',
				pragmaFrag: 'Fragment',
			},
		],
	];
	return {
		presets,
		plugins,
	};
};
