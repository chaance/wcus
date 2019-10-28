import gulp from 'gulp';
import del from 'del';
import { dist } from '../index';

const task = 'cssclean';

gulp.task( task, cb => {
	del( [ `${ dist }/css/**/*` ], { force: true } );
	cb();
} );
