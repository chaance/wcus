import sass from '@chancestrickland/gulp-sass';
import gulp from 'gulp';
import rename from 'gulp-rename';
import sourcemaps from 'gulp-sourcemaps';
import postcss from 'gulp-postcss';
import notify from 'gulp-notify';
import pump from 'pump';
import tildeImporter from 'node-sass-tilde-importer';
import { getAssetSrc } from '../util';
import { dist, isProd, successMessage } from '../index';

const task = 'sass';

const postcssPlugins = [
	require( 'postcss-preset-env' )( {
		stage: 2,
		autoprefixer: {
			grid: true,
		},
	} ),
];

if ( isProd ) {
	postcssPlugins.push(
		require( 'cssnano' )( {
			autoprefixer: false,
			calc: {
				precision: 8,
			},
			colormin: true,
			convertValues: true,
			cssDeclarationSorter: true,
			discardComments: true,
			discardEmpty: true,
			discardOverridden: true,
			mergeLonghand: true,
			mergeRules: true,
			minifyFontValues: true,
			minifyGradients: true,
			minifyParams: true,
			minifySelectors: true,
			normalizeCharset: true,
			normalizePositions: true,
			normalizeRepeatStyle: true,
			normalizeString: true,
			normalizeTimingFunctions: true,
			normalizeUnicode: true,
			normalizeUrl: true,
			normalizeWhitespace: true,
			orderedValues: true,
			reduceTransforms: true,
			svgo: true,
			uniqueSelectors: true,
			zindex: false,
		} ),
	);
}

gulp.task( task, cb => {
	pump(
		[
			gulp.src( getAssetSrc( 'scss' ) ),
			sourcemaps.init( { loadMaps: true } ),
			sass( { importer: tildeImporter } ).on( 'error', sass.logError ),
			postcss( postcssPlugins ),
			rename( { suffix: isProd ? '.min' : '' } ),
			sourcemaps.write( './' ),
			gulp.dest( `${ dist }/css` ),
			notify( { message: successMessage( task ), onLast: true } ),
		],
		cb,
	);
} );
