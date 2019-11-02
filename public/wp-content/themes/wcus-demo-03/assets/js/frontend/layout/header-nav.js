import MediaQuery from '../utils/media-query';
import breakpoints from '../utils/breakpoints';

export default function( $ ) {
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
					Array.from( focusableElements ).indexOf( document.activeElement ) + 1;
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
					Array.from( focusableElements ).indexOf( document.activeElement ) - 1;
				focusableElements[ nextItemIndex ].focus();
			}
		}
	}
}
