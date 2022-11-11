/* global tns, baseSlideConfig */
/**
 * File slide-init.js.
 * Gets slide going when needed.
 */

(function() {
	'use strict';
	window.baseSlide = {
		/**
		 * Initiate the script to process all
		 */
		start: function( element ) {
			var sliderDirection = 'ltr',
			nextLabel = element.getAttribute('data-slider-next-label'),
			prevLabel = element.getAttribute('data-slider-prev-label'),
			slideLabel = element.getAttribute('data-slider-slide-label'),
			sliderSpeed = parseInt( element.getAttribute( 'data-slider-speed' ) ),
			sliderAnimationSpeed = parseInt( element.getAttribute( 'data-slider-anim-speed' ) ),
			sliderArrows = element.getAttribute( 'data-slider-arrows' ),
			sliderDots = element.getAttribute( 'data-slider-dots' ),
			sliderPause = element.getAttribute( 'data-slider-pause-hover' ),
			sliderLoop = element.getAttribute( 'data-slider-loop' ),
			sliderAuto = element.getAttribute( 'data-slider-auto' ),
			xxl = parseInt( element.getAttribute( 'data-columns-xxl' ) ),
			xl = parseInt( element.getAttribute( 'data-columns-xl' ) ),
			md = parseInt( element.getAttribute( 'data-columns-md' ) ),
			sm = parseInt( element.getAttribute( 'data-columns-sm' ) ),
			xs = parseInt( element.getAttribute( 'data-columns-xs' ) ),
			ss = parseInt( element.getAttribute( 'data-columns-ss' ) ),
			gutter = parseInt( element.getAttribute( 'data-slider-gutter' ) ),
			scroll = parseInt( element.getAttribute( 'data-slider-scroll' ) );
			if ( ! nextLabel ) {
				nextLabel = baseSlideConfig.next;
			}
			if ( ! prevLabel ) {
				prevLabel = baseSlideConfig.next;
			}
			if ( ! slideLabel ) {
				slideLabel = baseSlideConfig.slide;
			}
			if ( document.querySelector( 'html[dir="rtl"]' ) ) {
				sliderDirection = 'rtl';
			}
			if ( 1 !== scroll ) {
				scroll = 'page'
			}
			var scrollSxxl = xxl,
				scrollSxl = xl,
				scrollSmd = md,
				scrollSsm = sm,
				scrollSxs = xs,
				scrollSss = ss;
			if ( 1 === scroll ) {
				scrollSxxl = 1;
				scrollSxl = 1;
				scrollSmd = 1;
				scrollSsm = 1;
				scrollSxs = 1;
				scrollSss = 1;
			}
			var initialize = false;
			var slideCount = element.querySelector( '.splide__list' ).childElementCount;
			if ( window.innerWidth < 544 ) {
				if ( slideCount > ss ) {
					initialize = true;
				}
			} else if ( window.innerWidth < 768 ) {
				if ( slideCount > xs ) {
					initialize = true;
				}
			} else if ( window.innerWidth < 992 ) {
				if (  slideCount > sm ) {
					initialize = true;
				}
			} else if ( window.innerWidth < 1200 ) {
				if ( slideCount > md ) {
					initialize = true;
				}
			} else if ( window.innerWidth < 1500 ) {
				if ( slideCount > xl ) {
					initialize = true;
				}
			} else if ( slideCount > xxl ) {
				initialize = true;
			}
			if ( initialize ) {
				element.classList.add( 'splide-initial' );
			}
			var options = {
				perPage: xxl,
				type: ( 'false' === sliderLoop ? 'slide' : 'loop' ),
				slideFocus: false,
				perMove: scrollSxxl,
				autoplay: ( sliderAuto == 'false' ? false : true ),
				easing: undefined !== sliderAnimationSpeed && sliderAnimationSpeed > 1000 ? 'linear' : 'cubic-bezier(0.25, 1, 0.5, 1)',
				speed: ( undefined !== sliderAnimationSpeed ? sliderAnimationSpeed : 400 ),
				interval: ( undefined !== sliderSpeed ? sliderSpeed : 7000 ),
				autoplayHoverPause: ( 'true' === sliderPause ? true : false ),
				arrows: ( sliderArrows == 'false' ? false : true ),
				pagination: ( sliderDots == 'false' ? false : true ),
				gap: gutter + 'px',
				direction: sliderDirection,
				rewind:( sliderLoop == 'false' ? true : false ),
				focus: 0,
				perMove: scrollSxxl,
				i18n: {
					carousel: slideLabel,
					prev: prevLabel,
					next: nextLabel,
					slideLabel: '%s ' + baseSlideConfig.of + ' %s',
				},
				breakpoints: {
					543: {
						perPage: ss,
						perMove: scrollSss,
					},
					767: {
						perPage: xs,
						perMove: scrollSxs,
					},
					991: {
						perPage: sm,
						perMove: scrollSsm,
					},
					1199: {
						perPage: md,
						perMove: scrollSmd,
					},
					1499: {
						perPage: xl,
						perMove: scrollSxl,
					}
				}
			};
			var slider = new Splide( element, options );
			if ( initialize ) {
				slider.mount();
			}
		},
		/**
		 * Initiate the script to process all
		 */
		initAll: function( element ) {
			document.querySelectorAll( '.base-slide-init' ).forEach(function ( element ) {
				window.baseSlide.start( element );
			} );
		},
		// Initiate the menus when the DOM loads.
		init: function() {
			if ( typeof Splide == 'function' ) {
				window.baseSlide.initAll();
			} else {
				var initLoadDelay = setInterval( function(){ if ( typeof Splide == 'function' ) { window.baseSlide.initAll(); clearInterval(initLoadDelay); } }, 200 );
			}
		}
	}
	if ( 'loading' === document.readyState ) {
		// The DOM has not yet been loaded.
		document.addEventListener( 'DOMContentLoaded', window.baseSlide.init );
	} else {
		// The DOM has already been loaded.
		window.baseSlide.init();
	}
})();
