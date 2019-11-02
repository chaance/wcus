import gulp from 'gulp';
import env from 'gulp-env';
import path from 'path';
import requireDir from 'require-dir';
import { assets, baseDir } from './index';

requireDir( './gulp-tasks' );

gulp.task( 'env', cb => {
	try {
		env( { file: `${ baseDir }/.env` } );
		cb();
	} catch ( err ) {
		cb();
	}
} );

gulp.task( 'copyProcess', gulp.series( 'copy' ) );
gulp.task( 'jsProcess', gulp.series( 'jsclean', 'webpack' ) );
gulp.task( 'cssProcess', gulp.series( 'cssclean', 'sass' ) );
gulp.task( 'imageProcess', gulp.series( 'images' ) );

// Watch for file changes.
gulp.task( 'watch', () => {
	gulp.watch(
		[ `../${ path.basename( assets ) }/scss/**/*` ],
		gulp.series( 'cssProcess' ),
	);
	gulp.watch(
		[ `../${ path.basename( assets ) }/js/**/*.js` ],
		gulp.series( 'webpack' ),
	);
	gulp.watch(
		[ `../${ path.basename( assets ) }/img/**/*.{jpg,jpeg,png,gif,svg}` ],
		gulp.series( 'imageProcess' ),
	);
} );

gulp.task(
	'default',
	gulp.parallel(
		'copyProcess',
		'cssProcess',
		gulp.series( 'jsProcess', 'images' ),
	),
);

gulp.task( 'dev', gulp.series( 'env', 'default', 'watch' ) );
gulp.task( 'build', gulp.series( 'default' ) );
