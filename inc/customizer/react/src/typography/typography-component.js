/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import ResponsiveControl from '../common/responsive.js';
import ColorControl from '../common/color.js';
import Icons from '../common/icons.js';
import capitalizeFirstLetter from '../common/capitalize-first.js';
import BaseWebfontLoader from '../common/font-loader.js';
import FontPairModal from './font-pair';
import map from 'lodash/map';
import { __ } from '@wordpress/i18n';
import { ButtonGroup, Popover, Dashicon, Toolbar, Tooltip, Button, TextControl, TabPanel, RangeControl, SelectControl } from '@wordpress/components';
/**
 * WordPress dependencies
 */
import { createRef, Component, Fragment } from '@wordpress/element';
class TypographyComponent extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		this.getSizeUnitSelect = this.getSizeUnitSelect.bind( this );
		this.getUnitSelect = this.getUnitSelect.bind( this );
		this.setTypographyOptions = this.setTypographyOptions.bind( this );
		this.maybesScroll = this.maybesScroll.bind( this );
		this.onColorChange = this.onColorChange.bind( this );
		this.remoteUpdate = this.remoteUpdate.bind( this );
		this.linkRemoteUpdate = this.linkRemoteUpdate.bind( this );
		let value = this.props.control.setting.get();
		let baseDefault;
		let familyBaseDefault = {
			'family': '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"',
			'google': false,
			'fallback': '',
		};
		let allBaseDefault = {
			'size': {
				'desktop': 18,
			},
			'sizeType': 'px',
			'lineHeight': {
				'desktop': 1.65,
			},
			'lineType': '-',
			'letterSpacing': {
				'desktop': '',
			},
			'spacingType': 'em',
			'family': '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"',
			'google': false,
			'style': 'normal',
			'weight': 'regular',
			'variant': 'regular',
			'color': 'palette4',
			'transform': '',
			'fallback': '',
		};
		let noColorBaseDefault = {
			'size': {
				'desktop': 18,
			},
			'sizeType': 'px',
			'lineHeight': {
				'desktop': 1.65,
			},
			'lineType': '-',
			'letterSpacing': {
				'desktop': '',
			},
			'spacingType': 'em',
			'family': '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"',
			'google': false,
			'style': 'normal',
			'weight': 'regular',
			'variant': 'regular',
			'transform': '',
			'fallback': '',
		};
		let sizeBaseDefault = {
			'size': {
				'desktop': 18,
			},
			'sizeType': 'px',
			'lineHeight': {
				'desktop': 1.65,
			},
			'lineType': '-',
			'letterSpacing': {
				'desktop': '',
			},
			'spacingType': 'em',
			'color': 'palette4',
			'transform': '',
		};
		let defaultParams = {
			min: {
				px: '0',
				em: '0',
				rem: '0',
				'-': '0',
			},
			max: {
				px: '200',
				em: '16',
				rem: '16',
				'-': '16',
			},
			step: {
				px: '1',
				em: '0.001',
				rem: '0.001',
				'-': '0.001',
			},
			sizeUnits: ['px', 'em', 'rem'],
			lineUnits: ['-', 'px', 'em', 'rem'],
			spacingUnits: [ 'px', 'em', 'rem'],
			canInherit: true,
			transform: [ 'none', 'capitalize', 'uppercase', 'lowercase'],
			id: 'base-general-font',
			options: 'all',
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		if ( 'family' === this.controlParams.options ) {
			baseDefault = familyBaseDefault;
		} else if ( 'size' === this.controlParams.options ) {
			baseDefault = sizeBaseDefault;
		} else if ( 'no-color' === this.controlParams.options ) {
			baseDefault = noColorBaseDefault;
		} else {
			baseDefault = allBaseDefault;
		}
		this.defaultValue = this.props.control.params.default ? {
			...baseDefault,
			...this.props.control.params.default
		} : baseDefault;
		value = value ? {
			...JSON.parse( JSON.stringify( this.defaultValue ) ),
			...value
		} : JSON.parse( JSON.stringify( this.defaultValue ) );
		this.state = {
			currentDevice: 'desktop',
			isVisible: false,
			isPreviewVisible: false,
			openTab: 'size',
			typographyOptions: [],
			typographyVariants: [],
			activeFont: [],
			value: value,
			fontVars: ( baseCustomizerControlsData.gfontvars ? baseCustomizerControlsData.gfontvars : [] ),
			customFontVars: ( baseCustomizerControlsData.cfontvars ? baseCustomizerControlsData.cfontvars : [] ),
		};
		this.linkRemoteUpdate();
		this.anchorNodeRef = createRef();
	}
	componentDidMount() {
		let base_font;
		let heading_font;
		const fontsarray = Object.keys( this.state.fontVars ).map( ( name ) => {
			return { label: name, value: name, google: true, group: 'Google Font' };
		} );
		let customFonts = false;
		if ( this.state.customFontVars ) {
			customFonts = Object.keys( this.state.customFontVars ).map( ( name ) => {
				return { label: name, value: name, google: false, group: 'Custom Font', variants:'custom' };
			} );
		}
		const inheritFont = [
			{ label: 'Inherit', value: 'inherit', google: false, },
			{ label: 'Inherit Heading Font Family', value: 'var(--global-heading-font-family, inherit)', google: false, },
			{ label: 'Inherit Body Font Family', value: 'var(--global-body-font-family, inherit)', google: false, },
		];
		let systemFonts = [
			{ label: 'System Default', value: '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"', google: false, variants:'systemstack' },
			{ label: 'Arial, Helvetica, sans-serif', value: 'Arial, Helvetica, sans-serif', google: false, group: 'System Fonts'},
			{ label: '"Arial Black", Gadget, sans-serif', value: '"Arial Black", Gadget, sans-serif', google: false, group: 'System Fonts' },
			{ label: 'Helvetica, sans-serif', value: 'Helvetica, sans-serif', google: false, group: 'System Fonts'},
			{ label: '"Comic Sans MS", cursive, sans-serif', value: '"Comic Sans MS", cursive, sans-serif', google: false, group: 'System Fonts' },
			{ label: 'Impact, Charcoal, sans-serif', value: 'Impact, Charcoal, sans-serif', google: false, group: 'System Fonts' },
			{ label: '"Lucida Sans Unicode", "Lucida Grande", sans-serif', value: '"Lucida Sans Unicode", "Lucida Grande", sans-serif', google: false, group: 'System Fonts' },
			{ label: 'Tahoma, Geneva, sans-serif', value: 'Tahoma, Geneva, sans-serif', google: false, group: 'System Fonts' },
			{ label: '"Trebuchet MS", Helvetica, sans-serif', value: '"Trebuchet MS", Helvetica, sans-serif', google: false, group: 'System Fonts' },
			{ label: 'Verdana, Geneva, sans-serif', value: 'Verdana, Geneva, sans-serif', google: false, group: 'System Fonts' },
			{ label: 'Georgia, serif', value: 'Georgia, serif', google: false, group: 'System Fonts' },
			{ label: '"Palatino Linotype", "Book Antiqua", Palatino, serif', value: '"Palatino Linotype", "Book Antiqua", Palatino, serif', google: false, group: 'System Fonts' },
			{ label: '"Times New Roman", Times, serif', value: '"Times New Roman", Times, serif', google: false, group: 'System Fonts' },
			{ label: 'Courier, monospace', value: 'Courier, monospace', google: false, group: 'System Fonts' },
			{ label: '"Lucida Console", Monaco, monospace', value: '"Lucida Console", Monaco, monospace', google: false, group: 'System Fonts' },
		];
		if ( customFonts ) {
			systemFonts = customFonts.concat( systemFonts );
		}
		let typographyOptions = systemFonts.concat( fontsarray );
		if ( this.controlParams.canInherit ) {
			base_font = this.props.customizer.control( 'base_font' ).setting.get()
			typographyOptions = inheritFont.concat( typographyOptions );
		}
		if ( this.controlParams.headingInherit ) {
			heading_font = this.props.customizer.control( 'heading_font' ).setting.get()
		}
		this.setState( { typographyOptions: typographyOptions } );
		const standardVariants = [
			{ value: 'regular', label: 'Regular', weight: 'regular', style: 'normal' },
			{ value: 'italic', label: 'Regular Italic', weight: 'regular', style: 'italic' },
			{ value: '700', label: 'Bold 700', weight: '700', style: 'normal' },
			{ value: '700italic', label: 'Bold 700 Italic', weight: '700', style: 'italic' },
		];
		const systemVariants = [
			{ value: '100', label: 'Thin 100', weight: '100', style: 'normal' },
			{ value: '100italic', label: 'Thin 100 Italic', weight: '100', style: 'italic' },
			{ value: '200', label: 'Extra-Light 200', weight: '200', style: 'normal' },
			{ value: '200italic', label: 'Extra-Light 200 Italic', weight: '200', style: 'italic' },
			{ value: '300', label: 'Light 300', weight: '300', style: 'normal' },
			{ value: '300italic', label: 'Light 300 Italic', weight: '300', style: 'italic' },
			{ value: 'regular', label: 'Regular', weight: 'regular', style: 'normal' },
			{ value: 'italic', label: 'Regular Italic', weight: 'regular', style: 'italic' },
			{ value: '500', label: 'Medium 500', weight: '500', style: 'normal' },
			{ value: '500italic', label: 'Medium 500 Italic', weight: '500', style: 'italic' },
			{ value: '600', label: 'Semi-Bold 600', weight: '600', style: 'normal' },
			{ value: '600italic', label: 'Semi-Bold 600 Italic', weight: '600', style: 'italic' },
			{ value: '700', label: 'Bold 700', weight: '700', style: 'normal' },
			{ value: '700italic', label: 'Bold 700 Italic', weight: '700', style: 'italic' },
			{ value: '800', label: 'Extra-Bold 800', weight: '800', style: 'normal' },
			{ value: '800italic', label: 'Extra-Bold 800 Italic', weight: '800', style: 'italic' },
			{ value: '900', label: 'Ultra-Bold 900', weight: '900', style: 'normal' },
			{ value: '900italic', label: 'Ultra-Bold 900 Italic', weight: '900', style: 'italic' },
		];
		let activeFont = ( typographyOptions ? typographyOptions.filter( ( { value } ) => value === this.state.value.family ) : [ { label: 'Inherit', value: 'inherit', google: false } ] );
		if ( 'inherit' === this.state.value.family && this.controlParams.headingInherit && undefined !== heading_font.family ) {
			activeFont = ( typographyOptions ? typographyOptions.filter( ( { value } ) => value === heading_font.family ) : activeFont );
	 	} else if ( 'inherit' === this.state.value.family && undefined !== base_font.family ) {
			activeFont = ( typographyOptions ? typographyOptions.filter( ( { value } ) => value === base_font.family ) : activeFont );
		}
		let fontStandardVariants = standardVariants;
		if ( activeFont && activeFont[ 0 ] ) {
			if ( undefined !== activeFont[ 0 ].variants && 'systemstack' === activeFont[ 0 ].variants ) {
				fontStandardVariants = systemVariants;
			}
			if ( undefined !== activeFont[ 0 ].variants && 'custom' === activeFont[ 0 ].variants && this.state.customFontVars && undefined !== this.state.customFontVars[ activeFont[ 0 ].value ] ) {
				fontStandardVariants = this.state.customFontVars[ activeFont[ 0 ].value ].v.map( opt => ( { label: capitalizeFirstLetter( opt ), value: opt } ) );
			}
			if ( activeFont[ 0 ].google && activeFont[ 0 ].value ) {
				fontStandardVariants = this.state.fontVars[ activeFont[ 0 ].value ].v.map( opt => ( { label: capitalizeFirstLetter( opt ), value: opt } ) );
			}
		}
		this.setState( { typographyVariants: fontStandardVariants } );
		this.setState( { activeFont: activeFont } );
	}
	setTypographyOptions( typographyOptions ) {
		let base_font;
		let heading_font;
		const standardVariants = [
			{ value: 'regular', label: 'Regular', weight: 'regular', style: 'normal' },
			{ value: 'italic', label: 'Regular Italic', weight: 'regular', style: 'italic' },
			{ value: '700', label: 'Bold 700', weight: '700', style: 'normal' },
			{ value: '700italic', label: 'Bold 700 Italic', weight: '700', style: 'italic' },
		];
		const systemVariants = [
			{ value: '100', label: 'Thin 100', weight: '100', style: 'normal' },
			{ value: '100italic', label: 'Thin 100 Italic', weight: '100', style: 'italic' },
			{ value: '200', label: 'Extra-Light 200', weight: '200', style: 'normal' },
			{ value: '200italic', label: 'Extra-Light 200 Italic', weight: '200', style: 'italic' },
			{ value: '300', label: 'Light 300', weight: '300', style: 'normal' },
			{ value: '300italic', label: 'Light 300 Italic', weight: '300', style: 'italic' },
			{ value: 'regular', label: 'Regular', weight: 'regular', style: 'normal' },
			{ value: 'italic', label: 'Regular Italic', weight: 'regular', style: 'italic' },
			{ value: '500', label: 'Medium 500', weight: '500', style: 'normal' },
			{ value: '500italic', label: 'Medium 500 Italic', weight: '500', style: 'italic' },
			{ value: '600', label: 'Semi-Bold 600', weight: '600', style: 'normal' },
			{ value: '600italic', label: 'Semi-Bold 600 Italic', weight: '600', style: 'italic' },
			{ value: '700', label: 'Bold 700', weight: '700', style: 'normal' },
			{ value: '700italic', label: 'Bold 700 Italic', weight: '700', style: 'italic' },
			{ value: '800', label: 'Extra-Bold 800', weight: '800', style: 'normal' },
			{ value: '800italic', label: 'Extra-Bold 800 Italic', weight: '800', style: 'italic' },
			{ value: '900', label: 'Ultra-Bold 900', weight: '900', style: 'normal' },
			{ value: '900italic', label: 'Ultra-Bold 900 Italic', weight: '900', style: 'italic' },
		];
		if ( this.controlParams.canInherit ) {
			base_font = this.props.customizer.control( 'base_font' ).setting.get()
		}
		if ( this.controlParams.headingInherit ) {
			heading_font = this.props.customizer.control( 'heading_font' ).setting.get()
		}
		let activeFont = ( typographyOptions ? typographyOptions.filter( ( { value } ) => value === this.state.value.family ) : [ { label: 'Inherit', value: 'inherit', google: false } ] );
		if ( 'inherit' === this.state.value.family && this.controlParams.headingInherit && undefined !== heading_font.family && 'inherit' !== heading_font.family ) {
			activeFont = ( typographyOptions ? typographyOptions.filter( ( { value } ) => value === heading_font.family ) : activeFont );
	 	} else if ( 'inherit' === this.state.value.family && undefined !== base_font.family ) {
			activeFont = ( typographyOptions ? typographyOptions.filter( ( { value } ) => value === base_font.family ) : activeFont )
		}
		let fontStandardVariants = standardVariants;
		if ( activeFont && activeFont[ 0 ] ) {
			if ( undefined !== activeFont[ 0 ].variants && 'systemstack' === activeFont[ 0 ].variants ) {
				fontStandardVariants = systemVariants;
			}
			if ( undefined !== activeFont[ 0 ].variants && 'custom' === activeFont[ 0 ].variants && this.state.customFontVars && undefined !== this.state.customFontVars[ activeFont[ 0 ].value ] ) {
				fontStandardVariants = this.state.customFontVars[ activeFont[ 0 ].value ].v.map( opt => ( { label: capitalizeFirstLetter( opt ), value: opt } ) );
			}
			if ( activeFont[ 0 ].google && activeFont[ 0 ].value ) {
				fontStandardVariants = this.state.fontVars[ activeFont[ 0 ].value ].v.map( opt => ( { label: capitalizeFirstLetter( opt ), value: opt } ) );
			}
		}
		this.setState( { typographyVariants: fontStandardVariants } );
		this.setState( { activeFont: activeFont } );
	}
	maybesScroll( tab ) {
		let self = this;
		if ( 'font' === tab ) {
			setTimeout(function(){
				var myElement = document.getElementById( self.controlParams.id + '-active-font' );
				if ( myElement ) {
					var topPos = myElement.offsetTop - 50;
					document.getElementById(self.controlParams.id ).scrollTop = topPos;
				}
			}, 100);
		} else if ( 'style' === tab ) {
			setTimeout(function(){
				var myElement = document.getElementById( self.controlParams.id + '-active-style' );
				if ( myElement ) {
					var topPos = myElement.offsetTop - 50;
					document.getElementById(self.controlParams.id  + '-style').scrollTop = topPos;
				}
			}, 100);
		}
	}
	onColorChange( color, isPalette ) {
		let value = this.state.value;
		if ( isPalette ) {
			value.color = isPalette;
		} else if ( undefined !== color.rgb && undefined !== color.rgb.a && 1 !== color.rgb.a ) {
			value.color = 'rgba(' +  color.rgb.r + ',' +  color.rgb.g + ',' +  color.rgb.b + ',' + color.rgb.a + ')';
		} else {
			value.color = color.hex;
		}
		this.updateValues( value );
	}
	render() {
		const { typographyOptions } = this.state;
		let deviceIndex = this.state.currentDevice;
		let currentFamily;
		let fontVar = ( this.controlParams.headingInherit ? 'var(--global-heading-font)' : 'var(--global-base-font)' );
		let currentSize;
		let currentLineHeight;
		let currentLetterSpacing;
		var placeholderLineHeight = this.controlParams.headingInherit ? 1.5 : 1.4;
		var placeholderSize = this.controlParams.id === 'base_font' || ( this.controlParams.canInherit && ! this.controlParams.headingInherit ) ? 17 : '';
		if ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options || 'family' === this.controlParams.options ) {
			currentFamily = ( this.state.value.family && 'inherit' !== this.state.value.family ? this.state.value.family : fontVar );
		}
		if ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options || 'size' === this.controlParams.options ) {
			// Size
			if ( undefined === this.state.value.size[deviceIndex] ) {
				let largerDevice = ( this.state.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value.size[largerDevice] && this.state.value.size[largerDevice] ) {
					currentSize = this.state.value.size[largerDevice];
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value.size['desktop'] && this.state.value.size['desktop'] ) {
					currentSize = this.state.value.size['desktop'];
				}
			} else if ( '' === this.state.value.size[deviceIndex] ) {
				let largerDevice = ( this.state.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value.size[largerDevice] && this.state.value.size[largerDevice] ) {
					currentSize = this.state.value.size[largerDevice];
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value.size['desktop'] && this.state.value.size['desktop'] ) {
					currentSize = this.state.value.size['desktop'];
				}
			} else if ( '' !== this.state.value.size[deviceIndex] ) {
				currentSize = this.state.value.size[deviceIndex]
			}
			// Height
			if ( undefined === this.state.value.lineHeight[deviceIndex] ) {
				let largerDevice = ( this.state.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value.lineHeight[largerDevice] && this.state.value.lineHeight[largerDevice] ) {
					currentLineHeight = this.state.value.lineHeight[largerDevice];
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value.lineHeight['desktop'] && this.state.value.lineHeight['desktop'] ) {
					currentLineHeight = this.state.value.lineHeight['desktop'];
				}
			} else if ( '' === this.state.value.lineHeight[deviceIndex] ) {
				let largerDevice = ( this.state.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value.lineHeight[largerDevice] && this.state.value.lineHeight[largerDevice] ) {
					currentLineHeight = this.state.value.lineHeight[largerDevice];
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value.lineHeight['desktop'] && this.state.value.lineHeight['desktop'] ) {
					currentLineHeight = this.state.value.lineHeight['desktop'];
				}
			} else if ( '' !== this.state.value.lineHeight[deviceIndex] ) {
				currentLineHeight = this.state.value.lineHeight[deviceIndex]
			}
			// Spacing
			if ( undefined === this.state.value.letterSpacing[deviceIndex] ) {
				let largerDevice = ( this.state.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value.letterSpacing[largerDevice] && this.state.value.letterSpacing[largerDevice] ) {
					currentLetterSpacing = this.state.value.letterSpacing[largerDevice];
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value.letterSpacing['desktop'] && this.state.value.letterSpacing['desktop'] ) {
					currentLetterSpacing = this.state.value.letterSpacing['desktop'];
				}
			} else if ( '' === this.state.value.letterSpacing[deviceIndex] ) {
				let largerDevice = ( this.state.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value.letterSpacing[largerDevice] && this.state.value.letterSpacing[largerDevice] ) {
					currentLetterSpacing = this.state.value.letterSpacing[largerDevice];
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value.letterSpacing['desktop'] && this.state.value.letterSpacing['desktop'] ) {
					currentLetterSpacing = this.state.value.letterSpacing['desktop'];
				}
			} else if ( '' !== this.state.value.letterSpacing[deviceIndex] ) {
				currentLetterSpacing = this.state.value.letterSpacing[deviceIndex]
			}
		}
		const fontFamilyTab = (
			<Fragment>
				<div className="base-font-family-search">
					<TextControl
						type="text"
						value={ this.state.search }
						placeholder={ __( 'Search' ) }
						autocomplete="off"
						onChange={ value => this.setState( { search: value } ) }
					/>
					{ undefined !== this.state.search && '' !== this.state.search && (
						<Button
							className="base-clear-search"
							onClick={ () => {
								this.setState( { search: '' } )
							} }
						>
							<Dashicon icon='no' />
						</Button>
					) }
				</div>
				<div className="base-font-family-list-wrapper">
					<ButtonGroup id={ this.controlParams.id } className="base-font-family-list" aria-label={ __( 'Font Family List' ) }>
						{ map( typographyOptions, ( { label, value, google, group }, index ) => {
							if ( ( ! this.state.search || ( label && label.toLowerCase().includes( this.state.search.toLowerCase() ) ) ) ) {
								return (
									<Button
										key={ index }
										id={ ( value === this.state.value.family ?
											this.controlParams.id + '-active-font' :
											undefined ) }
										className={ ( value === this.state.value.family ?
											'active-radio ' :
											'' ) + "base-font-family-choice" }
										onClick={ () => onTypoFontChange( value, google ) }
									>
										{ label }
									</Button>
								);
							}
						} ) }
					</ButtonGroup>
				</div>
			</Fragment>
		);
		const fontStyleTab = (
			<Fragment>
				<div className="base-font-variant-list-wrapper">
					<ButtonGroup id={ this.controlParams.id + '-style' } className="base-font-variant-list" aria-label={ __( 'Font Style List' ) }>
						{ map( this.state.typographyVariants, ( { label, value }, index ) => {
							return (
								<Button
									key={ index }
									id={ ( value === this.state.value.variant ?
										this.controlParams.id + '-active-style' :
										undefined ) }
									className={ ( value === this.state.value.variant ?
										'active-radio ' :
										'' ) + "base-font-variant-choice" }
									style={{
										fontFamily: this.state.value.family,
										fontWeight: ( value === 'italic' || value === 'regular' ? 'normal' : value.replace(/[^0-9]/g,'') ),
										fontStyle: ( value.includes( 'italic' ) ? 'italic' : 'regular' ),
									} }
									onClick={ () => onVariantFontChange( value ) }
								>
									{ label }
								</Button>
							);
						} ) }
					</ButtonGroup>
				</div>
			</Fragment>
		);
		const fontSizeTab = (
			<Fragment>
				<div class="base-range-control">
					<ResponsiveControl
						onChange={ ( currentDevice) => this.setState( { currentDevice } ) }
						controlLabel={ __( 'Font Size', 'avanam' ) }
						tooltip={ false }
					>
						<RangeControl
							allowReset={ true }
							initialPosition={ ( currentSize ? currentSize : placeholderSize ) }
							value={ currentSize }
							onChange={ (val) => {
								if ( val >= 0 ) {
									let value = this.state.value;
									value.size[ this.state.currentDevice ] = val;
									this.updateValues( value );
								} else {
									let value = this.state.value;
									value.size[ this.state.currentDevice ] = '';
									this.updateValues( value );
								}
							} }
							min={this.controlParams.min[this.state.value.sizeType]}
							max={this.controlParams.max[this.state.value.sizeType]}
							step={this.controlParams.step[this.state.value.sizeType]}
						/>
						{ this.controlParams.sizeUnits && (
							<div className="base-select-units">
								{ this.getSizeUnitSelect() }
							</div>
						) }
					</ResponsiveControl>
				</div>
				<div class="base-range-control">
					<ResponsiveControl
						onChange={ ( currentDevice) => this.setState( { currentDevice } ) }
						controlLabel={ __( 'Line Height', 'avanam' ) }
						tooltip={ false }
					>
						<RangeControl
							allowReset={ true }
							initialPosition={ ( currentLineHeight ? currentLineHeight : placeholderLineHeight ) }
							value={ currentLineHeight }
							onChange={ (val) => {
								if ( typeof(val) !== 'undefined' && val !== '' ) {
									let value = this.state.value;
									value.lineHeight[ this.state.currentDevice ] = val;
									this.updateValues( value );
								} else {
									let value = this.state.value;
									value.lineHeight[ this.state.currentDevice ] = '';
									this.updateValues( value );
								}
							} }
							min={this.controlParams.min[this.state.value.lineType]}
							max={this.controlParams.max[this.state.value.lineType]}
							step={this.controlParams.step[this.state.value.lineType]}
						/>
						{ this.controlParams.lineUnits && (
							<div className="base-select-units">
								{ this.getUnitSelect( 'lineUnits', 'lineType' ) }
							</div>
						) }
					</ResponsiveControl>
				</div>
				<div class="base-range-control">
					<ResponsiveControl
						onChange={ ( currentDevice) => this.setState( { currentDevice } ) }
						controlLabel={ __( 'Letter Spacing', 'avanam' ) }
						tooltip={ false }
					>
						<RangeControl
							allowReset={ true }
							value={ currentLetterSpacing }
							initialPosition={ ( currentLetterSpacing ? currentLetterSpacing : '' ) }
							onChange={ (val) => {
								if ( typeof(val) !== 'undefined' && val !== '' ) {
									let value = this.state.value;
									value.letterSpacing[ this.state.currentDevice ] = val;
									this.updateValues( value );
								} else {
									let value = this.state.value;
									value.letterSpacing[ this.state.currentDevice ] = '';
									this.updateValues( value );
								}
							} }
							min={-4}
							max={this.controlParams.max[this.state.value.spacingType]}
							step={this.controlParams.step[this.state.value.spacingType]}
						/>
						{ this.controlParams.spacingUnits && (
							<div className="base-select-units">
								{ this.getUnitSelect( 'spacingUnits', 'spacingType' ) }
							</div>
						) }
					</ResponsiveControl>
				</div>
				<div class="base-range-control base-transform-controls">
					<span className="customize-control-title">{ __( 'Transform' ) }</span>
					<ButtonGroup className="base-radio-container-control base-radio-icon-container-control">
						{ this.controlParams.transform.map( ( item ) => {
							return (
								<Fragment>
									<Button
										isTertiary
										className={ ( item === this.state.value.transform ?
												'active-radio ' :
												'' ) + item }
										onClick={ () => {
											let value = this.state.value;
											if ( item === this.state.value.transform ) {
												value.transform = '';
											} else {
												value.transform = item;
											}
											this.updateValues( value );
										} }
									>
										{ Icons[ item ] }
									</Button>
								</Fragment>
							);
						} ) }
					</ButtonGroup>
				</div>
			</Fragment>
		);
		const controlLabel = (
			<Fragment>
				{
					this.props.control.params.label &&
					<span className="customize-control-title">
						<Tooltip text={ __( 'Reset Values', 'avanam' ) }>
							<Button
								className="reset base-reset"
								onClick={ () => {
									this.updateValues( this.defaultValue );
								} }
							>
								<Dashicon icon='image-rotate' />
							</Button>
						</Tooltip>
						{ this.props.control.params.label }
						{ 'base_font' === this.controlParams.id && (
							<FontPairModal control={ this.props.control } customizer={ this.props.customizer } />
						) }
					</span>
				}
			</Fragment>
		);
		const onTypoFontChange = ( selected, isGoogle ) => {
			let value = this.state.value;
			let variant;
			let weight;
			let style;
			let fallback;
			if ( isGoogle ) {
				if ( 'family' === this.controlParams.options ) {
					variant = this.state.fontVars[ selected ].v;
				} else {
					if ( this.state.fontVars[ selected ].v.includes( value.variant ) ) {
						variant = value.variant;
					} else if ( ! this.state.fontVars[ selected ].v.includes( 'regular' ) ) {
						variant = this.state.fontVars[ selected ].v[ 0 ];
					} else {
						variant = 'regular';
					}
					if ( variant === 'regular' || variant === 'italic' ) {
						weight = 'normal';
					} else {
						weight = variant.replace(/[^0-9]/g,'');
					}
					if ( variant.includes( 'italic' ) ) {
						style = 'italic';
					} else {
						style = 'normal';
					}
				}
				fallback = ( this.state.fontVars[ selected ].c && this.state.fontVars[ selected ].c[0] ? this.state.fontVars[ selected ].c[0] : '' );
			} else {
				variant = 'regular';
				weight = '400';
				style = 'normal';
				fallback = '';
			}
			value.variant = variant;
			value.family = selected;
			value.google = ( isGoogle ? true : false );
			value.fallback = fallback;
			if ( 'family' !== this.controlParams.options ) {
				value.weight = weight;
				value.style = style;
			}
			this.updateValues( value );
		}
		const onVariantFontChange = ( variant ) => {
			let value = this.state.value;
			let weight;
			let style;
			if ( variant === 'regular' || variant === 'italic' ) {
				weight = 'normal';
			} else {
				weight = variant.replace(/[^0-9]/g,'');
			}
			if ( variant.includes( 'italic' ) ) {
				style = 'italic';
			} else {
				style = 'normal';
			}
			value.variant = variant;
			value.weight = weight;
			value.style = style;
			this.updateValues( value );
		}
		const toggleVisible = ( tab ) => {
			this.setTypographyOptions( this.state.typographyOptions );
			this.setState( { openTab : tab } );
			this.setState( { isVisible: true } );
			this.maybesScroll( tab );
		};
		const toggleClose = () => {
			if ( this.state.isVisible === true ) {
				this.setState( { isVisible: false } );
			}
		};
		const toggleVisiblePreview = () => {
			this.setState( { isPreviewVisible: true } );
		};
		const toggleClosePreview = () => {
			if ( this.state.isPreviewVisible === true ) {
				this.setState( { isPreviewVisible: false } );
			}
		};
		const configVariants = {
			google: {
				families: [ this.state.value.family + ':' + ( this.state.value.google && this.state.fontVars[ this.state.value.family ] && this.state.fontVars[ this.state.value.family ].v ? this.state.fontVars[ this.state.value.family ].v.toString() : '' ) ],
			},
			classes: false,
			events: false,
		};
		let fontFamilyLabel = capitalizeFirstLetter( this.state.value.family );
		if ( this.state.value.family === '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"' ) {
			fontFamilyLabel = __( 'System Default', 'avanam' );
		}
		if ( this.state.value.family === 'var(--global-heading-font-family, inherit)' ) {
			fontFamilyLabel = __( 'Inherit Heading', 'avanam' );
		}
		if ( this.state.value.family === 'var(--global-body-font-family, inherit)' ) {
			fontFamilyLabel = __( 'Inherit Body', 'avanam' );
		}
		return (
			<div ref={ this.anchorNodeRef } className="base-control-field base-typography-control-wrap">
				<div className="base-typography-control">
					{ controlLabel }
					<div className="base-typography-controls">
						{ this.state.isVisible && 'family' === this.controlParams.options && (
							<Popover position="top right" inline={true} className="base-popover-color base-popover-typography base-customizer-popover" onClose={ toggleClose }>
								<div className="base-popover-typography-single-item">
									{ fontFamilyTab }
								</div>
							</Popover>
						) }
						{ this.state.isVisible && ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options ) && (
							<Popover position="top left" inline={true} className="base-popover-color base-popover-typography base-customizer-popover" onClose={ toggleClose }>
								<TabPanel className="base-popover-tabs base-typography-tabs base-background-tabs"
									activeClass="active-tab"
									initialTabName={ this.state.openTab }
									onSelect={ ( value ) => this.maybesScroll( value ) }
									tabs={ [
										{
											name: 'font',
											title: __( 'Font', 'avanam' ),
											className: 'base-font-typography',
										},
										{
											name: 'style',
											title: __( 'Style', 'avanam' ),
											className: 'base-style-typography',
										},
										{
											name: 'size',
											title: __( 'Size', 'avanam' ),
											className: 'base-size-typography',
										},
									] }>
									{
										( tab ) => {
											let tabout;
											if ( tab.name ) {
												if ( 'style' === tab.name ) {
													tabout = fontStyleTab;
												} else if ( 'font' === tab.name ) {
													tabout = fontFamilyTab;
												} else {
													tabout = fontSizeTab;
												}
											}
											return <div>{ tabout }</div>;
										}
									}
								</TabPanel>
							</Popover>
						) }
						{ 'all' === this.controlParams.options && (
							<ColorControl
								presetColors={ this.state.colorPalette }
								color={ ( undefined !== this.state.value.color && this.state.value.color ? this.state.value.color : '' ) }
								usePalette={ true }
								onChangeComplete={ ( color, isPalette ) => this.onColorChange( color, isPalette ) }
								customizer={ this.props.customizer }
								controlRef={ this.anchorNodeRef }
							/>
						) }
						{ ( 'all' === this.controlParams.options || 'family' === this.controlParams.options || 'no-color' === this.controlParams.options ) && (
							<Tooltip text={ this.controlParams.tooltip ? this.controlParams.tooltip : __( 'Select Font', 'avanam' ) }>
								<div className="typography-button-wrap">
									<Button className={ 'base-typography-family-indicate' } onClick={ () => { this.state.isVisible ? toggleClose() : toggleVisible('font') } }>
										{ fontFamilyLabel }
									</Button>
								</div>
							</Tooltip>
						) }
						{ ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options ) && (
							<Tooltip text={ this.controlParams.tooltip ? this.controlParams.tooltip : __( 'Select Style', 'avanam' ) }>
								<div className="typography-button-wrap">
									<Button className={ 'base-typography-style-indicate' } onClick={ () => { this.state.isVisible ? toggleClose() : toggleVisible('style') } }>
										{ this.state.value.variant ? this.state.value.variant : __( 'inherit', 'avanam' ) }
									</Button>
								</div>
							</Tooltip>
						) }
						{ ( 'all' === this.controlParams.options || 'size' === this.controlParams.options || 'no-color' === this.controlParams.options ) && (
							<Tooltip text={ this.controlParams.tooltip ? this.controlParams.tooltip : __( 'Select Size', 'avanam' ) }>
								<div className="typography-button-wrap">
									<Button className={ 'base-typography-size-indicate' } onClick={ () => { this.state.isVisible ? toggleClose() : toggleVisible('size') } }>
										{ currentSize ? currentSize + this.state.value.sizeType : __( 'inherit', 'avanam' ) }
									</Button>
								</div>
							</Tooltip>
						) }
						{ ( 'all' === this.controlParams.options || 'family' === this.controlParams.options || 'no-color' === this.controlParams.options ) && (
							<Tooltip text={ __( 'Show Preview Text', 'avanam' ) }>
								<div className="typography-button-wrap">
									<Button className={ 'base-typography-preview-indicate' } onClick={ () => { this.state.isPreviewVisible ? toggleClosePreview() : toggleVisiblePreview() } }>
									{ this.state.isPreviewVisible ? <Dashicon icon="arrow-up" /> : <Dashicon icon="arrow-down" /> }
									</Button>
								</div>
							</Tooltip>
						) }
					</div>
				</div>
				{ this.state.value.google && (
					<BaseWebfontLoader config={ configVariants } >
					</BaseWebfontLoader>
				) }
				{ this.state.isPreviewVisible && (
					<div className="base-preview-font" style={ { 
							fontFamily: currentFamily,
							fontWeight: ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options ? this.state.value.weight : 'bold' ),
							fontStyle: ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options ? this.state.value.style : undefined ),
							fontSize: ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options ? currentSize + this.state.value.sizeType : undefined ),
							lineHeight: ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options ? currentLineHeight + ( '-' === this.state.value.lineType ? '' : this.state.value.lineType ) : 1.3 ),
							letterSpacing: ( ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options ) && currentLetterSpacing ? currentLetterSpacing + this.state.value.spacingType : undefined ),
							textTransform: ( ( 'all' === this.controlParams.options || 'no-color' === this.controlParams.options ) && this.state.value.transform ? this.state.value.transform : undefined ),
							color: ( this.state.value.color && this.state.value.color.includes( 'palette' ) ? 'var(--global-' + this.state.value.color + ')' : this.state.value.color ),
						} }>
						{ __( 'Design is not just what it looks like and feels like. Design is how it works.', 'avanam' ) }
					</div>
				) }
			</div>
		);
	}
	getUnitSelect( unitType, unitSetting ) {
		const units = this.controlParams[ unitType ];
		if ( this.state.currentDevice !== 'desktop' ) {
			return ( <Button
					className="is-single"
					disabled
			>{ this.state.value[ unitSetting ] }</Button> );
		}
		const unitOptions = units.map( unit => ( { label: unit, value: unit } ) );
		return <SelectControl
				value={ this.state.value[ unitSetting ] }
				options={ unitOptions }
				onChange={ ( val ) => {
					let value = this.state.value;
					value[ unitSetting ] = val;
					this.updateValues( value );
				} }
			/> 
	}
	getSizeUnitSelect() {
		const { sizeUnits } = this.controlParams;
		if ( this.state.currentDevice !== 'desktop' ) {
			return ( <Button
					className="is-single"
					disabled
			>{ this.state.value.sizeType }</Button> );
		}
		const unitOptions = sizeUnits.map( unit => ( { label: unit, value: unit } ) );
		return <SelectControl
				value={ this.state.value.sizeType }
				options={ unitOptions }
				onChange={ ( val ) => {
					let value = this.state.value;
					value['sizeType'] = val;
					this.updateValues( value );
				} }
			/> 
	}
	updateValues( value ) {
		this.setTypographyOptions( this.state.typographyOptions );
		if ( 'base_font' === this.controlParams.id ) {
			document.documentElement.style.setProperty('--global-base-font' , value.family );
		}
		if ( 'heading_font' === this.controlParams.id ) {
			document.documentElement.style.setProperty('--global-heading-font' , value.family );
		}
		this.setState( { value: value } );
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
			flag: ! this.props.control.setting.get().flag
		} );
	}
	linkRemoteUpdate() {
		if ( 'base_font' === this.controlParams.id || 'heading_font' === this.controlParams.id ) {
			let self = this;
			document.addEventListener( 'baseRemoteUpdateFonts', function( e ) {
				if ( e.detail === 'typography' ) {
					self.remoteUpdate();
				}
			} );
		}
	}
	remoteUpdate() {
		let allBaseDefault = {
			'size': {
				'desktop': 18,
			},
			'sizeType': 'px',
			'lineHeight': {
				'desktop': 1.65,
			},
			'lineType': '-',
			'letterSpacing': {
				'desktop': '',
			},
			'spacingType': 'em',
			'family': '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"',
			'google': false,
			'style': 'normal',
			'weight': 'regular',
			'variant': 'regular',
			'color': 'palette4',
			'transform': '',
			'fallback': '',
		};
		let familyBaseDefault = {
			'family': '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"',
			'google': false,
			'fallback': '',
		};
		if ( 'heading_font' === this.controlParams.id ) {
			allBaseDefault = familyBaseDefault;
		}
		let value = this.props.control.setting.get();
		value = value ? {
			...JSON.parse( JSON.stringify( allBaseDefault ) ),
			...value
		} : JSON.parse( JSON.stringify( allBaseDefault ) );
		this.setState( { value: value } );
	}
}

TypographyComponent.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default TypographyComponent;
