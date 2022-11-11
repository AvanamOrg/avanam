/* global baseConfig */
/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */

// polyfill forEach
// https://developer.mozilla.org/en-US/docs/Web/API/NodeList/forEach#Polyfill
if ( window.NodeList && ! NodeList.prototype.forEach ) {
	NodeList.prototype.forEach = function( callback, thisArg ) {
		var i;
		var len = this.length;

		thisArg = thisArg || window;

		for ( i = 0; i < len; i++ ) {
			callback.call( thisArg, this[ i ], i, this );
		}
	};
}
(function() {
	'use strict';
	window.base = {

		/**
		 * Function to init different style of focused element on keyboard users and mouse users.
		 */
		initOutlineToggle: function() {
			document.body.addEventListener( 'keydown', function() {
				document.body.classList.remove( 'hide-focus-outline' );
			});

			document.body.addEventListener( 'mousedown', function() {
				document.body.classList.add( 'hide-focus-outline' );
			});
		},

		/**
		 * Get element's offset.
		 */
		getOffset: function( el ) {
			if ( el instanceof HTMLElement ) {
				var rect = el.getBoundingClientRect();

				return {
					top: rect.top + window.pageYOffset,
					left: rect.left + window.pageXOffset
				}
			}

			return {
				top: null,
				left: null
			};
		},

		/**
		 * traverses the DOM up to find elements matching the query
		 *
		 * @param {HTMLElement} target
		 * @param {string} query
		 * @return {NodeList} parents matching query
		 */
		findParents: function( target, query ) {
			var parents = [];

			// recursively go up the DOM adding matches to the parents array
			function traverse( item ) {
				var parent = item.parentNode;
				if ( parent instanceof HTMLElement ) {
					if ( parent.matches( query ) ) {
						parents.push( parent );
					}
					traverse( parent );
				}
			}

			traverse( target );

			return parents;
		},
		/**
		 * Toggle an attribute.
		 */
		toggleAttribute: function( element, attribute, trueVal, falseVal ) {
			if ( trueVal === undefined ) {
				trueVal = true;
			}
			if ( falseVal === undefined ) {
				falseVal = false;
			}
			if ( element.getAttribute( attribute ) !== trueVal ) {
				element.setAttribute( attribute, trueVal );
			} else {
				element.setAttribute( attribute, falseVal );
			}
		},
		/**
		 * Initiate the script to process all
		 * navigation menus with submenu toggle enabled.
		 */
		initNavToggleSubmenus: function() {
			var navTOGGLE = document.querySelectorAll( '.nav--toggle-sub' );

			// No point if no navs.
			if ( ! navTOGGLE.length ) {
				return;
			}

			for ( let i = 0; i < navTOGGLE.length; i++ ) {
				window.base.initEachNavToggleSubmenu( navTOGGLE[ i ] );
			}
		},
		initEachNavToggleSubmenu: function( nav ) {
			// Get the submenus.
			var SUBMENUS = nav.querySelectorAll( '.menu ul' );
		
			// No point if no submenus.
			if ( ! SUBMENUS.length ) {
				return;
			}
		
			for ( let i = 0; i < SUBMENUS.length; i++ ) {
				var parentMenuItem = SUBMENUS[ i ].parentNode;
				let dropdown = parentMenuItem.querySelector( '.dropdown-nav-toggle' );
				// If dropdown.
				if ( dropdown ) {
					var dropdownBtn = document.createElement( 'BUTTON' ); // Create a <button> element
					dropdownBtn.setAttribute( 'aria-label', baseConfig.screenReader.expand );
					dropdownBtn.classList.add( 'dropdown-nav-special-toggle' );
					parentMenuItem.insertBefore( dropdownBtn, parentMenuItem.childNodes[1] );
					// Toggle the submenu when we click the dropdown button.
					dropdownBtn.addEventListener( 'click', function( e ) {
						e.preventDefault();
						window.base.toggleSubMenu( e.target.parentNode );
					} );
		
					// Clean up the toggle if a mouse takes over from keyboard.
					parentMenuItem.addEventListener( 'mouseleave', function( e ) {
						window.base.toggleSubMenu( e.target, false );
					} );
		
					// When we focus on a menu link, make sure all siblings are closed.
					parentMenuItem.querySelector( 'a' ).addEventListener( 'focus', function( e ) {
						var parentMenuItemsToggled = e.target.parentNode.parentNode.querySelectorAll( 'li.menu-item--toggled-on' );
						for ( let j = 0; j < parentMenuItemsToggled.length; j++ ) {
							window.base.toggleSubMenu( parentMenuItemsToggled[ j ], false );
						}
					} );
		
					// Handle keyboard accessibility for traversing menu.
					SUBMENUS[ i ].addEventListener( 'keydown', function( e ) {
						// These specific selectors help us only select items that are visible.
						var focusSelector = 'ul.toggle-show > li > a, ul.toggle-show > li > .dropdown-nav-special-toggle';
		
						// 9 is tab KeyMap
						if ( 9 === e.keyCode ) {
							if ( e.shiftKey ) {
								// Means we're tabbing out of the beginning of the submenu.
								if ( window.base.isfirstFocusableElement (SUBMENUS[ i ], document.activeElement, focusSelector ) ) {
									window.base.toggleSubMenu( SUBMENUS[ i ].parentNode, false );
								}
								// Means we're tabbing out of the end of the submenu.
							} else if ( window.base.islastFocusableElement( SUBMENUS[ i ], document.activeElement, focusSelector ) ) {
								window.base.toggleSubMenu( SUBMENUS[ i ].parentNode, false );
							}
						}
					} );
		
					SUBMENUS[ i ].parentNode.classList.add( 'menu-item--has-toggle' );
				}
			}
		},
		/**
		 * Toggle submenus open and closed, and tell screen readers what's going on.
		 * @param {Object} parentMenuItem Parent menu element.
		 * @param {boolean} forceToggle Force the menu toggle.
		 * @return {void}
		 */
		 toggleSubMenu: function( parentMenuItem, forceToggle ) {
			var toggleButton = parentMenuItem.querySelector( '.dropdown-nav-special-toggle' ),
				subMenu = parentMenuItem.querySelector( 'ul' );
			let parentMenuItemToggled = parentMenuItem.classList.contains( 'menu-item--toggled-on' );
			// Will be true if we want to force the toggle on, false if force toggle close.
			if ( undefined !== forceToggle && 'boolean' === ( typeof forceToggle ) ) {
				parentMenuItemToggled = ! forceToggle;
			}

			// Toggle aria-expanded status.
			toggleButton.setAttribute( 'aria-expanded', ( ! parentMenuItemToggled ).toString() );

			/*
			* Steps to handle during toggle:
			* - Let the parent menu item know we're toggled on/off.
			* - Toggle the ARIA label to let screen readers know will expand or collapse.
			*/
			if ( parentMenuItemToggled ) {
				// Toggle "off" the submenu.
				parentMenuItem.classList.remove( 'menu-item--toggled-on' );
				subMenu.classList.remove( 'toggle-show' );
				toggleButton.setAttribute( 'aria-label', baseConfig.screenReader.expand );

				// Make sure all children are closed.
				var subMenuItemsToggled = parentMenuItem.querySelectorAll( '.menu-item--toggled-on' );
				for ( let i = 0; i < subMenuItemsToggled.length; i++ ) {
					window.base.toggleSubMenu( subMenuItemsToggled[ i ], false );
				}
			} else {
				// Make sure siblings are closed.
				var parentMenuItemsToggled = parentMenuItem.parentNode.querySelectorAll( 'li.menu-item--toggled-on' );
				for ( let i = 0; i < parentMenuItemsToggled.length; i++ ) {
					window.base.toggleSubMenu( parentMenuItemsToggled[ i ], false );
				}

				// Toggle "on" the submenu.
				parentMenuItem.classList.add( 'menu-item--toggled-on' );
				subMenu.classList.add( 'toggle-show' );
				toggleButton.setAttribute( 'aria-label', baseConfig.screenReader.collapse );
			}
		},
		/**
		 * Returns true if element is the
		 * first focusable element in the container.
		 * @param {Object} container
		 * @param {Object} element
		 * @param {string} focusSelector
		 * @return {boolean} whether or not the element is the first focusable element in the container
		 */
		isfirstFocusableElement: function( container, element, focusSelector ) {
			var focusableElements = container.querySelectorAll( focusSelector );
			if ( 0 < focusableElements.length ) {
				return element === focusableElements[ 0 ];
			}
			return false;
		},

		/**
		 * Returns true if element is the
		 * last focusable element in the container.
		 * @param {Object} container
		 * @param {Object} element
		 * @param {string} focusSelector
		 * @return {boolean} whether or not the element is the last focusable element in the container
		 */
		islastFocusableElement: function( container, element, focusSelector ) {
			var focusableElements = container.querySelectorAll( focusSelector );
			//console.log( focusableElements );
			if ( 0 < focusableElements.length ) {
				return element === focusableElements[ focusableElements.length - 1 ];
			}
			return false;
		},
		/**
		 * Initiate the script to process all drawer toggles.
		 */
		 toggleDrawer: function( element, changeFocus ) {
			changeFocus = (typeof changeFocus !== 'undefined') ?  changeFocus : true;
			var toggle = element;
			var target = document.querySelector( toggle.dataset.toggleTarget );
			var _doc   = document;
			var scrollBar = window.innerWidth - document.documentElement.clientWidth;
			var duration = ( toggle.dataset.toggleDuration ? toggle.dataset.toggleDuration : 250 );
			window.base.toggleAttribute( toggle, 'aria-expanded', 'true', 'false' );
			if ( target.classList.contains('show-drawer') ) {
				if ( toggle.dataset.toggleBodyClass ) {
					_doc.body.classList.remove( toggle.dataset.toggleBodyClass );
				}
				// Hide the drawer.
				target.classList.remove('active');
				target.classList.remove('pop-animated');
				_doc.body.classList.remove( 'base-scrollbar-fixer' );
				setTimeout(function () {
					target.classList.remove('show-drawer');
					if ( toggle.dataset.setFocus && changeFocus ) {
						var focusElement = document.querySelector(toggle.dataset.setFocus);
						if ( focusElement ) {
							focusElement.focus();
							if ( focusElement.hasAttribute( 'aria-expanded') ) {
								window.base.toggleAttribute( focusElement, 'aria-expanded', 'true', 'false' );
							}
						}
					}
				}, duration);
			} else {
				// Show the drawer.
				target.classList.add('show-drawer');
				// Toggle body class
				if ( toggle.dataset.toggleBodyClass ) {
					_doc.body.classList.toggle( toggle.dataset.toggleBodyClass );
					if ( toggle.dataset.toggleBodyClass.includes( 'showing-popup-drawer-' ) ) {
						_doc.body.style.setProperty('--scrollbar-offset', scrollBar + 'px' );
						_doc.body.classList.add( 'base-scrollbar-fixer' );
					}
				}
				setTimeout(function () {
					target.classList.add('active');
					if ( toggle.dataset.setFocus, changeFocus ) {
						var focusElement = document.querySelector(toggle.dataset.setFocus);

						if ( focusElement ) {
							if ( focusElement.hasAttribute( 'aria-expanded') ) {
								window.base.toggleAttribute( focusElement, 'aria-expanded', 'true', 'false' );
							}
							var searchTerm = focusElement.value;
							focusElement.value = '';
							focusElement.focus();
							focusElement.value = searchTerm;
						}
					}
				}, 10);
				setTimeout(function () {
					target.classList.add('pop-animated');
				}, duration);
				// Keep Focus in Modal
				if ( target.classList.contains('popup-drawer') ) {
					// add all the elements inside modal which you want to make focusable
					var focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
					var focusableContent = target.querySelectorAll( focusableElements );
					var firstFocusableElement = focusableContent[0]; // get first element to be focused inside modal
					var lastFocusableElement = focusableContent[ focusableContent.length - 1 ]; // get last element to be focused inside modal

					document.addEventListener( 'keydown', function(e) {
						let isTabPressed = e.key === 'Tab' || e.keyCode === 9;

						if ( ! isTabPressed ) {
							return;
						}

						if ( e.shiftKey ) { // if shift key pressed for shift + tab combination
							if ( document.activeElement === firstFocusableElement ) {
								lastFocusableElement.focus(); // add focus for the last focusable element
								e.preventDefault();
							}
						} else { // if tab key is pressed
							if ( document.activeElement === lastFocusableElement ) { // if focused has reached to last focusable element then focus first focusable element after pressing tab
								firstFocusableElement.focus(); // add focus for the first focusable element
								e.preventDefault();
							}
						}
					});
				}
			}
		},
		/**
		 * Initiate the script to process all
		 * navigation menus with small toggle enabled.
		 */
		 initToggleDrawer: function() {
			var drawerTOGGLE = document.querySelectorAll( '.drawer-toggle' );

			// No point if no drawers.
			if ( ! drawerTOGGLE.length ) {
				return;
			}
			for ( let i = 0; i < drawerTOGGLE.length; i++ ) {
				drawerTOGGLE[ i ].addEventListener('click', function( event ) {
					event.preventDefault();
					window.base.toggleDrawer( drawerTOGGLE[ i ] );
				} );
			}
			// Close Drawer if esc is pressed.
			document.addEventListener( 'keyup', function (event) {
				// 27 is keymap for esc key.
				if ( event.keyCode === 27 ) {
					if ( document.querySelectorAll( '.popup-drawer.show-drawer.active' ) ) {
						event.preventDefault();
						document.querySelectorAll( '.popup-drawer.show-drawer.active' ).forEach(function ( element ) {
							window.base.toggleDrawer( document.querySelector('*[data-toggle-target="' + element.dataset.drawerTargetString + '"]' ) );
						} );
					}
				}
			  });
			// Close modal on outside click.
			document.addEventListener( 'click', function (event) {
				var target = event.target;
				var modal = document.querySelector( '.show-drawer.active .drawer-overlay' );
				if ( target === modal ) {
					window.base.toggleDrawer(document.querySelector('*[data-toggle-target="' + modal.dataset.drawerTargetString + '"]'));
				}
			} );
		},
		/**
		 * Initiate the script to process all
		 * navigation menus with small toggle enabled.
		 */
		 initMobileToggleSub: function() {
			var modalMenus = document.querySelectorAll( '.has-collapse-sub-nav' );

			modalMenus.forEach( function( modalMenu ) {
				var activeMenuItem = modalMenu.querySelector( '.current-menu-item' );
				if ( activeMenuItem ) {
					window.base.findParents( activeMenuItem, 'li' ).forEach( function( element ) {
						var subMenuToggle = element.querySelector( '.drawer-sub-toggle' );
						if ( subMenuToggle ) {
							window.base.toggleDrawer( subMenuToggle, true );
						}
					} );
				}
			} );
			var drawerSubTOGGLE = document.querySelectorAll( '.drawer-sub-toggle' );
			// No point if no drawers.
			if ( ! drawerSubTOGGLE.length ) {
				return;
			}
		
			for ( let i = 0; i < drawerSubTOGGLE.length; i++ ) {
				drawerSubTOGGLE[ i ].addEventListener('click', function (event) {
					event.preventDefault();
					window.base.toggleDrawer( drawerSubTOGGLE[ i ] );
				} );
			}
		},
		/**
		 * Initiate the script to process all
		 * navigation menus check to close mobile.
		 */
		 initMobileToggleAnchor: function() {
			var mobileModal = document.getElementById( 'mobile-drawer' );
			// No point if no drawers.
			if ( ! mobileModal ) {
				return;
			}
			var menuLink = mobileModal.querySelectorAll( 'a' );
			// No point if no links.
			if ( ! menuLink.length ) {
				return;
			}
			for ( let i = 0; i < menuLink.length; i++ ) {
				menuLink[ i ].addEventListener('click', function (event) {
					window.base.toggleDrawer( mobileModal.querySelector( '.menu-toggle-close' ), false );
				} );
			}
		},
		/**
		 * Initiate setting the top padding for hero title when page is transparent.
		 */
		initTransHeaderPadding: function() {
			if ( document.body.classList.contains( 'no-header' ) ) {
				return;
			}
			if ( ! document.body.classList.contains( 'transparent-header' ) || ! document.body.classList.contains( 'mobile-transparent-header' ) ) {
				return;
			}
			var titleHero = document.querySelector( '.entry-hero-container-inner' ),
				header =  document.querySelector( '#masthead' );
			var updateHeroPadding = function( e ) {
				header
				if ( baseConfig.breakPoints.desktop <= window.innerWidth ) {
					if ( ! document.body.classList.contains( 'transparent-header' ) ) {
						titleHero.style.paddingTop = 0;
					} else {
						titleHero.style.paddingTop = header.offsetHeight + 'px';
					}
				} else {
					if ( ! document.body.classList.contains( 'mobile-transparent-header' ) ) {
						titleHero.style.paddingTop = 0;
					} else {
						titleHero.style.paddingTop = header.offsetHeight + 'px';
					}
				}
			}
			if ( titleHero ) {
				window.addEventListener( 'resize', updateHeroPadding, false );
				window.addEventListener( 'scroll', updateHeroPadding, false );
				window.addEventListener( 'load', updateHeroPadding, false );
				updateHeroPadding();
			}
		},
		getTopOffset: function() {
				var activeScrollOffsetTop = 0,
				activeScrollAdminOffsetTop = 0;
			if ( baseConfig.breakPoints.desktop <= window.innerWidth ) {
				if ( document.body.classList.contains( 'admin-bar' ) ) {
					activeScrollAdminOffsetTop = 32;
				}
			} else {
				activeScrollAdminOffsetTop = 0;
			}
			return Math.floor( activeScrollOffsetTop + activeScrollAdminOffsetTop );
		},
		/**
		 * Initiate the sticky sidebar.
		 */
		initStickySidebar: function() {
			if ( ! document.body.classList.contains( 'has-sticky-sidebar' ) ) {
				return;
			}
			var offsetSticky = window.base.getTopOffset(),
			sidebar  = document.querySelector( '#secondary .sidebar-inner-wrap' );
			sidebar.style.top = Math.floor( offsetSticky + 20 ) + 'px';
			sidebar.style.maxHeight = 'calc( 100vh - ' + Math.floor( offsetSticky + 20 ) + 'px )';
		},
		// Initiate the menus when the DOM loads.
		init: function() {
			window.base.initNavToggleSubmenus();
			window.base.initToggleDrawer();
			window.base.initMobileToggleAnchor();
			window.base.initMobileToggleSub();
			window.base.initOutlineToggle();
			window.base.initStickySidebar();
			window.base.initTransHeaderPadding();
		}
	}
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', window.base.init );
	} else {
		// The DOM has already been loaded.
		window.base.init();
	}
})();
