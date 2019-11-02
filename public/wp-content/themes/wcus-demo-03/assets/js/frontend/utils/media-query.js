import throttle from './throttle';
import breakpoints from './breakpoints';

/**
 * Media query event prototype.
 * @param {*} bp
 */
function MediaQuery( bp ) {
	bp = bp || {};
	this.breakpoints = {
		...breakpoints,
		...bp,
	};
	this._state = this.getInitialState();
	this.listener = throttle( this.listener, 100 ).bind( this );
	console.log( this._state );
}

MediaQuery.prototype.getInitialState = function() {
	const { innerWidth: screenWidth, innerHeight: screenHeight } = window;
	const breakpointsAray = Object.keys( this.breakpoints );
	const currentBreakpoint = breakpointsAray.find(
		( size, index, arr ) => screenWidth >= size,
	);
	const currentIndex = breakpointsAray.indexOf( currentBreakpoint );

	const lastBp = currentIndex === breakpointsAray.length - 1;
	const nextBp = lastBp
		? null
		: this.breakpoints[ breakpointsAray[ currentIndex + 1 ] ];
	return {
		screenWidth,
		screenHeight,
		prevMqSize: null,
		mqSize: lastBp
			? screenWidth >= this.breakpoints[ currentBreakpoint ]
			: screenWidth >= this.breakpoints[ currentBreakpoint ] &&
			  screenWidth < nextBp,
	};
};

MediaQuery.prototype.listener = function() {
	const { innerWidth: width, innerHeight: height } = window;
	const prevMqSize = this._state.mqSize;
	const mqSize = Object.keys( this.breakpoints ).find( ( size, index, arr ) => {
		const lastBp = index === arr.length - 1;
		const nextBp = lastBp ? null : this.breakpoints[ arr[ index + 1 ] ];
		return lastBp
			? width >= this.breakpoints[ size ]
			: width >= this.breakpoints[ size ] && width < nextBp;
	} );
	this._state.height = height;
	this._state.width = width;
	if ( mqSize !== prevMqSize ) {
		this._state.mqSize = mqSize;
		this._state.prevMqSize = prevMqSize;
		window.dispatchEvent( this.changeEvent() );
	}
	return;
};

MediaQuery.prototype.changeEvent = function() {
	return new CustomEvent( 'mqChanged', {
		detail: {
			newSize: this._state.mqSize,
			oldSize: this._state.prevMqSize,
		},
	} );
};

export default MediaQuery;
