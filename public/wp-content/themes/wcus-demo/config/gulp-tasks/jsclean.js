import gulp from 'gulp';
import del from 'del';
import { dist } from '../index';

const task = 'jsclean';

gulp.task( task, cb => {
	del( [ `${ dist }/js/**/*` ], { force: true } );
	cb();
} );
