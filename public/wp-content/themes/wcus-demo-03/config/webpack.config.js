import path from 'path';
import webpack from 'webpack';
import merge from 'webpack-merge';
import TerserPlugin from 'terser-webpack-plugin';
import { getAssetSrc } from './util';
import { assets, dist, isDev, isProd, theme } from './index';

const publicPath = `/wp-content/themes/${  theme.slug  }/${ path.basename(
	dist,
) }/`;

const optimization = {
	minimizer: [
		new TerserPlugin( {
			cache: true,
			parallel: true,
			sourceMap: false,
			terserOptions: {
				parse: { ecma: 8 },
				compress: {
					ecma: 5,
					warnings: false,
					comparisons: false,
					inline: 2,
				},
				output: {
					ecma: 5,
					comments: false,
				},
				ie8: false,
			},
		} ),
	],
};

/**
 * Create the entry object.
 * @type {Object.<string, string>}
 * @example
 *   entry = {
 *     frontend: './js/frontend/frontend.js',
 *     admin: './js/admin/admin.js',
 *     editor: './js/editor/editor.js',
 *     shared: './js/shared/shared.js',
 *   }
 */
const entry = getAssetSrc( 'js' ).reduce( ( acc, file ) => {
	const entryParts = file.split( '/' );
	const entryDirectory = entryParts
		.slice( Math.max( entryParts.length - 2, 1 ) )
		.join( '/' );
	const filename = path.basename( file );
	const entryName = filename.replace( path.extname( filename ), '' );
	return { ...acc, [ entryName ]: `./js/${ entryDirectory }` };
}, {} );

const configOptions = {
	entry,
	mode: isDev ? 'development' : 'production',
	externals: {
		jquery: 'jQuery',
	},
	output: {
		path: dist,
		publicPath,
		filename: isProd ? '[name].min.js' : '[name].js',
	},
	context: assets + '/',
	cache: true,
	resolve: { modules: [ 'node_modules' ] },
	devtool: 'source-map',
	module: {
		rules: [
			{
				test: /\.js$/,
				enforce: 'pre',
				include: assets,
				loader: 'eslint-loader',
			},
			{
				test: /\.js$/,
				exclude: [ '/node_modules/' ],
				use: [
					{
						loader: 'babel-loader',
						query: { compact: isProd }
					},
				],
			},
		],
	},
	plugins: [ new webpack.NoEmitOnErrorsPlugin() ],
	stats: {
		colors: true,
		warnings: false,
	},
};

export const config = isProd
	? merge( configOptions, { optimization } )
	: configOptions;

export default config;
