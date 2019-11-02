const breakpoints = {
	small: 0,
	medium: 768,
	large: 992,
	xlarge: 1200,
	xxlarge: 1440,
};

( function( $ ) {
	$( document ).ready( function() {
		// console.log( 'Well hello there! 🍻' );
		const mq = new MediaQuery();
		window.addEventListener( 'resize', mq.listener );

		// Header nav menu
		const $headerNavToggle = $( '#js-header-nav-toggle' );
		const $headerNav = $( '#js-header-nav' );
		const focusableElements = document.querySelectorAll( '#js-header-nav a' );
		const firstFocusableElement = focusableElements[ 0 ];
		const lastFocusableElement =
			focusableElements[ focusableElements.length - 1 ];

		$headerNavToggle.click( toggleHeaderNav );

		if (
			window.matchMedia( `(max-width: ${ breakpoints.large - 1 }px)` ).matches
		) {
			console.log( 'we start small' );
			makeMenuButton();
		}

		$( window ).on( 'mqChanged', function( event ) {
			const { newSize, oldSize } = event.detail;
			console.log( { newSize, oldSize } );
			if (
				breakpoints[ newSize ] < breakpoints.large &&
				breakpoints[ oldSize ] >= breakpoints.large
			) {
				console.log( 'now small' );
				makeMenuButton();
			} else if (
				breakpoints[ newSize ] >= breakpoints.large &&
				breakpoints[ oldSize ] < breakpoints.large
			) {
				$headerNav.removeAttr( 'style' );
				destroyMenuButton();
			}
		} );

		function makeMenuButton() {
			const $menu = $headerNav.find( '.menu' );
			const $menuItems = $headerNav.find( '.menu-item' );
			const $menuLinks = $headerNav.find( '.menu-item a' );
			const menuId = 'js-header-nav-menu';
			$menu.attr( 'id', menuId );
			$menu.attr( 'role', 'menubutton' );
			$menuItems.attr( 'role', 'none' );
			$menuLinks.attr( 'role', 'menuitem' );
			$headerNavToggle.attr( 'tabindex', '0' );
			$headerNavToggle.attr( 'aria-controls', menuId );
		}

		function destroyMenuButton() {
			$headerNavToggle.attr( 'tabindex', '-1' );
			const $menu = $headerNav.find( '.menu' );
			const $menuItems = $headerNav.find( '.menu-item' );
			const $menuLinks = $headerNav.find( '.menu-item a' );

			$menu.removeAttr( 'role' );
			$menuItems.removeAttr( 'role' );
			$menuLinks.removeAttr( 'role' );
		}

		function closeHeaderNav() {
			$headerNavToggle.attr( 'aria-expanded', 'false' );
			$headerNav.fadeOut();
			document.removeEventListener( 'keydown', handleKeyDown );
		}

		function openHeaderNav() {
			$headerNavToggle.attr( 'aria-expanded', 'true' );
			$headerNav.fadeIn();
			firstFocusableElement.focus();
			document.addEventListener( 'keydown', handleKeyDown );
		}

		function toggleHeaderNav() {
			isHeaderNavActive() ? closeHeaderNav() : openHeaderNav();
		}

		function isHeaderNavActive() {
			return $headerNavToggle.attr( 'aria-expanded' ) === 'true' ? true : false;
		}

		function handleKeyDown( event ) {
			if ( event.key === 'Tab' ) {
				event.preventDefault();
			}

			if ( event.key === 'Escape' ) {
				closeHeaderNav();
				$headerNavToggle.focus();
			}

			if ( event.key === 'ArrowDown' ) {
				event.preventDefault();
				console.log( lastFocusableElement );
				console.log( document.activeElement );
				if ( document.activeElement === lastFocusableElement ) {
					firstFocusableElement.focus();
				} else {
					const nextItemIndex =
						Array.from( focusableElements ).indexOf( document.activeElement ) +
						1;
					focusableElements[ nextItemIndex ].focus();
				}
			}

			if ( event.key === 'ArrowUp' ) {
				event.preventDefault();
				if ( document.activeElement === firstFocusableElement ) {
					lastFocusableElement.focus();
					event.preventDefault();
				} else {
					const nextItemIndex =
						Array.from( focusableElements ).indexOf( document.activeElement ) -
						1;
					focusableElements[ nextItemIndex ].focus();
				}
			}
		}
	} );
} )( jQuery );

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

// Utils
function throttle( func, wait, options = {} ) {
	let context;
	let args;
	let result;
	let timeout = null;
	let previous = 0;
	const later = function() {
		previous = options.leading === false ? 0 : Date.now();
		timeout = null;
		result = func.apply( context, args );
		if ( ! timeout ) context = args = null;
	};
	return function() {
		const now = Date.now();
		if ( ! previous && options.leading === false ) previous = now;
		const remaining = wait - ( now - previous );
		context = this;
		args = arguments;
		if ( remaining <= 0 || remaining > wait ) {
			if ( timeout ) {
				clearTimeout( timeout );
				timeout = null;
			}
			previous = now;
			result = func.apply( context, args );
			if ( ! timeout ) context = args = null;
		} else if ( ! timeout && options.trailing !== false ) {
			timeout = setTimeout( later, remaining );
		}
		return result;
	};
}
