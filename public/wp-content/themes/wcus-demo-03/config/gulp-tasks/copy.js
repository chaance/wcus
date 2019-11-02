import gulp from 'gulp';
import notify from 'gulp-notify';
import { assets, dist, successMessage } from '../index';

const task = 'copy';

gulp.task( task, () => {
	return gulp
		.src( `${ assets }/fonts/**/*` )
		.pipe( gulp.dest( `${ dist }/fonts` ) )
		.pipe( notify( { message: successMessage( task ), onLast: true } ) );
} );
