import initHeaderNav from './layout/header-nav';

( function( $ ) {
	$( document ).ready( function() {
		console.log( 'Well hello there, front-end! 🍻' );
		$( document ).ready( function() {
			initHeaderNav( $ );
		} );
	} );
} )( jQuery );
