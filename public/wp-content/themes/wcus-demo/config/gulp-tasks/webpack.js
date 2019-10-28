import gulp from 'gulp';
import pump from 'pump';
import webpack from 'webpack';
import webpackStream from 'webpack-stream';
import notify from 'gulp-notify';
import { getAssetSrc } from '../util';
import { dist, successMessage } from '../index';
import webpackConfig from '../webpack.config';

const task = 'webpack';

gulp.task( task, cb => {
	pump(
		[
			gulp.src( getAssetSrc( 'js' ) ),
			webpackStream( webpackConfig, webpack ),
			gulp.dest( `${ dist }/js` ),
			notify( { message: successMessage( task ), onLast: true } ),
		],
		cb,
	);
} );
