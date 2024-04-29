import PropTypes from 'prop-types';
import { __ } from '@wordpress/i18n';
import {
	Component,
	Fragment
} from '@wordpress/element';
import {
	Button,
	Popover,
	Dashicon,
	ButtonGroup,
	ColorIndicator,
	FocalPointPicker,
	Icon,
	Tooltip,
	TabPanel,
	GradientPicker,
	__experimentalGradientPicker,
} from '@wordpress/components';
// const {
// 	MediaUpload,
// } = wp.blockEditor;
// import { SketchPicker } from 'react-color';
// import ColorControl from '../common/color.js';
import BaseGradientPicker from '../gradient-control/index.js';
import BaseColorPicker from '../common/color-picker';
import ResponsiveControl from '../common/responsive';
import SwatchesControl from '../common/swatches';
import Icons from '../common/icons';

class BackgroundComponent extends Component {
	constructor(props) {
		super( props );
		this.onColorChangeComplete = this.onColorChangeComplete.bind( this );
		this.onGradientChangeComplete = this.onGradientChangeComplete.bind( this );
		this.updateValues = this.updateValues.bind( this );
		this.removeValues = this.removeValues.bind( this );
		this.onColorChangeState = this.onColorChangeState.bind( this );
		this.saveBackgroundType = this.saveBackgroundType.bind( this );
		this.preventClose = this.preventClose.bind( this );
		this.onPositionChange = this.onPositionChange.bind( this );
		this.onImageRemove = this.onImageRemove.bind( this );
		this.resetValues = this.resetValues.bind( this );
		this.preventClose();
		let value = this.props.control.setting.get();
		let baseDefault = {
			'desktop': {
				'color': '',
				'image': {
					'url': '',
					'size': 'cover',
					'repeat': 'no-repeat',
					'position': {
						'x': 0.5,
						'y': 0.5
					},
					'attachment': 'scroll',
				},
				'type': 'color',
			},
		};
		this.defaultValue = this.props.control.params.default ? {
			...baseDefault,
			...this.props.control.params.default
		} : baseDefault;
		value = value ? {
			...JSON.parse( JSON.stringify( this.defaultValue ) ),
			...value
		} : JSON.parse( JSON.stringify( this.defaultValue ) );
		let defaultParams = {
			responsive: [ 'mobile', 'tablet', 'desktop' ],
			repeat: {
				'no-repeat': {
					name: __( 'No Repeat', 'avanam' ),
				},
				'repeat': {
					name: __( 'Repeat', 'avanam' ),
				},
				'repeat-x': {
					name: __( 'Repeat-X', 'avanam' ),
				},
				'repeat-y': {
					name: __( 'Repeat-y', 'avanam' ),
				},
			},
			size: {
				auto: {
					name: __( 'Auto', 'avanam' ),
				},
				cover: {
					name: __( 'Cover', 'avanam' ),
				},
				contain: {
					name: __( 'Contain', 'avanam' ),
				},
			},
			attachment: {
				scroll: {
					name: __( 'Scroll', 'avanam' ),
				},
				fixed: {
					name: __( 'Fixed', 'avanam' ),
				},
			},
			attachments: {
				desktop: {},
				tablet: {},
				mobile: {},
			},
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		const palette = JSON.parse( this.props.customizer.control( 'base_color_palette' ).setting.get() );
		//console.log( palette );
		this.state = {
			value: value,
			currentDevice: 'desktop',
			colorPalette: palette,
			activePalette: ( palette && palette.active ? palette.active : 'palette' ),
			isVisible: false,
			refresh: true,
			modalCanClose: true,
			supportGradient: ( undefined === GradientPicker && undefined === __experimentalGradientPicker ? false : true ),
		};
	}
	onColorChangeState( color, palette, device ) {
		let value = this.state.value;
		if ( undefined === value[ device ] ) {
			value[ device ] = {}
		}
		if ( undefined === value[ device ].color ) {
			value[ device ].color = '';
		}
		if ( palette ) {
			value[ device ].color = palette;
		} else if ( undefined !== color.rgb && undefined !== color.rgb.a && 1 !== color.rgb.a ) {
			value[ device ].color = 'rgba(' +  color.rgb.r + ',' +  color.rgb.g + ',' +  color.rgb.b + ',' + color.rgb.a + ')';
		} else {
			value[ device ].color = color.hex;
		}
		this.setState( { value: value } );
	}
	onGradientChangeComplete( gradient, device ) {
		let value = this.state.value;
		if ( undefined === value[ device ] ) {
			value[ device ] = {}
		}
		if ( undefined === value[ device ].gradient ) {
			value[ device ].gradient = '';
		}
		if ( undefined === gradient ) {
			value[ device ].gradient = '';
		} else {
			value[ device ].gradient = gradient;
		}
		this.updateValues( value );
	}
	onColorChangeComplete( color, palette, device ) {
		let value = this.state.value;
		if ( undefined === value[ device ] ) {
			value[ device ] = {}
		}
		if ( undefined === value[ device ].color ) {
			value[ device ].color = '';
		}
		if ( palette ) {
			value[ device ].color = palette;
		} else if ( undefined !== color.rgb && undefined !== color.rgb.a && 1 !== color.rgb.a ) {
			value[ device ].color = 'rgba(' +  color.rgb.r + ',' +  color.rgb.g + ',' +  color.rgb.b + ',' + color.rgb.a + ')';
		} else {
			value[ device ].color = color.hex;
		}
		this.updateValues( value );
	}
	saveBackgroundType( tab, device ) {
		let value = this.state.value;
		if ( tab ) {
			if ( undefined === value[ device ] ) {
				value[ device ] = {}
			}
			value[ device ].type = tab;
		}
		this.updateValues( value );
	}
	onPositionChange( position, device ) {
		let value = this.state.value;
		if ( position && position.focalPoint  ) {
			if ( undefined === value[ device ] ) {
				value[ device ] = {}
			}
			if ( undefined === value[ device ].image ) {
				value[ device ].image = {};
			}
			if ( undefined === value[ device ].image.position ) {
				value[ device ].image.position = {};
			}
			value[ device ].image.position = position.focalPoint;
		}
		this.updateValues( value );
	}
	onImageRemove( device ) {
		if ( undefined !== this.props.control.params.attachment && this.props.control.params.attachment && undefined !== this.props.control.params.attachment[device] ) {
			this.props.control.params.attachment[device] = {};
		}
		let value = this.state.value;
		if ( value[device] ) {
			value[device].image.url = '';
		}
		this.updateValues( value );
	}
	render() {
		const data = this.props.control.params;
		const toggleVisible = () => {
			const updateColors = JSON.parse( this.props.customizer.control( 'base_color_palette' ).setting.get() );
			const active = ( updateColors && updateColors.active ? updateColors.active : 'palette' );
			this.setState( { colorPalette: updateColors, activePalette: active } );
			this.setState( { isVisible: true } );
		};
		const toggleClose = () => {
			if ( this.state.isVisible === true ) {
				this.setState( { isVisible: false } );
			}
		};
		const maybeToggleClose = ( e ) => {
			if ( undefined !== this.props.control.frame ) {
				if ( this.state.modalCanClose ) {
					if ( this.state.isVisible === true ) {
						this.setState( { isVisible: false } );
					}
				}
			} else {
				if ( this.state.isVisible === true ) {
					this.setState( { isVisible: false } );
				}
			}
		}
		const dimensions = {
			desktop: {
				width: ( undefined !== this.controlParams.attachments && 'object' === typeof this.controlParams.attachments && undefined !== this.controlParams.attachments.desktop && 'object' === typeof this.controlParams.attachments.desktop && this.controlParams.attachments.desktop && undefined !== this.controlParams.attachments.desktop.width ? this.controlParams.attachments.desktop.width : 400 ),
				height: ( undefined !== this.controlParams.attachments && 'object' === typeof this.controlParams.attachments && undefined !== this.controlParams.attachments.desktop && 'object' === typeof this.controlParams.attachments.desktop && this.controlParams.attachments.desktop && undefined !== this.controlParams.attachments.desktop.height ? this.controlParams.attachments.desktop.height : 400 ),
			},
			tablet: {
				width: ( undefined !== this.controlParams.attachments && 'object' === typeof this.controlParams.attachments && undefined !== this.controlParams.attachments.tablet && 'object' === typeof this.controlParams.attachments.tablet && this.controlParams.attachments.tablet && undefined !== this.controlParams.attachments.tablet.width ? this.controlParams.attachments.tablet.width : 400 ),
				height: ( undefined !== this.controlParams.attachments && 'object' === typeof this.controlParams.attachments && undefined !== this.controlParams.attachments.tablet && 'object' === typeof this.controlParams.attachments.tablet && this.controlParams.attachments.tablet && undefined !== this.controlParams.attachments.tablet.height ? this.controlParams.attachments.tablet.height : 400 ),
			},
			mobile: {
				width: ( undefined !== this.controlParams.attachments && 'object' === typeof this.controlParams.attachments && undefined !== this.controlParams.attachments.mobile && 'object' === typeof this.controlParams.attachments.mobile && this.controlParams.attachments.mobile && undefined !== this.controlParams.attachments.mobile.width ? this.controlParams.attachments.mobile.width : 400 ),
				height: ( undefined !== this.controlParams.attachments && 'object' === typeof this.controlParams.attachments && undefined !== this.controlParams.attachments.mobile && 'object' === typeof this.controlParams.attachments.mobile && this.controlParams.attachments.mobile && undefined !== this.controlParams.attachments.mobile.height ? this.controlParams.attachments.mobile.height : 400 ),
			},
		};
		const getColorValue = ( device ) => {
			let color = '';
			if ( undefined === this.state.value[device] ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].color && this.state.value[largerDevice].color ) {
					if ( this.state.value[largerDevice].color.includes( 'palette' ) ) {
						color = this.state.colorPalette[ this.state.activePalette ][ parseInt( this.state.value[largerDevice].color.slice(-1), 10 ) - 1 ].color;
					} else {
						color = this.state.value[largerDevice].color;
					}
				} else if ( 'tablet' === largerDevice ) {
					if ( this.state.value['desktop'].color && this.state.value['desktop'].color.includes( 'palette' ) ) {
						color = this.state.colorPalette[ this.state.activePalette ][ parseInt( this.state.value['desktop'].color.slice(-1), 10 ) - 1 ].color;
					} else {
						color = this.state.value['desktop'].color;
					}
				}
			} else if ( undefined === this.state.value[device].color ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].color && this.state.value[largerDevice].color ) {
					if ( this.state.value[largerDevice].color.includes( 'palette' ) ) {
						color = this.state.colorPalette[ this.state.activePalette ][ parseInt( this.state.value[largerDevice].color.slice(-1), 10 ) - 1 ].color;
					} else {
						color = this.state.value[largerDevice].color;
					}
				} else if ( 'tablet' === largerDevice ) {
					if ( this.state.value['desktop'].color && this.state.value['desktop'].color.includes( 'palette' ) ) {
						color = this.state.colorPalette[ this.state.activePalette ][ parseInt( this.state.value['desktop'].color.slice(-1), 10 ) - 1 ].color;
					} else {
						color = this.state.value['desktop'].color;
					}
				}
			} else if ( '' !== this.state.value[device].color && null !== this.state.value[device].color && this.state.value[device].color.includes( 'palette' ) && this.state.colorPalette[ this.state.activePalette ] && this.state.colorPalette[ this.state.activePalette ][parseInt(this.state.value[device].color.slice(-1), 10 ) - 1] ) {
				color = this.state.colorPalette[ this.state.activePalette ][parseInt(this.state.value[device].color.slice(-1), 10 ) - 1].color;
			} else if ( null === this.state.value[device].color ) {
				color = '';
			} else {
				color = this.state.value[device].color;
			}
			return color;
		}
		const getColorPreviewValue = ( device ) => {
			let color = null;
			if ( undefined === this.state.value[device] ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].color && this.state.value[largerDevice].color ) {
					color = ( this.state.value[largerDevice].color.includes( 'palette' ) ? 'var(--global-' + this.state.value[largerDevice].color + ')' : this.state.value[largerDevice].color );
				} else if ( 'tablet' === largerDevice ) {
					color = ( this.state.value['desktop'].color.includes( 'palette' ) ? 'var(--global-' + this.state.value['desktop'].color + ')' : this.state.value['desktop'].color );
				}
			} else if ( undefined === this.state.value[device].color ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].color && this.state.value[largerDevice].color ) {
					color = ( this.state.value[largerDevice].color.includes( 'palette' ) ? 'var(--global-' + this.state.value[largerDevice].color + ')' : this.state.value[largerDevice].color );
				} else if ( 'tablet' === largerDevice ) {
					color = ( this.state.value['desktop'].color.includes( 'palette' ) ? 'var(--global-' + this.state.value['desktop'].color + ')' : this.state.value['desktop'].color );
				}
			} else if ( '' === this.state.value[device].color ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].color && this.state.value[largerDevice].color ) {
					color = ( this.state.value[largerDevice].color.includes( 'palette' ) ? 'var(--global-' + this.state.value[largerDevice].color + ')' : this.state.value[largerDevice].color );
				} else if ( 'tablet' === largerDevice ) {
					color = ( this.state.value['desktop'].color.includes( 'palette' ) ? 'var(--global-' + this.state.value['desktop'].color + ')' : this.state.value['desktop'].color );
				}
			} else if ( '' !== this.state.value[device].color && null !== this.state.value[device].color && this.state.value[device].color.includes( 'palette' ) ) {
				color = 'var(--global-' + this.state.value[device].color + ')';
			} else if ( null === this.state.value[device].color ) {
				color = '';
			} else {
				color = this.state.value[device].color
			}
			return color;
		}
		const getGradientPreviewValue = ( device ) => {
			let gradient;
			if ( undefined === this.state.value[device] ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].gradient && this.state.value[largerDevice].gradient ) {
					gradient = this.state.value[largerDevice].gradient;
				} else if ( 'tablet' === largerDevice ) {
					gradient = this.state.value['desktop'].gradient;
				}
			} else if ( undefined === this.state.value[device].gradient ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].gradient && this.state.value[largerDevice].gradient ) {
					gradient = this.state.value[largerDevice].gradient;
				} else if ( 'tablet' === largerDevice ) {
					gradient = this.state.value['desktop'].gradient;
				}
			} else if ( '' === this.state.value[device].gradient ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].gradient && this.state.value[largerDevice].gradient ) {
					gradient = this.state.value[largerDevice].gradient;
				} else if ( 'tablet' === largerDevice ) {
					gradient = this.state.value['desktop'].gradient;
				}
			} else {
				gradient = this.state.value[device].gradient
			}
			return gradient;
		}
		const getRadioClassName = ( item, device, control ) => {
			let itemClass;
			if ( undefined === this.state.value[device] ) {
				itemClass = item;
			} else if ( undefined === this.state.value[device].image ) {
				itemClass = item;
			} else if ( undefined === this.state.value[ device ].image[ control ] ) {
				itemClass = item;
			} else if ( item === this.state.value[ device ].image[ control ] ) {
				itemClass = 'active-radio ' + item;
			} else {
				itemClass = item;
			}
			return itemClass;
		}
		const showImagePreview = ( device ) => {
			let showImagePreview = false;
			if ( undefined === this.state.value[ device ] ) {
				showImagePreview = true;
				if ( device === 'mobile' ) {
					if ( undefined !== this.state.value['tablet'] && undefined !== this.state.value['tablet'].color && '' !== this.state.value['tablet'].color ) {
						showImagePreview = false;
					}
				}
			} else if ( undefined === this.state.value[ device ].color ) {
				showImagePreview = true;
				if ( device === 'mobile' ) {
					if ( undefined !== this.state.value['tablet'] && undefined !== this.state.value['tablet'].color && '' !== this.state.value['tablet'].color ) {
						showImagePreview = false;
					}
				}
			}
			return showImagePreview;
		}
		const showGradientPreview = ( device ) => {
			let showGradientPreview = false;
			if ( undefined === this.state.value[ device ] ) {
				showGradientPreview = true;
				if ( device === 'mobile' ) {
					if ( undefined !== this.state.value['tablet'] && undefined !== this.state.value['tablet'].type && '' !== this.state.value['tablet'].type ) {
						showGradientPreview = false;
					}
				}
			} else if ( undefined === this.state.value[ device ].color ) {
				showGradientPreview = true;
				if ( device === 'mobile' ) {
					if ( undefined !== this.state.value['tablet'] && undefined !== this.state.value['tablet'].type && '' !== this.state.value['tablet'].type ) {
						showGradientPreview = false;
					}
				}
			}
			return showGradientPreview;
		}
		const getImagePreview = ( device ) => {
			let imagePreview;
			if ( undefined === this.state.value[ device ] ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].image && this.state.value[largerDevice].image.url ) {
					imagePreview = <img className="base-background-image-preview" src={ ( undefined !== this.controlParams.attachments && undefined !== this.controlParams.attachments[largerDevice] && undefined !== this.controlParams.attachments[largerDevice].thumbnail ? this.controlParams.attachments[largerDevice].thumbnail : this.state.value[largerDevice].image.url ) } />
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'].image && this.state.value['desktop'].image.url ) {
					imagePreview = <img className="base-background-image-preview" src={ ( undefined !== this.controlParams.attachments && undefined !== this.controlParams.attachments['desktop'] && this.controlParams.attachments['desktop'] && undefined !== this.controlParams.attachments['desktop'].thumbnail ? this.controlParams.attachments['desktop'].thumbnail : this.state.value['desktop'].image.url ) } />
				}
			} else if ( undefined === this.state.value[ device ].image ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].image && this.state.value[largerDevice].image.url ) {
					imagePreview = <img className="base-background-image-preview" src={ ( undefined !== this.controlParams.attachments && undefined !== this.controlParams.attachments[largerDevice] && undefined !== this.controlParams.attachments[largerDevice].thumbnail ? this.controlParams.attachments[largerDevice].thumbnail : this.state.value[largerDevice].image.url ) } />
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'].image && this.state.value['desktop'].image.url ) {
					imagePreview = <img className="base-background-image-preview" src={ ( undefined !== this.controlParams.attachments && undefined !== this.controlParams.attachments['desktop'] && this.controlParams.attachments['desktop'] && undefined !== this.controlParams.attachments['desktop'].thumbnail ? this.controlParams.attachments['desktop'].thumbnail : this.state.value['desktop'].image.url ) } />
				}
			} else if ( undefined === this.state.value[ device ].image.url ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].image && this.state.value[largerDevice].image.url ) {
					imagePreview = <img className="base-background-image-preview" src={ ( undefined !== this.controlParams.attachments && undefined !== this.controlParams.attachments[largerDevice] && undefined !== this.controlParams.attachments[largerDevice].thumbnail ? this.controlParams.attachments[largerDevice].thumbnail : this.state.value[largerDevice].image.url ) } />
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'].image && this.state.value['desktop'].image.url ) {
					imagePreview = <img className="base-background-image-preview" src={ ( undefined !== this.controlParams.attachments && undefined !== this.controlParams.attachments['desktop'] && this.controlParams.attachments['desktop'] && undefined !== this.controlParams.attachments['desktop'].thumbnail ? this.controlParams.attachments['desktop'].thumbnail : this.state.value['desktop'].image.url ) } />
				}
			} else if ( '' === this.state.value[ device ].image.url ) {
				let largerDevice = ( device === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && undefined !== this.state.value[largerDevice].image && this.state.value[largerDevice].image.url ) {
					imagePreview = <img className="base-background-image-preview" src={ ( undefined !== this.controlParams.attachments && undefined !== this.controlParams.attachments[largerDevice] && undefined !== this.controlParams.attachments[largerDevice].thumbnail ? this.controlParams.attachments[largerDevice].thumbnail : this.state.value[largerDevice].image.url ) } />
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'].image && this.state.value['desktop'].image.url ) {
					imagePreview = <img className="base-background-image-preview" src={ ( undefined !== this.controlParams.attachments && undefined !== this.controlParams.attachments['desktop'] && this.controlParams.attachments['desktop'] && undefined !== this.controlParams.attachments['desktop'].thumbnail ? this.controlParams.attachments['desktop'].thumbnail : this.state.value['desktop'].image.url ) } />
				}
			} else if ( '' !== this.state.value[ device ].image.url ) {
				imagePreview = <img className="base-background-image-preview" src={ ( undefined !== this.controlParams.attachments && undefined !== this.controlParams.attachments[device] && this.controlParams.attachments[device] && undefined !== this.controlParams.attachments[device].thumbnail ? this.controlParams.attachments[device].thumbnail : this.state.value[device].image.url ) } />
			} else {
				imagePreview = '';
			}
			return imagePreview;
		}
		const controlLabel = (
			<Fragment>
				{ this.state.currentDevice !== 'desktop' && (
					<Tooltip text={ __( 'Reset Device Values', 'avanam' ) }>
						<Button
							className="reset base-reset"
							disabled={ ( undefined === this.state.value[this.state.currentDevice] ) }
							onClick={ () => {
								let value = this.state.value;
								delete value[this.state.currentDevice];
								this.removeValues( value );
							} }
						>
							<Dashicon icon='image-rotate' />
						</Button>
					</Tooltip>
				) }
				{ this.state.currentDevice === 'desktop' && (
					<Tooltip text={ __( 'Reset Values', 'avanam' ) }>
						<Button
							className="reset base-reset"
							disabled={ ( this.state.value.desktop.color === this.defaultValue.desktop.color && undefined === this.state.value.desktop.type ) }
							onClick={ () => {
								this.resetValues();
							} }
						>
							<Dashicon icon='image-rotate' />
						</Button>
					</Tooltip>
				) }
				{ data.label &&
					data.label
				}
			</Fragment>
		);
		let colorString = getColorPreviewValue( this.state.currentDevice );
		const styleing = {
			saturation: {
				paddingBottom: '50%',
				width: '100%',
				position: 'relative',
				overflow: 'hidden',
			},
		};
		let initial_tab = ( undefined !== this.state.value[ this.state.currentDevice ] && undefined !== this.state.value[this.state.currentDevice].type ? this.state.value[this.state.currentDevice].type: 'color' );
		let tab_options;
		if( this.state.supportGradient ) {
			tab_options = [
				{
					name: 'color',
					title: __( 'Color', 'avanam' ),
					className: 'base-color-background',
				},
				{
					name: 'gradient',
					title: __( 'Gradient', 'avanam' ),
					className: 'base-gradient-background',
				},
				{
					name: 'image',
					title: __( 'Image', 'avanam' ),
					className: 'base-image-background',
				},
			];
		} else {
			tab_options = [
				{
					name: 'color',
					title: __( 'Color', 'avanam' ),
					className: 'base-color-background',
				},
				{
					name: 'image',
					title: __( 'Image', 'avanam' ),
					className: 'base-image-background',
				},
			];
			if ( 'gradient' === initial_tab ) {
				initial_tab = 'color';
			}
		}
		return (
			<div className="base-control-field base-background-control">
				<ResponsiveControl
					onChange={ ( currentDevice) => this.setState( { currentDevice } ) }
					controlLabel={ controlLabel }
				>
					<div className="base-background-picker-wrap">
						{ this.state.isVisible && (
							<Popover position="top left" className="base-popover-color base-customizer-popover" inline={ true } onClose={ maybeToggleClose }>
								<TabPanel className="base-popover-tabs base-background-tabs"
									activeClass="active-tab"
									initialTabName={ initial_tab }
									onSelect={ ( value ) => this.saveBackgroundType( value, this.state.currentDevice ) }
									tabs={ tab_options }>
									{
										( tab ) => {
											let tabout;
											if ( tab.name ) {
												if ( 'image' === tab.name ) {
													tabout = (
														<>
															{ undefined === this.state.value[ this.state.currentDevice ] && (
																	<div className="attachment-media-view">
																		<button type="button" className="upload-button button-add-media">{ data.button_labels.select }</button>
																	</div>
															) }
															{ undefined !== this.state.value[ this.state.currentDevice ] && (
																<>
																	{ ! this.state.value[ this.state.currentDevice ].image && (
																		<>
																		{/* <MediaUpload
																			onSelect={ media => {
																				let value = this.state.value;
																				if ( undefined === value[ this.state.currentDevice ] ) {
																					value[ this.state.currentDevice ] = {};
																				}
																				if ( undefined === value[ this.state.currentDevice ].image ) {
																					value[ this.state.currentDevice ].image = {};
																				}
																				if ( undefined === value[ this.state.currentDevice ].image.url ) {
																					value[ this.state.currentDevice ].image.url = '';
																				}
																				value[ this.state.currentDevice ].image.url = media.url;
																				this.updateValues( value );
																			} }
																			type="image"
																			value={ '' }
																			allowedTypes={ [ 'image' ] }
																			render={ ( { open } ) => (
																				<Button
																					className={ 'upload-button button-add-media' }
																					onClick={ open }
																				>
																					{data.button_labels.select}
																				</Button>
																			) }
																		/> */}
																		<div className="attachment-media-view">
																			<button type="button" className="upload-button button-add-media">{ data.button_labels.select }</button>
																		</div>
																		</>
																	) }
																	{ this.state.value[ this.state.currentDevice ].image && ! this.state.value[ this.state.currentDevice ].image.url && (
																		<div className="attachment-media-view">
																			<button type="button" className="upload-button button-add-media">{ data.button_labels.select }</button>
																		</div>
																	) }
																	{ this.state.value[ this.state.currentDevice ].image && this.state.value[ this.state.currentDevice ].image.url && (
																		<>
																			<FocalPointPicker 
																				url={ this.state.value[ this.state.currentDevice ].image.url }
																				dimensions={ dimensions[ this.state.currentDevice ] }
																				value={ ( undefined !== this.state.value[ this.state.currentDevice ].image.position ? this.state.value[ this.state.currentDevice ].image.position : { x: 0.5, y: 0.5 } ) }
																				onChange={ ( focalPoint ) => this.onPositionChange( { focalPoint }, this.state.currentDevice ) } 
																			/>
																			<div class="actions">
																				<Button type="button" className="button remove-button" onClick={ () => this.onImageRemove( this.state.currentDevice ) } >{ data.button_labels.remove }</Button>
																				<Button type="button" className="button upload-button control-focus">{ data.button_labels.change }</Button>
																			</div>
																		</>
																	) }
																</>
															) }
															<span className="customize-control-title">{ __( 'Background Repeat', 'avanam' ) }</span>
															<ButtonGroup className="base-radio-container-control">
																{ Object.keys( this.controlParams.repeat ).map( ( item ) => {
																	return (
																		<Button
																			isTertiary
																			className={ getRadioClassName( item, this.state.currentDevice, 'repeat' ) }
																					onClick={ () => {
																						let value = this.state.value;
																						if ( undefined === value[ this.state.currentDevice ] ) {
																							value[ this.state.currentDevice ] = {};
																						}
																						if ( undefined === value[ this.state.currentDevice ].image ) {
																							value[ this.state.currentDevice ].image = {};
																						}
																						if ( undefined === value[ this.state.currentDevice ].image.repeat ) {
																							value[ this.state.currentDevice ].image.repeat = '';
																						}
																						value[ this.state.currentDevice ].image.repeat = item;
																						this.updateValues( value );
																					} }
																		>
																			{ this.controlParams.repeat[ item ].name && (
																					this.controlParams.repeat[ item ].name
																			) }
																		</Button>
																	);
																} ) }
															</ButtonGroup>
															<span className="customize-control-title">{ __( 'Background Size', 'avanam' ) }</span>
															<ButtonGroup className="base-radio-container-control">
																{ Object.keys( this.controlParams.size ).map( ( item ) => {
																	return (
																		<Button
																			isTertiary
																			className={ getRadioClassName( item, this.state.currentDevice, 'size' ) }
																					onClick={ () => {
																						let value = this.state.value;
																						if ( undefined === value[ this.state.currentDevice ] ) {
																							value[ this.state.currentDevice ] = {};
																						}
																						if ( undefined === value[ this.state.currentDevice ].image ) {
																							value[ this.state.currentDevice ].image = {};
																						}
																						if ( undefined === value[ this.state.currentDevice ].image.size ) {
																							value[ this.state.currentDevice ].image.size = '';
																						}
																						value[ this.state.currentDevice ].image.size = item;
																						this.updateValues( value );
																					} }
																		>
																			{ this.controlParams.size[ item ].name && (
																					this.controlParams.size[ item ].name
																			) }
																		</Button>
																	);
																} ) }
															</ButtonGroup>
															<span className="customize-control-title">{ __( 'Background Attachment', 'avanam' ) }</span>
															<ButtonGroup className="base-radio-container-control">
																{ Object.keys( this.controlParams.attachment ).map( ( item ) => {
																	return (
																		<Button
																			isTertiary
																			className={ getRadioClassName( item, this.state.currentDevice, 'attachment' ) }
																					onClick={ () => {
																						let value = this.state.value;
																						if ( undefined === value[ this.state.currentDevice ] ) {
																							value[ this.state.currentDevice ] = {};
																						}
																						if ( undefined === value[ this.state.currentDevice ].image ) {
																							value[ this.state.currentDevice ].image = {};
																						}
																						if ( undefined === value[ this.state.currentDevice ].image.attachment ) {
																							value[ this.state.currentDevice ].image.attachment = '';
																						}
																						value[ this.state.currentDevice ].image.attachment = item;
																						this.updateValues( value );
																					} }
																		>
																			{ this.controlParams.attachment[ item ].name && (
																					this.controlParams.attachment[ item ].name
																			) }
																		</Button>
																	);
																} ) }
															</ButtonGroup>
														</>
													);
												} else if ( 'gradient' === tab.name ) {
														tabout = (
															<>
																<BaseGradientPicker
																	value={ undefined !== this.state.value[ this.state.currentDevice ].gradient && '' !== this.state.value[ this.state.currentDevice ].gradient ? this.state.value[ this.state.currentDevice ].gradient : '' }
																	onChange={ ( newGradient ) => this.onGradientChangeComplete( newGradient, this.state.currentDevice ) }
																	activePalette={ ( this.state.colorPalette[ this.state.activePalette ] ? this.state.colorPalette[ this.state.activePalette ] : [] ) }
																/>
															</>
														);
												} else {
													tabout = (
														<>
															{ this.state.isVisible && this.state.refresh && (
																<div className="base-background-color-wrap">
																	<BaseColorPicker
																		//presetColors={ [] }
																		color={ getColorValue( this.state.currentDevice ) }
																		onChange={ ( color ) => this.onColorChangeState( color, '', this.state.currentDevice ) }
																		onChangeComplete={ ( color ) => this.onColorChangeComplete( color, '', this.state.currentDevice ) }
																		//width={ 300 }
																		//styles={ styleing }
																	/>
																	<SwatchesControl
																		colors={ ( this.state.colorPalette[ this.state.activePalette ] ? this.state.colorPalette[ this.state.activePalette ] : [] ) }
																		isPalette={ ( undefined !== this.state.value[ this.state.currentDevice ] && undefined !== this.state.value[ this.state.currentDevice ].color && '' !== this.state.value[ this.state.currentDevice ].color && null !== this.state.value[ this.state.currentDevice ].color && this.state.value[ this.state.currentDevice ].color.includes( 'palette' ) ? this.state.value[ this.state.currentDevice ].color : '' ) }
																		onClick={ ( color, palette ) => this.onColorChangeComplete( color, palette, this.state.currentDevice ) }
																	/>
																</div>
															) }
															{ this.state.isVisible && ! this.state.refresh && (
																<div className="base-background-color-wrap">
																	<BaseColorPicker
																		//presetColors={ [] }
																		color={ getColorValue( this.state.currentDevice ) }
																		onChange={ ( color ) => this.onColorChangeState( color, '', this.state.currentDevice ) }
																		onChangeComplete={ ( color ) => this.onColorChangeComplete( color, '', this.state.currentDevice ) }
																		//width={ 300 }
																		//styles={ styleing }
																	/>
																	<SwatchesControl
																		colors={ ( this.state.colorPalette[ this.state.activePalette ] ? this.state.colorPalette[ this.state.activePalette ] : [] ) }
																		isPalette={ ( undefined !== this.state.value[ this.state.currentDevice ] && undefined !== this.state.value[ this.state.currentDevice ].color && '' !== this.state.value[ this.state.currentDevice ].color && this.state.value[ this.state.currentDevice ].color.includes( 'palette' ) ? this.state.value[ this.state.currentDevice ].color : '' ) }
																		onClick={ ( color, palette ) => this.onColorChangeComplete( color, palette, this.state.currentDevice ) }
																	/>
																</div>
															) }
														</>
													);
												}
											}
											return <div>{ tabout }</div>;
										}
									}
								</TabPanel>
							</Popover>
						) }
						<Tooltip text={ this.controlParams.tooltip ? this.controlParams.tooltip : __( 'Select Background', 'avanam' ) }>
							<div className="background-button-wrap">
								<Button className={ 'base-background-icon-indicate' } onClick={ () => { this.state.isVisible ? toggleClose() : toggleVisible() } }>
									{ ( undefined === this.state.value[ this.state.currentDevice ] || undefined === this.state.value[ this.state.currentDevice ].type || this.state.value[ this.state.currentDevice ].type === 'color' ) && (
										<Fragment>
											<ColorIndicator className="base-advanced-color-indicate" colorValue={ getColorPreviewValue( this.state.currentDevice ) } />
											{ undefined !== colorString && '' !== colorString && null !== colorString && 'var' === colorString.substring(0, 3) && (
													<Icon className="dashicon" icon={ Icons.globe }/>
											) }
											{ showImagePreview( this.state.currentDevice ) &&
												getImagePreview( this.state.currentDevice )
											}
											{ showGradientPreview( this.state.currentDevice ) &&
												<ColorIndicator className="base-advanced-color-indicate" colorValue={ getGradientPreviewValue( this.state.currentDevice ) } />
											}
										</Fragment>
									) }
									{ ( undefined !== this.state.value[ this.state.currentDevice ] && undefined !== this.state.value[ this.state.currentDevice ].type && this.state.value[ this.state.currentDevice ].type === 'gradient' ) && (
										<Fragment>
											<ColorIndicator className="base-advanced-color-indicate" colorValue={ getGradientPreviewValue( this.state.currentDevice ) } />
										</Fragment>
									) }
									{ undefined !== this.state.value[ this.state.currentDevice ] && undefined !== this.state.value[ this.state.currentDevice ].type && this.state.value[ this.state.currentDevice ].type === 'image' && (
										<Fragment>
											<ColorIndicator className="base-advanced-color-indicate" colorValue={ getColorPreviewValue( this.state.currentDevice ) } />
											{ undefined !== colorString && '' !== colorString && null !== colorString && 'var' === colorString.substring(0, 3) && (
													<Icon className="dashicon" icon={ Icons.globe }/>
											) }
											{ getImagePreview( this.state.currentDevice ) }
										</Fragment>
									) }
								</Button>
							</div>
						</Tooltip>
					</div>
				</ResponsiveControl>
			</div>
		);
	}
	preventClose() {
		let self = this;
		document.addEventListener( 'baseOpenMediaModal', function(e) {
			self.setState( { modalCanClose: e.detail } );
		} );
	}
	updateValues( value ) {
		this.setState( { value: value } );
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
			flag: ! this.props.control.setting.get().flag
		} );
	}
	removeValues( value ) {
		this.setState( { value: value } );
		this.props.control.setting.set( {
			...value,
			flag: ! this.props.control.setting.get().flag
		} );
	}
	resetValues() {
		this.setState( { value: JSON.parse( JSON.stringify( this.defaultValue ) ) } );
		this.props.control.setting.set( {
			...this.defaultValue,
			flag: ! this.props.control.setting.get().flag
		} );
	}
}

BackgroundComponent.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default BackgroundComponent;
