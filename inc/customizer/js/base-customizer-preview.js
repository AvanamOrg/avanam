( function( $ ) {
	'use strict';
	var $window = $( window ),
		$document = $( document ),
		$body = $( 'body' );
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );
	wp.customize.bind( 'preview-ready', function() {
		var defaultTarget = window.parent === window ? null : window.parent;
		$document.on(
			'click',
			'.site-header-focus-item .item-customizer-focus, .builder-item-focus .edit-row-action',
			function(e) {
				e.preventDefault();
				e.stopPropagation();
				var p = $( this ).closest( '.site-header-focus-item' );
				var section_id = p.attr( 'data-section' ) || '';
				if ( section_id ) {
					if ( defaultTarget.wp.customize.section( section_id ) ) {
						defaultTarget.wp.customize.section( section_id ).focus();
					}
				}
			}
		);
		$document.on(
			'click',
			'.site-footer-focus-item .item-customizer-focus',
			function(e) {
				e.preventDefault();
				e.stopPropagation();
				var p = $( this ).closest( '.site-footer-focus-item' );
				var section_id = p.attr( 'data-section' ) || '';
				if ( section_id ) {
					if ( defaultTarget.wp.customize.section( section_id ) ) {
						defaultTarget.wp.customize.section( section_id ).focus();
					}
				}
			}
		);
	} );
	document.addEventListener('DOMContentLoaded', function () {
		if ( navigator.userAgent.toLowerCase().indexOf('safari/') != -1 ) {
			if ( navigator.userAgent.toLowerCase().indexOf('chrome') > -1) {
			} else {
				// Safari doesn't want to render the iframe... This hack at least makes it render although it's not idea because of the flash.
				// $body.animate({
				// 	opacity: 0,
				//   }, 50, function() {
				// 	$body.css( 'display', 'none' );
				// 	$body.css( 'opacity', 1 );
				// });
				// setTimeout(function(){
				// 	$body.css( 'display', 'block' );
				// }, 100);
			}
		}
		var hasSelectiveRefresh = (
			'undefined' !== typeof wp &&
			wp.customize &&
			wp.customize.selectiveRefresh &&
			wp.customize.widgetsPreview &&
			wp.customize.widgetsPreview.WidgetPartial
		);
		if ( hasSelectiveRefresh ) {
			wp.customize.selectiveRefresh.bind('partial-content-rendered', function (placement) {
				if ( placement.partial.id === 'header_desktop_items' ) {
					window.base.initToggleDrawer();
				}
				window.base.initTransHeaderPadding();
				if ( typeof window.base.initStickyHeader !== "undefined" ) { 
					window.base.initStickyHeader();
					window.base.initScrollToTop();
				}
			} );
		}
	});
	var baseCustomizer = {
		live_css_typography : function( key, rules, newValue ) {
			var styleID = 'base-customize-preview-css-' + key,
			    $style = $( '#' + styleID ),
				css = '',
				media_tablet = '@media screen and (max-width: 1023px)',
				media_mobile = '@media screen and (max-width: 499px)',
				selector,
			    cssArray = {};

			// Create <style> tag if doesn't exist.
			if ( 0 === $style.length ) {
				$style = $( document.createElement( 'style' ) );
				$style.attr( 'id', styleID );
				$style.attr( 'type', 'text/css' );

				// Append <style> tag to <head>.
				$style.appendTo( $( 'head' ) );
			}
			_.each( rules, function( rule ) {
				if ( undefined == rule['property'] || undefined == rule['selector'] ) {
					return;
				}
				rule['media'] = rule['media'] || 'global';
				rule['pattern'] = rule['pattern'] || '$';
				if ( 'object' == typeof newValue ) {
					if ( undefined !== newValue['size'] && 'object' == typeof newValue['size'] && 'family' !== rule['key'] ) {
						if ( undefined !== newValue['size']['desktop'] && '' !== newValue['size']['desktop'] ) {
							selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
							// Define properties.
							if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
							if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
							var unit = ( undefined !== newValue['sizeType'] && '' !== newValue['sizeType'] ? newValue['sizeType'] : 'px' );
							cssArray[ rule['media'] ][ selector ]['font-size'] = newValue['size']['desktop'] + unit;
						}
						if ( undefined !== newValue['size']['tablet'] && '' !== newValue['size']['tablet'] ) {
							selector = ( undefined !== rule['selector']['tablet'] ? rule['selector']['tablet'] : rule['selector'] );
							// Define properties.
							if ( undefined == cssArray[ media_tablet ] ) cssArray[ media_tablet ] = {};
							if ( undefined == cssArray[ media_tablet ][ selector ] ) cssArray[ media_tablet ][ selector ] = {};
							var unit = ( undefined !== newValue['sizeType'] && '' !== newValue['sizeType'] ? newValue['sizeType'] : 'px' );
							cssArray[ media_tablet ][ selector ]['font-size'] = newValue['size']['tablet'] + unit;
						}
						if ( undefined !== newValue['size']['mobile'] && '' !== newValue['size']['mobile'] ) {
							selector = ( undefined !== rule['selector']['mobile'] ? rule['selector']['mobile'] : rule['selector'] );
							// Define properties.
							if ( undefined == cssArray[ media_mobile ] ) cssArray[ media_mobile ] = {};
							if ( undefined == cssArray[ media_mobile ][ selector ] ) cssArray[ media_mobile ][ selector ] = {};
							var unit = ( undefined !== newValue['sizeType'] && '' !== newValue['sizeType'] ? newValue['sizeType'] : 'px' );
							cssArray[ media_mobile ][ selector ]['font-size'] = newValue['size']['mobile'] + unit;
						}
					}
					if ( undefined !== newValue['lineHeight'] && 'object' == typeof newValue['lineHeight'] && 'family' !== rule['key'] ) {
						if ( undefined !== newValue['lineHeight']['desktop'] && '' !== newValue['lineHeight']['desktop'] ) {
							selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
							// Define properties.
							if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
							if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
							var unit = ( undefined !== newValue['lineType'] && '' !== newValue['lineType'] && '-' !== newValue['lineType'] ? newValue['lineType'] : '' );
							cssArray[ rule['media'] ][ selector ]['line-height'] = newValue['lineHeight']['desktop'] + unit;
						}
						if ( undefined !== newValue['lineHeight']['tablet'] && '' !== newValue['lineHeight']['tablet'] ) {
							selector = ( undefined !== rule['selector']['tablet'] ? rule['selector']['tablet'] : rule['selector'] );
							// Define properties.
							if ( undefined == cssArray[ media_tablet ] ) cssArray[ media_tablet ] = {};
							if ( undefined == cssArray[ media_tablet ][ selector ] ) cssArray[ media_tablet ][ selector ] = {};
							var unit = ( undefined !== newValue['lineType'] && '' !== newValue['lineType'] && '-' !== newValue['lineType'] ? newValue['lineType'] : '' );
							cssArray[ media_tablet ][ selector ]['line-height'] = newValue['lineHeight']['tablet'] + unit;
						}
						if ( undefined !== newValue['lineHeight']['mobile'] && '' !== newValue['lineHeight']['mobile'] ) {
							selector = ( undefined !== rule['selector']['mobile'] ? rule['selector']['mobile'] : rule['selector'] );
							// Define properties.
							if ( undefined == cssArray[ media_mobile ] ) cssArray[ media_mobile ] = {};
							if ( undefined == cssArray[ media_mobile ][ selector ] ) cssArray[ media_mobile ][ selector ] = {};
							var unit = ( undefined !== newValue['lineType'] && '' !== newValue['lineType'] && '-' !== newValue['lineType'] ? newValue['lineType'] : '' );
							cssArray[ media_mobile ][ selector ]['line-height'] = newValue['lineHeight']['mobile'] + unit;
						}
					}
					if ( undefined !== newValue['letterSpacing'] && 'object' == typeof newValue['letterSpacing'] && 'family' !== rule['key']) {
						if ( undefined !== newValue['letterSpacing']['desktop'] && '' !== newValue['letterSpacing']['desktop'] ) {
							selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
							// Define properties.
							if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
							if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
							var unit = ( undefined !== newValue['spacingType'] && '' !== newValue['spacingType'] ? newValue['spacingType'] : '' );
							cssArray[ rule['media'] ][ selector ]['letter-spacing'] = newValue['letterSpacing']['desktop'] + unit;
						}
						if ( undefined !== newValue['letterSpacing']['tablet'] && '' !== newValue['letterSpacing']['tablet'] ) {
							selector = ( undefined !== rule['selector']['tablet'] ? rule['selector']['tablet'] : rule['selector'] );
							// Define properties.
							if ( undefined == cssArray[ media_tablet ] ) cssArray[ media_tablet ] = {};
							if ( undefined == cssArray[ media_tablet ][ selector ] ) cssArray[ media_tablet ][ selector ] = {};
							var unit = ( undefined !== newValue['spacingType'] && '' !== newValue['spacingType'] ? newValue['spacingType'] : '' );
							cssArray[ media_tablet ][ selector ]['letter-spacing'] = newValue['letterSpacing']['tablet'] + unit;
						}
						if ( undefined !== newValue['letterSpacing']['mobile'] && '' !== newValue['letterSpacing']['mobile'] ) {
							selector = ( undefined !== rule['selector']['mobile'] ? rule['selector']['mobile'] : rule['selector'] );
							// Define properties.
							if ( undefined == cssArray[ media_mobile ] ) cssArray[ media_mobile ] = {};
							if ( undefined == cssArray[ media_mobile ][ selector ] ) cssArray[ media_mobile ][ selector ] = {};
							var unit = ( undefined !== newValue['spacingType'] && '' !== newValue['spacingType'] ? newValue['spacingType'] : '' );
							cssArray[ media_mobile ][ selector ]['letter-spacing'] = newValue['letterSpacing']['mobile'] + unit;
						}
					}
					if ( undefined !== newValue['family'] && '' !== newValue['family'] ) {
						selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
						if ( undefined !== newValue['google'] && newValue['google'] && typeof (WebFont) !== 'undefined' && WebFont ) {
							var link = newValue['family'];
							if ( 'family' === rule['key'] && newValue['variant'] ) {
								link += ':' + newValue['variant'].toString();
							} else if ( newValue['variant'] && '' !== newValue['variant'] ) {
								link += ':' + newValue['variant'];
							}
							WebFont.load( {google: {families: [link]}} );
						}
						cssArray[ rule['media'] ][ selector ]['font-family'] = ( 'inherit' !== newValue['family'] ? newValue['family'] : '' );
					}
					if ( undefined !== newValue['style'] && 'family' !== rule['key'] ) {
						selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
						cssArray[ rule['media'] ][ selector ]['font-style'] = newValue['style'];
					}
					if ( undefined !== newValue['weight'] && 'family' !== rule['key'] ) {
						selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
						cssArray[ rule['media'] ][ selector ]['font-weight'] = newValue['weight'];
					}
					if ( undefined !== newValue['transform'] && '' !== newValue['transform'] && 'family' !== rule['key'] ) {
						selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
						cssArray[ rule['media'] ][ selector ]['text-transform'] = newValue['transform'];
					}
					if ( undefined !== newValue['color'] && 'family' !== rule['key'] ) {
						selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
						var color_type = '';
						if ( newValue['color'].includes('palette') ) {
							color_type = 'var(--global-' + newValue['color'] + ')';
						} else {
							color_type = newValue['color']
						}
						cssArray[ rule['media'] ][ selector ]['color'] = color_type;
					}
				}
			} );
			// Loop into the sorted array to build CSS string.
			_.each( cssArray, function( selectors, media ) {
				if ( 'global' !== media ) css += media + '{';
				_.each( selectors, function( properties, selector ) {
					css += selector + '{';
					_.each( properties, function( value, property ) {
						css += property + ':' + value + ';';
					});
					css += '}';
				});
				if ( 'global' !== media ) css += '}';
			});

			// Add CSS string to <style> tag.
			$style.html( css );
		},
		live_css_background : function( key, rules, newValue ) {
			var styleID = 'base-customize-preview-css-' + key,
			    $style = $( '#' + styleID ),
				css = '',
				media_tablet = '@media screen and (max-width: 1023px)',
				media_mobile = '@media screen and (max-width: 499px)',
				selector,
			    cssArray = {};

			// Create <style> tag if doesn't exist.
			if ( 0 === $style.length ) {
				$style = $( document.createElement( 'style' ) );
				$style.attr( 'id', styleID );
				$style.attr( 'type', 'text/css' );

				// Append <style> tag to <head>.
				$style.appendTo( $( 'head' ) );
			}
			_.each( rules, function( rule ) {
				if ( undefined == rule['property'] || undefined == rule['selector'] ) {
					return;
				}
				rule['media'] = rule['media'] || 'global';
				rule['pattern'] = rule['pattern'] || '$';
				if ( 'object' == typeof newValue ) {
					if ( undefined !== newValue['desktop'] ) {
						selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
						var background_type = ( undefined !== newValue['desktop']['type'] && '' !== newValue['desktop']['type'] ? newValue['desktop']['type'] : 'color' );
						var color_type = '';
						if ( undefined !== newValue['desktop']['color'] && '' !== newValue['desktop']['color'] ) {
							if ( newValue['desktop']['color'].includes('palette') ) {
								color_type = 'var(--global-' + newValue['desktop']['color'] + ')';
							} else {
								color_type = newValue['desktop']['color']
							}
						}
						if ( 'image' === background_type ) {
							var imageValue = ( undefined !== newValue['desktop']['image'] && undefined !== newValue['desktop']['image']['url'] && '' !== newValue['desktop']['image']['url'] ? 'url(' + newValue['desktop']['image']['url'] + ')' : false );
							if ( imageValue ) {
								cssArray[ rule['media'] ][ selector ]['background-color'] = color_type;
								cssArray[ rule['media'] ][ selector ]['background-image'] = imageValue;
								cssArray[ rule['media'] ][ selector ]['background-repeat'] = ( undefined !== newValue['desktop']['image']['repeat'] && '' !== newValue['desktop']['image']['repeat'] ? newValue['desktop']['image']['repeat'] : 'inherit' );
								cssArray[ rule['media'] ][ selector ]['background-size'] = ( undefined !== newValue['desktop']['image']['size'] && '' !== newValue['desktop']['image']['size'] ? newValue['desktop']['image']['size'] : 'inherit' );
								cssArray[ rule['media'] ][ selector ]['background-position'] = ( undefined !== newValue['desktop']['image']['position'] && undefined !== newValue['desktop']['image']['position']['x'] && '' !== newValue['desktop']['image']['position']['x'] ? `${ newValue['desktop']['image']['position']['x'] * 100 }% ${ newValue['desktop']['image']['position']['y'] * 100 }%` : 'center' );
								cssArray[ rule['media'] ][ selector ]['background-attachment'] = ( undefined !== newValue['desktop']['image']['attachment'] && '' !== newValue['desktop']['image']['attachment'] ? newValue['desktop']['image']['attachment'] : 'inherit' );
							} else {
								if ( '' !== color_type ) {
									cssArray[ rule['media'] ][ selector ]['background'] = color_type;
								}
							}
						} else if ( 'gradient' === background_type ) {
							var gradientValue = ( undefined !== newValue['desktop']['gradient'] && '' !== newValue['desktop']['gradient'] ? newValue['desktop']['gradient'] : false );
							if ( '' !== gradientValue ) {
								cssArray[ rule['media'] ][ selector ]['background'] = gradientValue;
							}
						} else {
							if ( '' !== color_type ) {
								cssArray[ rule['media'] ][ selector ]['background'] = color_type;
							}
						}
					}
					if ( undefined !== newValue['tablet'] ) {
						selector = ( undefined !== rule['selector']['tablet'] ? rule['selector']['tablet'] : rule['selector'] );
						// Define properties.
						if ( undefined == cssArray[ media_tablet ] ) cssArray[ media_tablet ] = {};
						if ( undefined == cssArray[ media_tablet ][ selector ] ) cssArray[ media_tablet ][ selector ] = {};
						var background_type = ( undefined !== newValue['tablet']['type'] && '' !== newValue['tablet']['type'] ? newValue['tablet']['type'] : 'color' );
						var color_type = '';
						if ( undefined !== newValue['tablet']['color'] && '' !== newValue['tablet']['color'] ) {
							if ( newValue['tablet']['color'].includes('palette') ) {
								color_type = 'var(--global-' + newValue['tablet']['color'] + ')';
							} else {
								color_type = newValue['tablet']['color']
							}
						}
						if ( 'image' === background_type ) {
							var imageValue = ( undefined !== newValue['tablet']['image'] && undefined !== newValue['tablet']['image']['url'] && '' !== newValue['tablet']['image']['url'] ? 'url(' + newValue['tablet']['image']['url'] + ')' : false );
							if ( imageValue ) {
								cssArray[ media_tablet ][ selector ]['background-color'] = color_type;
								cssArray[ media_tablet ][ selector ]['background-image'] = imageValue;
								cssArray[ media_tablet ][ selector ]['background-repeat'] = ( undefined !== newValue['tablet']['image']['repeat'] && '' !== newValue['tablet']['image']['repeat'] ? newValue['tablet']['image']['repeat'] : 'inherit' );
								cssArray[ media_tablet ][ selector ]['background-size'] = ( undefined !== newValue['tablet']['image']['size'] && '' !== newValue['tablet']['image']['size'] ? newValue['tablet']['image']['size'] : 'inherit' );
								cssArray[ media_tablet ][ selector ]['background-position'] = ( undefined !== newValue['tablet']['image']['position'] && undefined !== newValue['tablet']['image']['position']['x'] && '' !== newValue['tablet']['image']['position']['x'] ? `${ newValue['tablet']['image']['position']['x'] * 100 }% ${ newValue['tablet']['image']['position']['y'] * 100 }%` : 'center' );
								cssArray[ media_tablet ][ selector ]['background-attachment'] = ( undefined !== newValue['tablet']['image']['attachment'] && '' !== newValue['tablet']['image']['attachment'] ? newValue['tablet']['image']['attachment'] : 'inherit' );
							} else {
								if ( '' !== color_type ) {
									cssArray[ media_tablet ][ selector ]['background'] = color_type;
								}
							}
						} else if ( 'gradient' === background_type ) {
							var gradientValue = ( undefined !== newValue['tablet']['gradient'] && '' !== newValue['tablet']['gradient'] ? newValue['tablet']['gradient'] : false );
							if ( '' !== gradientValue ) {
								cssArray[ rule['media'] ][ selector ]['background'] = gradientValue;
							}
						} else {
							if ( '' !== color_type ) {
								cssArray[ media_tablet ][ selector ]['background'] = color_type;
							}
						}
					}
					if ( undefined !== newValue['mobile'] ) {
						selector = ( undefined !== rule['selector']['mobile'] ? rule['selector']['mobile'] : rule['selector'] );
						// Define properties.
						if ( undefined == cssArray[ media_mobile ] ) cssArray[ media_mobile ] = {};
						if ( undefined == cssArray[ media_mobile ][ selector ] ) cssArray[ media_mobile ][ selector ] = {};
						var background_type = ( undefined !== newValue['mobile']['type'] && '' !== newValue['mobile']['type'] ? newValue['mobile']['type'] : 'color' );
						var color_type = '';
						if ( undefined !== newValue['mobile']['color'] && '' !== newValue['mobile']['color'] ) {
							if ( newValue['mobile']['color'].includes('palette') ) {
								color_type = 'var(--global-' + newValue['mobile']['color'] + ')';
							} else {
								color_type = newValue['mobile']['color']
							}
						}
						if ( 'image' === background_type ) {
							var imageValue = ( undefined !== newValue['mobile']['image'] && undefined !== newValue['mobile']['image']['url'] && '' !== newValue['mobile']['image']['url'] ? 'url(' + newValue['mobile']['image']['url'] + ')' : false );
							if ( imageValue ) {
								cssArray[ media_mobile ][ selector ]['background-color'] = color_type;
								cssArray[ media_mobile ][ selector ]['background-image'] = imageValue;
								cssArray[ media_mobile ][ selector ]['background-repeat'] = ( undefined !== newValue['mobile']['image']['repeat'] && '' !== newValue['mobile']['image']['repeat'] ? newValue['mobile']['image']['repeat'] : 'inherit' );
								cssArray[ media_mobile ][ selector ]['background-size'] = ( undefined !== newValue['mobile']['image']['size'] && '' !== newValue['mobile']['image']['size'] ? newValue['mobile']['image']['size'] : 'inherit' );
								cssArray[ media_mobile ][ selector ]['background-position'] = ( undefined !== newValue['mobile']['image']['position'] && undefined !== newValue['mobile']['image']['position']['x'] && '' !== newValue['mobile']['image']['position']['x'] ? `${ newValue['mobile']['image']['position']['x'] * 100 }% ${ newValue['mobile']['image']['position']['y'] * 100 }%` : 'inherit' );
								cssArray[ media_mobile ][ selector ]['background-attachment'] = ( undefined !== newValue['mobile']['image']['attachment'] && '' !== newValue['mobile']['image']['attachment'] ? newValue['mobile']['image']['attachment'] : 'inherit' );
							} else {
								if ( '' !== color_type ) {
									cssArray[ media_mobile ][ selector ]['background'] = color_type;
								}
							}
						} else if ( 'gradient' === background_type ) {
							var gradientValue = ( undefined !== newValue['mobile']['gradient'] && '' !== newValue['mobile']['gradient'] ? newValue['mobile']['gradient'] : false );
							if ( '' !== gradientValue ) {
								cssArray[ rule['media'] ][ selector ]['background'] = gradientValue;
							}
						} else {
							if ( '' !== color_type ) {
								cssArray[ media_mobile ][ selector ]['background'] = color_type;
							}
						}
					}
				}
			} );
			// Loop into the sorted array to build CSS string.
			_.each( cssArray, function( selectors, media ) {
				if ( 'global' !== media ) css += media + '{';
				_.each( selectors, function( properties, selector ) {
					css += selector + '{';
					_.each( properties, function( value, property ) {
						css += property + ':' + value + ';';
					});
					css += '}';
				});
				if ( 'global' !== media ) css += '}';
			});

			// Add CSS string to <style> tag.
			$style.html( css );
		},
		live_css_border : function( key, rules, newValue ) {
			var styleID = 'base-customize-preview-css-' + key,
			    $style = $( '#' + styleID ),
				css = '',
				media_tablet = '@media screen and (max-width: 1023px)',
				media_mobile = '@media screen and (max-width: 499px)',
				selector,
				property,
			    cssArray = {};
			// Create <style> tag if doesn't exist.
			if ( 0 === $style.length ) {
				$style = $( document.createElement( 'style' ) );
				$style.attr( 'id', styleID );
				$style.attr( 'type', 'text/css' );

				// Append <style> tag to <head>.
				$style.appendTo( $( 'head' ) );
			}
			_.each( rules, function( rule ) {
				if ( undefined == rule['property'] || undefined == rule['selector'] ) {
					return;
				}
				rule['media'] = rule['media'] || 'global';
				rule['pattern'] = rule['pattern'] || '$';
				if ( 'object' == typeof newValue ) {
					if ( undefined !== newValue['desktop'] ) {
						selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
						property = ( undefined !== rule['property']['desktop'] ? rule['property']['desktop'] : rule['property'] );
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
						var border_style = ( undefined !== newValue['desktop']['style'] && '' !== newValue['desktop']['style'] ? newValue['desktop']['style'] : 'undefined' );
						if ( 'undefined' !== border_style ) {
							var color_type = '';
							if ( undefined !== newValue['desktop']['color'] && '' !== newValue['desktop']['color'] ) {
								if ( newValue['desktop']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['desktop']['color'] + ')';
								} else {
									color_type = newValue['desktop']['color']
								}
							}
							if ( '' !== color_type ) {
								cssArray[ rule['media'] ][ selector ][ property ] = ( newValue['desktop']['width'] ? newValue['desktop']['width'] : '0' ) + newValue['desktop']['unit'] + ' ' + border_style + ' ' + color_type;
							} else {
								cssArray[ rule['media'] ][ selector ][ property ] = ( newValue['desktop']['width'] ? newValue['desktop']['width'] : '0' ) + newValue['desktop']['unit'] + ' ' + border_style + ' ' + 'transparent';
							}
						}
					}
					if ( undefined !== newValue['tablet'] && undefined !== newValue['tablet']['width'] && '' !== newValue['tablet']['width'] ) {
						selector = ( undefined !== rule['selector']['tablet'] ? rule['selector']['tablet'] : rule['selector'] );
						property = ( undefined !== rule['property']['tablet'] ? rule['property']['tablet'] : rule['property'] );
						// Define properties.
						if ( undefined == cssArray[ media_tablet ] ) cssArray[ media_tablet ] = {};
						if ( undefined == cssArray[ media_tablet ][ selector ] ) cssArray[ media_tablet ][ selector ] = {};
						var tablet_border_style = ( undefined !== newValue['tablet']['style'] && '' !== newValue['tablet']['style'] ? newValue['tablet']['style'] : '' );
						var desktop_border_style = ( undefined !== newValue['desktop']['style'] && '' !== newValue['desktop']['style'] ? newValue['desktop']['style'] : '' );
						var border_style = 'undefined';
						if ( '' !== tablet_border_style ) {
							border_style = tablet_border_style;
						} else if ( '' !== desktop_border_style ) {
							border_style = desktop_border_style;
						}
						if ( 'undefined' !== border_style ) {
							var color_type = '';
							var width = ( newValue['desktop']['width'] ? newValue['desktop']['width'] : '0' );
							var unit = ( newValue['desktop']['unit'] ? newValue['desktop']['unit'] : 'px' );
							if ( undefined !== newValue['tablet']['color'] && '' !== newValue['tablet']['color'] ) {
								if ( newValue['tablet']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['tablet']['color'] + ')';
								} else {
									color_type = newValue['tablet']['color']
								}
							} else if ( undefined !== newValue['desktop']['color'] && '' !== newValue['desktop']['color'] ) {
								if ( newValue['desktop']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['desktop']['color'] + ')';
								} else {
									color_type = newValue['desktop']['color']
								}
							}
							if ( '' !== color_type ) {
								cssArray[ media_tablet ][ selector ][ property ] = ( '' !== newValue['tablet']['width'] ? newValue['tablet']['width'] : width ) + ( newValue['tablet']['unit'] ? newValue['tablet']['unit'] : unit ) + ' ' + border_style + ' ' + color_type;
							} else {
								cssArray[ media_tablet ][ selector ][ property ] = ( '' !== newValue['tablet']['width'] ? newValue['tablet']['width'] : width ) + ( newValue['tablet']['unit'] ? newValue['tablet']['unit'] : unit ) + ' ' + border_style + ' ' + 'transparent';
							}
						}
					}
					if ( undefined !== newValue['mobile'] && undefined !== newValue['mobile']['width'] && '' !== newValue['mobile']['width'] ) {
						selector = ( undefined !== rule['selector']['mobile'] ? rule['selector']['mobile'] : rule['selector'] );
						property = ( undefined !== rule['property']['mobile'] ? rule['property']['mobile'] : rule['property'] );
						// Define properties.
						if ( undefined == cssArray[ media_mobile ] ) cssArray[ media_mobile ] = {};
						if ( undefined == cssArray[ media_mobile ][ selector ] ) cssArray[ media_mobile ][ selector ] = {};
						var mobile_border_style = ( undefined !== newValue['mobile']['style'] && '' !== newValue['mobile']['style'] ? newValue['mobile']['style'] : '' );
						var tablet_border_style = ( undefined !== newValue['tablet']['style'] && '' !== newValue['tablet']['style'] ? newValue['tablet']['style'] : '' );
						var desktop_border_style = ( undefined !== newValue['desktop']['style'] && '' !== newValue['desktop']['style'] ? newValue['desktop']['style'] : '' );
						var border_style = 'undefined';
						if ( '' !== mobile_border_style ) {
							border_style = mobile_border_style;
						} else if ( '' !== tablet_border_style ) {
							border_style = tablet_border_style;
						} else if ( '' !== desktop_border_style ) {
							border_style = desktop_border_style;
						}
						if ( 'undefined' !== border_style ) {
							var color_type = '';
							var deskWidth = ( newValue['desktop']['width'] ? newValue['desktop']['width'] : '0' );
							var deskUnit = ( newValue['desktop']['unit'] ? newValue['desktop']['unit'] : 'px' );
							var width = ( newValue['tablet'] && newValue['tablet']['width'] ? newValue['tablet']['width'] : deskWidth );
							var unit = ( newValue['tablet'] && newValue['tablet']['unit'] ? newValue['tablet']['unit'] : deskUnit );
							if ( undefined !== newValue['mobile']['color'] && '' !== newValue['mobile']['color'] ) {
								if ( newValue['mobile']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['mobile']['color'] + ')';
								} else {
									color_type = newValue['mobile']['color']
								}
							} else if ( undefined !== newValue['tablet'] && undefined !== newValue['tablet']['color'] && '' !== newValue['tablet']['color'] ) {
								if ( newValue['tablet']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['tablet']['color'] + ')';
								} else {
									color_type = newValue['tablet']['color']
								}
							} else if ( undefined !== newValue['desktop']['color'] && '' !== newValue['desktop']['color'] ) {
								if ( newValue['desktop']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['desktop']['color'] + ')';
								} else {
									color_type = newValue['desktop']['color']
								}
							}
							if ( '' !== color_type ) {
								cssArray[ media_mobile ][ selector ][ property ] = ( '' !== newValue['mobile']['width'] ? newValue['mobile']['width'] : width ) + ( newValue['mobile']['unit'] ? newValue['mobile']['unit'] : unit ) + ' ' + border_style + ' ' + color_type;
							} else {
								cssArray[ media_mobile ][ selector ][ property ] = ( '' !== newValue['mobile']['width'] ? newValue['mobile']['width'] : width ) + ( newValue['mobile']['unit'] ? newValue['mobile']['unit'] : unit ) + ' ' + border_style + ' ' + 'transparent';
							}
						}
					}
					if ( undefined !== newValue['style'] ) {
						selector = rule['selector'];
						property = rule['property'];
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};

						var border_style = ( undefined !== newValue['style'] && '' !== newValue['style'] ? newValue['style'] : 'none' );
						//if ( 'none' !== border_style ) {
							var color_type = '';
							if ( undefined !== newValue['color'] && '' !== newValue['color'] ) {
								if ( newValue['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['color'] + ')';
								} else {
									color_type = newValue['color']
								}
							}
							if ( '' !== color_type ) {
								cssArray[ rule['media'] ][ selector ][ property ] = ( newValue['width'] ? newValue['width'] : '0' ) + newValue['unit'] + ' ' + border_style + ' ' + color_type;
							} else {
								cssArray[ rule['media'] ][ selector ][ property ] = ( newValue['width'] ? newValue['width'] : '0' ) + newValue['unit'] + ' ' + border_style + ' ' + 'transparent';
							}
						//}
					}
				}
			} );
			// Loop into the sorted array to build CSS string.
			_.each( cssArray, function( selectors, media ) {
				if ( 'global' !== media ) css += media + '{';
				_.each( selectors, function( properties, selector ) {
					css += selector + '{';
					_.each( properties, function( value, property ) {
						css += property + ':' + value + ';';
					});
					css += '}';
				});
				if ( 'global' !== media ) css += '}';
			});

			// Add CSS string to <style> tag.
			$style.html( css );
		},
		live_css_boxshadow : function( key, rules, newValue ) {
			var styleID = 'base-customize-preview-css-' + key,
			    $style = $( '#' + styleID ),
				css = '',
				media_tablet = '@media screen and (max-width: 1023px)',
				media_mobile = '@media screen and (max-width: 499px)',
				selector,
				property,
			    cssArray = {};
			// Create <style> tag if doesn't exist.
			if ( 0 === $style.length ) {
				$style = $( document.createElement( 'style' ) );
				$style.attr( 'id', styleID );
				$style.attr( 'type', 'text/css' );

				// Append <style> tag to <head>.
				$style.appendTo( $( 'head' ) );
			}
			_.each( rules, function( rule ) {
				if ( undefined == rule['property'] || undefined == rule['selector'] ) {
					return;
				}
				rule['media'] = rule['media'] || 'global';
				rule['pattern'] = rule['pattern'] || '$';
				if ( 'object' == typeof newValue ) {
					if ( undefined !== newValue['desktop'] ) {
						selector = ( undefined !== rule['selector']['desktop'] ? rule['selector']['desktop'] : rule['selector'] );
						property = ( undefined !== rule['property']['desktop'] ? rule['property']['desktop'] : rule['property'] );
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};
						var border_style = ( undefined !== newValue['desktop']['style'] && '' !== newValue['desktop']['style'] ? newValue['desktop']['style'] : 'undefined' );
						if ( 'undefined' !== border_style ) {
							var color_type = '';
							if ( undefined !== newValue['desktop']['color'] && '' !== newValue['desktop']['color'] ) {
								if ( newValue['desktop']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['desktop']['color'] + ')';
								} else {
									color_type = newValue['desktop']['color']
								}
							}
							if ( '' !== color_type ) {
								cssArray[ rule['media'] ][ selector ][ property ] = ( newValue['desktop']['width'] ? newValue['desktop']['width'] : '0' ) + newValue['desktop']['unit'] + ' ' + border_style + ' ' + color_type;
							} else {
								cssArray[ rule['media'] ][ selector ][ property ] = ( newValue['desktop']['width'] ? newValue['desktop']['width'] : '0' ) + newValue['desktop']['unit'] + ' ' + border_style + ' ' + 'transparent';
							}
						}
					}
					if ( undefined !== newValue['tablet'] ) {
						selector = ( undefined !== rule['selector']['tablet'] ? rule['selector']['tablet'] : rule['selector'] );
						property = ( undefined !== rule['property']['tablet'] ? rule['property']['tablet'] : rule['property'] );
						// Define properties.
						if ( undefined == cssArray[ media_tablet ] ) cssArray[ media_tablet ] = {};
						if ( undefined == cssArray[ media_tablet ][ selector ] ) cssArray[ media_tablet ][ selector ] = {};
						var border_style = ( undefined !== newValue['tablet']['style'] && '' !== newValue['tablet']['style'] ? newValue['tablet']['style'] : newValue['desktop']['style'] );
						var border_style_show = ( undefined !== newValue['tablet']['style'] && '' !== newValue['tablet']['style'] ? newValue['tablet']['style'] : 'undefined' );
						if ( 'undefined' !== border_style_show ) {
							var color_type = '';
							var width = ( newValue['desktop']['width'] ? newValue['desktop']['width'] : '0' );
							var unit = ( newValue['desktop']['unit'] ? newValue['desktop']['unit'] : 'px' );
							if ( undefined !== newValue['tablet']['color'] && '' !== newValue['tablet']['color'] ) {
								if ( newValue['tablet']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['tablet']['color'] + ')';
								} else {
									color_type = newValue['tablet']['color']
								}
							} else if ( undefined !== newValue['desktop']['color'] && '' !== newValue['desktop']['color'] ) {
								if ( newValue['desktop']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['desktop']['color'] + ')';
								} else {
									color_type = newValue['desktop']['color']
								}
							}
							if ( '' !== color_type ) {
								cssArray[ media_tablet ][ selector ][ property ] = ( newValue['tablet']['width'] ? newValue['tablet']['width'] : width ) + ( newValue['tablet']['unit'] ? newValue['tablet']['unit'] : unit ) + ' ' + border_style + ' ' + color_type;
							} else {
								cssArray[ media_tablet ][ selector ][ property ] = ( newValue['tablet']['width'] ? newValue['tablet']['width'] : width ) + ( newValue['tablet']['unit'] ? newValue['tablet']['unit'] : unit ) + ' ' + border_style + ' ' + 'transparent';
							}
						}
					}
					if ( undefined !== newValue['mobile'] ) {
						selector = ( undefined !== rule['selector']['mobile'] ? rule['selector']['mobile'] : rule['selector'] );
						property = ( undefined !== rule['property']['mobile'] ? rule['property']['mobile'] : rule['property'] );
						// Define properties.
						if ( undefined == cssArray[ media_mobile ] ) cssArray[ media_mobile ] = {};
						if ( undefined == cssArray[ media_mobile ][ selector ] ) cssArray[ media_mobile ][ selector ] = {};
						var border_style = ( undefined !== newValue['mobile']['style'] && '' !== newValue['mobile']['style'] ? newValue['mobile']['style'] : newValue['desktop']['style'] );
						var border_style_show = ( undefined !== newValue['mobile']['style'] && '' !== newValue['mobile']['style'] ? newValue['mobile']['style'] : 'undefined' );
						if ( 'undefined' !== border_style_show ) {
							var color_type = '';
							var deskWidth = ( newValue['desktop']['width'] ? newValue['desktop']['width'] : '0' );
							var deskUnit = ( newValue['desktop']['unit'] ? newValue['desktop']['unit'] : 'px' );
							var width = ( newValue['tablet'] && newValue['tablet']['width'] ? newValue['tablet']['width'] : deskWidth );
							var unit = ( newValue['tablet'] && newValue['tablet']['unit'] ? newValue['tablet']['unit'] : deskUnit );
							if ( undefined !== newValue['mobile']['color'] && '' !== newValue['mobile']['color'] ) {
								if ( newValue['mobile']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['mobile']['color'] + ')';
								} else {
									color_type = newValue['mobile']['color']
								}
							} else if ( undefined !== newValue['tablet'] && undefined !== newValue['tablet']['color'] && '' !== newValue['tablet']['color'] ) {
								if ( newValue['tablet']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['tablet']['color'] + ')';
								} else {
									color_type = newValue['tablet']['color']
								}
							} else if ( undefined !== newValue['desktop']['color'] && '' !== newValue['desktop']['color'] ) {
								if ( newValue['desktop']['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['desktop']['color'] + ')';
								} else {
									color_type = newValue['desktop']['color']
								}
							}
							if ( '' !== color_type ) {
								cssArray[ media_mobile ][ selector ][ property ] = ( newValue['mobile']['width'] ? newValue['mobile']['width'] : width ) + ( newValue['mobile']['unit'] ? newValue['mobile']['unit'] : unit ) + ' ' + border_style + ' ' + color_type;
							} else {
								cssArray[ media_mobile ][ selector ][ property ] = ( newValue['mobile']['width'] ? newValue['mobile']['width'] : width ) + ( newValue['mobile']['unit'] ? newValue['mobile']['unit'] : unit ) + ' ' + border_style + ' ' + 'transparent';
							}
						}
					}
					if ( undefined !== newValue['color'] ) {
						selector = rule['selector'];
						property = rule['property'];
						// Define properties.
						if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
						if ( undefined == cssArray[ rule['media'] ][ selector ] ) cssArray[ rule['media'] ][ selector ] = {};

						var inset = ( undefined !== newValue['inset'] && newValue['inset'] ? 'inset' : '' );
						//if ( 'none' !== border_style ) {
							var color_type = '';
							if ( undefined !== newValue['color'] && '' !== newValue['color'] ) {
								if ( newValue['color'].includes('palette') ) {
									color_type = 'var(--global-' + newValue['color'] + ')';
								} else {
									color_type = newValue['color']
								}
							}
							if ( '' !== color_type ) {
								cssArray[ rule['media'] ][ selector ][ property ] = ( inset ? inset + ' ' : '' ) + ( newValue['hOffset'] ? newValue['hOffset'] : '0' ) + 'px ' + ( newValue['vOffset'] ? newValue['vOffset'] : '0' ) + 'px ' + ( newValue['blur'] ? newValue['blur'] : '0' ) + 'px ' + ( newValue['spread'] ? newValue['spread'] : '0' ) + 'px ' + ' ' + color_type;
							} else {
								cssArray[ rule['media'] ][ selector ][ property ] = ( inset ? inset + ' ' : '' ) + ( newValue['hOffset'] ? newValue['hOffset'] : '0' ) + 'px ' + ( newValue['vOffset'] ? newValue['vOffset'] : '0' ) + 'px ' + ( newValue['blur'] ? newValue['blur'] : '0' ) + 'px ' + ( newValue['spread'] ? newValue['spread'] : '0' ) + 'px ' + ' ' + 'transparent';
							}
						//}
					}
				}
			} );
			// Loop into the sorted array to build CSS string.
			_.each( cssArray, function( selectors, media ) {
				if ( 'global' !== media ) css += media + '{';
				_.each( selectors, function( properties, selector ) {
					css += selector + '{';
					_.each( properties, function( value, property ) {
						css += property + ':' + value + ';';
					});
					css += '}';
				});
				if ( 'global' !== media ) css += '}';
			});

			// Add CSS string to <style> tag.
			$style.html( css );
		},
		live_css : function( key, rules, newValue ) {
			var styleID = 'base-customize-preview-css-' + key,
			    $style = $( '#' + styleID ),
				css = '',
				media_tablet = '@media screen and (max-width: 1023px)',
				media_mobile = '@media screen and (max-width: 499px)',
			    cssArray = {};

			// Create <style> tag if doesn't exist.
			if ( 0 === $style.length ) {
				$style = $( document.createElement( 'style' ) );
				$style.attr( 'id', styleID );
				$style.attr( 'type', 'text/css' );

				// Append <style> tag to <head>.
				$style.appendTo( $( 'head' ) );
			}
			_.each( rules, function( rule ) {
				var formattedValue;
				if ( undefined == rule['property'] || undefined == rule['selector'] ) {
					return;
				}
				rule['media'] = rule['media'] || 'global';
				rule['pattern'] = rule['pattern'] || '$';
				if ( 'object' == typeof newValue ) {
					if ( undefined !== rule['key'] && 'measure' === rule['key'] ) {
						if ( 'object' == typeof newValue[ 'size' ] && undefined !== newValue[ 'size' ]['desktop'] ) {
							if ( undefined !== newValue[ 'size' ]['desktop'] ) {
								var unit = ( undefined !== newValue[ 'unit' ] && undefined !== newValue[ 'unit' ]['desktop'] ? newValue[ 'unit' ]['desktop'] : 'px' );
								formattedValue = ( '' !== newValue[ 'size' ]['desktop'][0] ? newValue[ 'size' ]['desktop'][0] : '0' ) + unit + ' ' + ( '' !== newValue[ 'size' ]['desktop'][1] ? newValue[ 'size' ]['desktop'][1] : '0' ) + unit + ' ' + ( '' !== newValue[ 'size' ]['desktop'][2] ? newValue[ 'size' ]['desktop'][2] : '0' ) + unit + ' ' + ( '' !== newValue[ 'size' ]['desktop'][3] ? newValue[ 'size' ]['desktop'][3] : '0' ) + unit;
								// Define properties.
								if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
								if ( undefined == cssArray[ rule['media'] ][ rule['selector' ] ] ) cssArray[ rule['media'] ][ rule['selector' ] ] = {};
								cssArray[ rule['media'] ][ rule['selector'] ][ rule['property'] ] = formattedValue;
							}
							if ( undefined !== newValue[ 'size' ]['tablet'] ) {
								var unit = ( undefined !== newValue[ 'unit' ]['tablet'] ? newValue[ 'unit' ]['tablet'] : '' );
								formattedValue = ( '' !== newValue[ 'size' ]['tablet'][0] ? newValue[ 'size' ]['tablet'][0] : '0' ) + unit + ' ' + ( '' !== newValue[ 'size' ]['tablet'][1] ? newValue[ 'size' ]['tablet'][1] : '0' ) + unit + ' ' + ( '' !== newValue[ 'size' ]['tablet'][2] ? newValue[ 'size' ]['tablet'][2] : '0' ) + unit + ' ' + ( '' !== newValue[ 'size' ]['tablet'][3] ? newValue[ 'size' ]['tablet'][3] : '0' ) + unit;
								// Define properties.
								if ( undefined == cssArray[ media_tablet ] ) cssArray[ media_tablet ] = {};
								if ( undefined == cssArray[ media_tablet ][ rule['selector' ] ] ) cssArray[ media_tablet ][ rule['selector' ] ] = {};
								cssArray[ media_tablet ][ rule['selector'] ][ rule['property'] ] = formattedValue;
							}
							if ( undefined !== newValue[ 'size' ]['mobile'] ) {
								var unit = ( undefined !== newValue[ 'unit' ]['mobile'] ? newValue[ 'unit' ]['mobile'] : '' );
								formattedValue = rule['pattern'].replace( '$', newValue[ 'size' ]['mobile'] + unit );
								formattedValue = ( '' !== newValue[ 'size' ]['mobile'][0] ? newValue[ 'size' ]['mobile'][0] : '0' ) + unit + ' ' + ( '' !== newValue[ 'size' ]['mobile'][1] ? newValue[ 'size' ]['mobile'][1] : '0' ) + unit + ' ' + ( '' !== newValue[ 'size' ]['mobile'][2] ? newValue[ 'size' ]['mobile'][2] : '0' ) + unit + ' ' + ( '' !== newValue[ 'size' ]['mobile'][3] ? newValue[ 'size' ]['mobile'][3] : '0' ) + unit;
								// Define properties.
								if ( undefined == cssArray[ media_mobile ] ) cssArray[ media_mobile ] = {};
								if ( undefined == cssArray[ media_mobile ][ rule['selector' ] ] ) cssArray[ media_mobile ][ rule['selector' ] ] = {};
								cssArray[ media_mobile ][ rule['selector'] ][ rule['property'] ] = formattedValue;
							}
						} else {
							formattedValue = ( newValue[ 'size' ] && '' !== newValue[ 'size' ][0] ? newValue[ 'size' ][0] : '0' ) + ( newValue[ 'unit' ] ? newValue[ 'unit' ] : 'px' ) + ' ' + ( newValue[ 'size' ] &&  '' !== newValue[ 'size' ][1] ? newValue[ 'size' ][1] : '0' ) + ( newValue[ 'unit' ] ? newValue[ 'unit' ] : 'px' ) + ' ' + ( newValue[ 'size' ] && '' !== newValue[ 'size' ][2] ? newValue[ 'size' ][2] : '0' ) + ( newValue[ 'unit' ] ? newValue[ 'unit' ] : 'px' ) + ' ' + ( newValue[ 'size' ] && '' !== newValue[ 'size' ][3] ? newValue[ 'size' ][3] : '0' ) + ( newValue[ 'unit' ] ? newValue[ 'unit' ] : 'px' );
							formattedValue = rule['pattern'].replace( '$', formattedValue );
							// Define properties.
							if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
							if ( undefined == cssArray[ rule['media'] ][ rule['selector' ] ] ) cssArray[ rule['media'] ][ rule['selector' ] ] = {};
							cssArray[ rule['media'] ][ rule['selector'] ][ rule['property'] ] = formattedValue;
						}
					} else if ( undefined !== rule['key'] && undefined !== newValue[ rule['key'] ] ) {
						// Fetch the property newValue using the key from setting value.
						if ( 'object' == typeof newValue[ rule['key'] ] ) {
							if ( undefined !== newValue[ rule['key'] ]['color'] && '' !== newValue[ rule['key'] ]['color'] ) {
								var color_type = ( undefined !== newValue[ rule['key'] ]['is_palette'] && '' !== newValue[ rule['key'] ]['is_palette'] ? 'var(--global-' + newValue[ rule['key'] ]['is_palette'] + ')' : newValue[ rule['key'] ]['color'] );
								formattedValue = rule['pattern'].replace( '$', color_type );
								// Define properties.
								if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
								if ( undefined == cssArray[ rule['media'] ][ rule['selector' ] ] ) cssArray[ rule['media'] ][ rule['selector' ] ] = {};
								cssArray[ rule['media'] ][ rule['selector'] ][ rule['property'] ] = formattedValue;
							}
							if ( undefined !== newValue[ rule['key'] ]['desktop'] ) {
								var unit = ( undefined !== newValue[ 'unit' ]['desktop'] ? newValue[ 'unit' ]['desktop'] : '' );
								formattedValue = rule['pattern'].replace( '$', newValue[ rule['key'] ]['desktop'] + unit );
								// Define properties.
								if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
								if ( undefined == cssArray[ rule['media'] ][ rule['selector' ] ] ) cssArray[ rule['media'] ][ rule['selector' ] ] = {};
								cssArray[ rule['media'] ][ rule['selector'] ][ rule['property'] ] = formattedValue;
							}
							if ( undefined !== newValue[ rule['key'] ]['tablet'] ) {
								var unit = ( undefined !== newValue[ 'unit' ]['tablet'] ? newValue[ 'unit' ]['tablet'] : '' );
								formattedValue = rule['pattern'].replace( '$', newValue[ rule['key'] ]['tablet'] + unit );
								// Define properties.
								if ( undefined == cssArray[ media_tablet ] ) cssArray[ media_tablet ] = {};
								if ( undefined == cssArray[ media_tablet ][ rule['selector' ] ] ) cssArray[ media_tablet ][ rule['selector' ] ] = {};
								cssArray[ media_tablet ][ rule['selector'] ][ rule['property'] ] = formattedValue;
							}
							if ( undefined !== newValue[ rule['key'] ]['mobile'] ) {
								var unit = ( undefined !== newValue[ 'unit' ]['mobile'] ? newValue[ 'unit' ]['mobile'] : '' );
								formattedValue = rule['pattern'].replace( '$', newValue[ rule['key'] ]['mobile'] + unit );
								// Define properties.
								if ( undefined == cssArray[ media_mobile ] ) cssArray[ media_mobile ] = {};
								if ( undefined == cssArray[ media_mobile ][ rule['selector' ] ] ) cssArray[ media_mobile ][ rule['selector' ] ] = {};
								cssArray[ media_mobile ][ rule['selector'] ][ rule['property'] ] = formattedValue;
							}
						} else {
							if ( rule['key'] === 'size' ) {
								formattedValue = newValue[ rule['key'] ] + ( newValue[ 'unit' ] ? newValue[ 'unit' ] : 'px' );
							} else if ( typeof newValue[ rule['key'] ] === 'string' && newValue[ rule['key'] ].includes('palette') ) {
								formattedValue = 'var(--global-' + newValue[ rule['key'] ] + ')';
							} else {
								formattedValue = newValue[ rule['key'] ];
							}
							formattedValue = rule['pattern'].replace( '$', formattedValue );
							// Define properties.
							if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
							if ( undefined == cssArray[ rule['media'] ][ rule['selector' ] ] ) cssArray[ rule['media'] ][ rule['selector' ] ] = {};
							cssArray[ rule['media'] ][ rule['selector'] ][ rule['property'] ] = formattedValue;
						}
					}
				} else {
					// Define new value based on the specified pattern.
					formattedValue = rule['pattern'].replace( '$', newValue );
					// Define properties.
					if ( undefined == cssArray[ rule['media'] ] ) cssArray[ rule['media'] ] = {};
					if ( undefined == cssArray[ rule['media'] ][ rule['selector' ] ] ) cssArray[ rule['media'] ][ rule['selector' ] ] = {};
					cssArray[ rule['media'] ][ rule['selector'] ][ rule['property'] ] = formattedValue;
				}
			});

				// Loop into the sorted array to build CSS string.
			_.each( cssArray, function( selectors, media ) {
				if ( 'global' !== media ) css += media + '{';
				_.each( selectors, function( properties, selector ) {
					css += selector + '{';
					_.each( properties, function( value, property ) {
						css += property + ':' + value + ';';
					});
					css += '}';
				});
				if ( 'global' !== media ) css += '}';
			});

			// Add CSS string to <style> tag.
			$style.html( css );
		},
		live_class : function( key, rules, newValue ) {
			_.each( rules, function( rule ) {
				var formattedValue;
				if ( undefined == rule['selector'] ) {
					return;
				}
				rule['pattern'] = rule['pattern'] || '$';
				if ( 'object' == typeof newValue ) {
					if ( undefined !== rule['key'] && undefined !== newValue[ rule['key'] ] ) {
						// Fetch the property newValue using the key from setting value.
						if ( 'object' == typeof newValue[ rule['key'] ] ) {
							if ( undefined !== newValue[ rule['key'] ]['desktop'] && '' !== newValue[ rule['key'] ]['desktop'] ) {
								if ( undefined !== rule['pattern']['desktop'] ) {
									var regex = new RegExp( rule['pattern']['desktop'].replace( '$', '[\\w\\-]+' ), 'i' );
									var device_pattern = rule['pattern']['desktop'];
								} else {
									var regex = new RegExp( rule['pattern'].replace( '$', '[\\w\\-]+' ), 'i' );
									var device_pattern = rule['pattern'];
								}
								if ( undefined !== rule['selector']['desktop'] ) {
									var items = document.querySelectorAll( rule['selector']['desktop'] );
								} else {
									var items = document.querySelectorAll( rule['selector'] );
								}
								formattedValue = device_pattern.replace( '$', newValue[ rule['key'] ]['desktop'] );
								items.forEach(function( item ) {
									if ( item.className.match( regex ) ) {
										item.className = item.className.replace( regex, formattedValue );
									} else {
										item.className += ' ' + formattedValue;
									}
								});
							}
							if ( undefined !== newValue[ rule['key'] ]['tablet'] && '' !== newValue[ rule['key'] ]['tablet'] ) {
								if ( undefined !== rule['pattern']['tablet'] ) {
									var regex = new RegExp( rule['pattern']['tablet'].replace( '$', '[\\w\\-]+' ), 'i' );
									var device_pattern = rule['pattern']['tablet'];
								} else {
									var regex = new RegExp( rule['pattern'].replace( '$', '[\\w\\-]+' ), 'i' );
									var device_pattern = rule['pattern'];
								}
								if ( undefined !== rule['selector']['tablet'] ) {
									var items = document.querySelectorAll( rule['selector']['tablet'] );
								} else {
									var items = document.querySelectorAll( rule['selector'] );
								}
								formattedValue = device_pattern.replace( '$', newValue[ rule['key'] ]['tablet'] );
								items.forEach(function( item ) {
									if ( item.className.match( regex ) ) {
										item.className = item.className.replace( regex, formattedValue );
									} else {
										item.className += ' ' + formattedValue;
									}
								});
							}
							if ( undefined !== newValue[ rule['key'] ]['mobile'] && '' !== newValue[ rule['key'] ]['mobile'] ) {
								if ( undefined !== rule['pattern']['mobile'] ) {
									var regex = new RegExp( rule['pattern']['mobile'].replace( '$', '[\\w\\-]+' ), 'i' );
									var device_pattern = rule['pattern']['mobile'];
								} else {
									var regex = new RegExp( rule['pattern'].replace( '$', '[\\w\\-]+' ), 'i' );
									var device_pattern = rule['pattern'];
								}
								if ( undefined !== rule['selector']['mobile'] ) {
									var items = document.querySelectorAll( rule['selector']['mobile'] );
								} else {
									var items = document.querySelectorAll( rule['selector'] );
								}
								formattedValue = device_pattern.replace( '$', newValue[ rule['key'] ]['mobile'] );
								items.forEach(function( item ) {
									if ( item.className.match( regex ) ) {
										item.className = item.className.replace( regex, formattedValue );
									} else {
										item.className += ' ' + formattedValue;
									}
								});
							}
						} else {
							var regex = new RegExp( rule['pattern'].replace( '$', '[\\w\\-]+' ), 'i' ),
							items = document.querySelectorAll( rule['selector'] ),
							formattedValue = rule['pattern'].replace( '$', newValue[ rule['key'] ] );
							items.forEach(function( item ) {
								if ( item.className.match( regex ) ) {
									item.className = item.className.replace( regex, formattedValue );
								} else {
									item.className += ' ' + formattedValue;
								}
							});
						}
					} else {
						if ( undefined !== newValue['desktop'] && '' !== newValue['desktop'] ) {
							if ( undefined !== rule['pattern']['desktop'] ) {
								var regex = new RegExp( rule['pattern']['desktop'].replace( '$', '[\\w\\-]+' ), 'i' );
								var device_pattern = rule['pattern']['desktop'];
							} else {
								var regex = new RegExp( rule['pattern'].replace( '$', '[\\w\\-]+' ), 'i' );
								var device_pattern = rule['pattern'];
							}
							if ( undefined !== rule['selector']['desktop'] ) {
								var items = document.querySelectorAll( rule['selector']['desktop'] );
							} else {
								var items = document.querySelectorAll( rule['selector'] );
							}
							formattedValue = device_pattern.replace( '$', newValue['desktop'] );
							items.forEach(function( item ) {
								if ( item.className.match( regex ) ) {
									item.className = item.className.replace( regex, formattedValue );
								} else {
									item.className += ' ' + formattedValue;
								}
							});
						}
						if ( undefined !== newValue['tablet'] && '' !== newValue['tablet'] ) {
							if ( undefined !== rule['pattern']['tablet'] ) {
								var regex = new RegExp( rule['pattern']['tablet'].replace( '$', '[\\w\\-]+' ), 'i' );
								var device_pattern = rule['pattern']['tablet'];
							} else {
								var regex = new RegExp( rule['pattern'].replace( '$', '[\\w\\-]+' ), 'i' );
								var device_pattern = rule['pattern'];
							}
							if ( undefined !== rule['selector']['tablet'] ) {
								var items = document.querySelectorAll( rule['selector']['tablet'] );
							} else {
								var items = document.querySelectorAll( rule['selector'] );
							}
							formattedValue = device_pattern.replace( '$', newValue['tablet'] );
							items.forEach(function( item ) {
								if ( item.className.match( regex ) ) {
									item.className = item.className.replace( regex, formattedValue );
								} else {
									item.className += ' ' + formattedValue;
								}
							});
						}
						if ( undefined !== newValue['mobile'] && '' !== newValue['mobile'] ) {
							if ( undefined !== rule['pattern']['mobile'] ) {
								var regex = new RegExp( rule['pattern']['mobile'].replace( '$', '[\\w\\-]+' ), 'i' );
								var device_pattern = rule['pattern']['mobile'];
							} else {
								var regex = new RegExp( rule['pattern'].replace( '$', '[\\w\\-]+' ), 'i' );
								var device_pattern = rule['pattern'];
							}
							if ( undefined !== rule['selector']['mobile'] ) {
								var items = document.querySelectorAll( rule['selector']['mobile'] );
							} else {
								var items = document.querySelectorAll( rule['selector'] );
							}
							formattedValue = device_pattern.replace( '$', newValue['mobile'] );
							items.forEach(function( item ) {
								if ( item.className.match( regex ) ) {
									item.className = item.className.replace( regex, formattedValue );
								} else {
									item.className += ' ' + formattedValue;
								}
							});
						}
					}
				} else {
					var regex = new RegExp( rule['pattern'].replace( '$', '[\\w\\-]+' ), 'i' ),
					items = document.querySelectorAll( rule['selector'] ),
					formattedValue = rule['pattern'].replace( '$', newValue );
					items.forEach(function( item ) {
						if ( item.className.match( regex ) ) {
							item.className = item.className.replace( regex, formattedValue );
						} else {
							item.className += ' ' + formattedValue;
						}
					});
				}
			});
		},
		live_palette: function( key, rules, newValue ) {
			var palette = JSON.parse( newValue );
			var active = ( palette && palette['active'] ? palette['active'] : 'palette' );
			if ( palette && palette[ active ] ) {
				_.each( palette[ active ], function( color ) {
					if ( color.slug && color.color ) {
						document.documentElement.style.setProperty('--global-' + color.slug, color.color );
					}
				} );
			}
		},
		live_global: function( key, rules, newValue ) {
			_.each( rules, function( rule ) {
				var formattedValue;
				if ( undefined == rule['property'] || undefined == rule['selector'] ) {
					return;
				}
				rule['media'] = rule['media'] || 'global';
				rule['pattern'] = rule['pattern'] || '$';
				if ( 'object' == typeof newValue ) {
					if ( undefined !== rule['key'] && undefined !== newValue[ rule['key'] ] ) {
						// Fetch the property newValue using the key from setting value.
						if ( typeof newValue[ rule['key'] ] === 'string' && newValue[ rule['key'] ].includes('palette') ) {
							formattedValue = 'var(--global-' + newValue[ rule['key'] ] + ')';
						} else {
							formattedValue = newValue[ rule['key'] ];
						}
						formattedValue = rule['pattern'].replace( '$', formattedValue );
						// Define properties.
						document.documentElement.style.setProperty(rule['selector'], formattedValue );
					}
				}
			});
		},
		live_html: function( key, rules, newValue ) {
			_.each( rules, function( rule ) {
				var value = newValue;

				if ( undefined == rule['selector'] ) return;
				rule['pattern'] = rule['pattern'] || '$';

				var elements = document.querySelectorAll( rule['selector'] ),
				    formattedValue = rule['pattern'].replace( '$', value );

				// Change innerHTML on all targeted elements.
				elements.forEach(function( element ) {
					if ( undefined !== rule['property'] ) {
						element.setAttribute( rule['property'], formattedValue );
					} else {
						element.innerHTML = formattedValue;
					}
				});
			});
		}
	};
	if ( baseCustomizerPreviewData && baseCustomizerPreviewData.liveControl ) {
		_.each( baseCustomizerPreviewData.liveControl, function( liveControl, key ) {
			var eachLiveControl = {};
			_.each( liveControl, function( rule ) {
				var type = rule['type'];

				if ( undefined == eachLiveControl[ type ] ) eachLiveControl[ type ] = [];

				eachLiveControl[ type ].push( rule );
			});
			wp.customize( key, function( value ) {
				value.bind( function( newValue ) {
					_.each( eachLiveControl, function( rule, type ) {
						var functionName = 'live_'.concat( type );
						baseCustomizer[ functionName ]( key, rule, newValue );
					});
				});
			});
		});
	}
	$( document ).on( 'customize-preview-menu-refreshed', function( e, params ) {
        if ( params.wpNavMenuArgs.show_toggles ) {
			window.base.initMobileToggleSub();
        }
	});
} )( jQuery );