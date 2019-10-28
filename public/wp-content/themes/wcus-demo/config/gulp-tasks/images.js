import gulp from 'gulp';
import cache from 'gulp-cache';
import imagemin from 'gulp-imagemin';
import notify from 'gulp-notify';
import imageminJpegRecompress from 'imagemin-jpeg-recompress';
import { assets, dist, successMessage } from '../index';

const task = 'images';

gulp.task( task, () => {
	return gulp
		.src( `${ assets }/img/**/*.{jpg,jpeg,png,gif,svg}` )
		.pipe(
			cache(
				imagemin( [
					imageminJpegRecompress( { accurate: true, quality: 'high' } ),
					imagemin.gifsicle( { interlaced: true } ),
					imagemin.optipng( { optimizationLevel: 5 } ),
					imagemin.svgo( {
						plugins: [ { removeViewBox: false }, { cleanupIDs: true } ],
					} ),
				] ),
			),
		)
		.pipe( gulp.dest( `${ dist }/img` ) )
		.pipe( notify( { message: successMessage( task ), onLast: true } ) );
} );
