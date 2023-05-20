/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';

import ResponsiveControl from '../common/responsive.js';
import ColorControl from '../common/color.js';
import Icons from '../common/icons.js';

import { __ } from '@wordpress/i18n';

const { ButtonGroup, Dashicon, Toolbar, ToolbarGroup, Tooltip, Button } = wp.components;

/**
 * WordPress dependencies
 */
import { createRef, Component, Fragment } from '@wordpress/element';
class SingleBorderComponent extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		this.resetValues = this.resetValues.bind( this );
		this.handleChangeComplete = this.handleChangeComplete.bind( this );
		this.handleResponsiveChangeComplete = this.handleResponsiveChangeComplete.bind( this );
		this.getUnitButtons = this.getUnitButtons.bind( this );
		this.getResponsiveUnitButtons = this.getResponsiveUnitButtons.bind( this );
		this.createLevelControlToolbar = this.createLevelControlToolbar.bind( this );
		this.createResponsiveLevelControlToolbar = this.createResponsiveLevelControlToolbar.bind( this );
		this.getStyleButtons = this.getStyleButtons.bind( this );
		this.getResponsiveStyleButtons = this.getResponsiveStyleButtons.bind( this );
		this.createStyleControlToolbar = this.createStyleControlToolbar.bind( this );
		this.createResponsiveStyleControlToolbar = this.createResponsiveStyleControlToolbar.bind( this );
		let value = this.props.control.settings[this.props.item] ? this.props.control.settings[this.props.item].get() : [];
		let defaultParams = {
			min: {
				px: '0',
				em: '0',
				rem: '0',
			},
			max: {
				px: '300',
				em: '12',
				rem: '12',
			},
			step: {
				px: '1',
				em: '0.01',
				rem: '0.01',
			},
			units: ['px', 'em', 'rem'],
			styles: ['none', 'solid', 'dashed', 'dotted', 'double'],
			responsive:true,
			color:true,
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		let responsiveDefault = {
			'desktop': {
				'width': '',
				'unit': 'px',
				'style': 'none',
				'color': ( this.controlParams.color ? '' : 'currentColor' ),
			}
		};
		let noneResponsiveDefault = {
			'width': '',
			'unit': 'px',
			'style': 'none',
			'color': ( this.controlParams.color ? '' : 'currentColor' ),
		};
		let baseDefault;
		if ( this.controlParams.responsive ) {
			baseDefault = responsiveDefault;
		} else {
			baseDefault = noneResponsiveDefault;
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
			value: value,
		};
		this.anchorNodeRef = createRef();
	}
	handleResponsiveChangeComplete( color, isPalette, device ) {
		let value = this.state.value;
		if ( undefined === value[ device ] ) {
			value[ device ] = {
				'width': '',
				'unit': '',
				'style': '',
				'color': '',
			}
		}
		if ( isPalette ) {
			value[ device ].color = isPalette;
		} else if ( undefined !== color.rgb && undefined !== color.rgb.a && 1 !== color.rgb.a ) {
			value[ device ].color = 'rgba(' +  color.rgb.r + ',' +  color.rgb.g + ',' +  color.rgb.b + ',' + color.rgb.a + ')';
		} else {
			value[ device ].color = color.hex;
		}
		this.updateValues( value );
	}
	handleChangeComplete( color, isPalette ) {
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
		const data = this.props.control.params;
		let currentUnit;
		if ( this.controlParams.responsive ) {
			if ( undefined === this.state.value[ this.props.currentDevice ] ) {
				let largerDevice = ( this.props.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].unit ) {
					currentUnit = this.state.value[largerDevice].unit;
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].unit ) {
					currentUnit = this.state.value['desktop'].unit;
				}
			} else if ( undefined === this.state.value[ this.props.currentDevice ].unit ) {
				let largerDevice = ( this.props.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].unit ) {
					currentUnit = this.state.value[largerDevice].unit;
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].unit ) {
					currentUnit = this.state.value['desktop'].unit;
				}
			} else if ( '' === this.state.value[ this.props.currentDevice ].unit ) {
				let largerDevice = ( this.props.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].unit ) {
					currentUnit = this.state.value[largerDevice].unit;
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].unit ) {
					currentUnit = this.state.value['desktop'].unit;
				}
			} else if ( '' !== this.state.value[ this.props.currentDevice ].unit ) {
				currentUnit = this.state.value[ this.props.currentDevice ].unit
			}
		} else {
			currentUnit = ( undefined !== this.state.value.unit ? this.state.value.unit : 'px' );
		}
		let currentStyle;
		if ( this.controlParams.responsive ) {
			if ( undefined === this.state.value[ this.props.currentDevice ] ) {
				let largerDevice = ( this.props.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].style ) {
					currentStyle = this.state.value[largerDevice].style;
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].style ) {
					currentStyle = this.state.value['desktop'].style;
				}
			} else if ( undefined === this.state.value[ this.props.currentDevice ].style ) {
				let largerDevice = ( this.props.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].style ) {
					currentStyle = this.state.value[largerDevice].style;
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].style ) {
					currentStyle = this.state.value['desktop'].style;
				}
			} else if ( '' === this.state.value[ this.props.currentDevice ].style ) {
				let largerDevice = ( this.props.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
				if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].style ) {
					currentStyle = this.state.value[largerDevice].style;
				} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].style ) {
					currentStyle = this.state.value['desktop'].style;
				}
			} else if ( '' !== this.state.value[ this.props.currentDevice ].style ) {
				currentStyle = this.state.value[ this.props.currentDevice ].style
			}
		} else {
			currentStyle = ( undefined !== this.state.value.style ? this.state.value.style : 'none' );
		}
		const onResponsiveInputChange = ( event ) => {
			const newValue = Number( event.target.value );
			let value = this.state.value;
			if ( undefined === value[ this.props.currentDevice ] ) {
				value[ this.props.currentDevice ] = {
					'width': '',
					'unit': '',
					'style': '',
					'color': '',
				}
			}
			value[ this.props.currentDevice ].width = newValue;
			this.updateValues( value );
		}
		const onInputChange = ( event ) => {
			const newValue = ( '' !== event.target.value ? Number( event.target.value ) : '' );
			let value = this.state.value;
			value.width = newValue;
			this.updateValues( value );
		}
		let theIcon;
		if ( 'border_top' === this.props.item ) {
			theIcon = Icons.outlinetop;
		} else if ( 'border_left' === this.props.item ) {
			theIcon = Icons.outlineleft;
		} else if ( 'border_right' === this.props.item ) {
			theIcon = Icons.outlineright;
		} else if ( 'border_bottom' === this.props.item ) {
			theIcon = Icons.outlinebottom;
		}
		let isDisabled = true;
		if ( this.controlParams.responsive ) {
			if ( ( undefined !== this.state.value['tablet'] ) || ( undefined !== this.state.value['mobile'] ) ) {
				isDisabled = false;
			} else if ( ( 'none' !== this.state.value['desktop'].style ) || ( '' !== this.state.value['desktop'].color ) || ( '' !== this.state.value['desktop'].width ) || ( 'px' !== this.state.value['desktop'].unit ) ) {
				isDisabled = false;
			}
		}
		return (
			<div ref={this.anchorNodeRef} className="base-responsive-controls-content base-border-single-item">
				{ this.controlParams.responsive && (
					<Fragment>
						<Tooltip text={ __( 'Reset Values', 'avanam' ) }>
							<Button
								className="reset base-reset"
								disabled={ isDisabled }
								onClick={ () => {
									this.resetValues();
								} }
							>
								<Dashicon icon='image-rotate' />
							</Button>
						</Tooltip>
						<span class="border-icon">{ theIcon }</span>
						{ 'none' !== currentStyle && (
							<Fragment>
								{ this.controlParams.color && (
									<ColorControl
										presetColors={ this.state.colorPalette }
										color={ ( undefined !== this.state.value[ this.props.currentDevice ] && this.state.value[ this.props.currentDevice ].color ? this.state.value[ this.props.currentDevice ].color : '' ) }
										usePalette={ true }
										tooltip={ __( 'Border Color' ) }
										onChangeComplete={ ( color, isPalette ) => this.handleResponsiveChangeComplete( color, isPalette, this.props.currentDevice ) }
										customizer={ this.props.customizer }
										controlRef={ this.anchorNodeRef }
									/>
								) }
								<input
									value={ ( undefined !== this.state.value[ this.props.currentDevice ] && undefined !== this.state.value[ this.props.currentDevice ].width ? this.state.value[ this.props.currentDevice ].width : '' ) }
									onChange={ onResponsiveInputChange }
									min={this.controlParams.min[currentUnit]}
									max={this.controlParams.max[currentUnit]}
									step={this.controlParams.step[currentUnit]}
									type="number"
									className="components-text-control__input"
								/>
								{ this.controlParams.units && (
									<div className="base-units">
										{ this.getResponsiveUnitButtons() }
									</div>
								) }
							</Fragment>
						) }
						{ this.controlParams.styles && (
							<div className="base-units base-style-options">
								{ this.getResponsiveStyleButtons() }
							</div>
						) }
					</Fragment>
				) }
				{ ! this.controlParams.responsive && (
					<Fragment>
						{ 'none' !== currentStyle && (
							<Fragment>
								{ this.controlParams.color && (
									<ColorControl
										presetColors={ this.state.colorPalette }
										color={ ( undefined !== this.state.value.color && this.state.value.color ? this.state.value.color : '' ) }
										usePalette={ true }
										tooltip={ __( 'Border Color' ) }
										onChangeComplete={ ( color, isPalette ) => this.handleChangeComplete( color, isPalette ) }
										customizer={ this.props.customizer }
										controlRef={ this.anchorNodeRef }
									/>
								) }
								<input
									value={ ( undefined !== this.state.value && undefined !== this.state.value.width ? this.state.value.width : '' ) }
									onChange={ onInputChange }
									min={this.controlParams.min[currentUnit]}
									max={this.controlParams.max[currentUnit]}
									step={this.controlParams.step[currentUnit]}
									type="number"
									className="components-text-control__input"
								/>
								{ this.controlParams.units && (
									<div className="base-units">
										{ this.getUnitButtons() }
									</div>
								) }
							</Fragment>
						) }
						{ this.controlParams.styles && (
							<div className="base-units base-style-options">
								{ this.getStyleButtons() }
							</div>
						) }
					</Fragment>
				) }
			</div>
		);
	}
	getResponsiveStyleButtons() {
		let self = this;
		const { styles } = this.controlParams;
		let currentStyle;
		if ( undefined === this.state.value[ this.props.currentDevice ] ) {
			let largerDevice = ( this.props.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
			if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].style ) {
				currentStyle = this.state.value[largerDevice].style;
			} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].style ) {
				currentStyle = this.state.value['desktop'].style;
			}
		} else if ( undefined === this.state.value[ this.props.currentDevice ].style ) {
			let largerDevice = ( this.props.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
			if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].style ) {
				currentStyle = this.state.value[largerDevice].style;
			} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].style ) {
				currentStyle = this.state.value['desktop'].style;
			}
		} else if ( '' === this.state.value[ this.props.currentDevice ].style ) {
			let largerDevice = ( this.props.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
			if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].style ) {
				currentStyle = this.state.value[largerDevice].style;
			} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].style ) {
				currentStyle = this.state.value['desktop'].style;
			}
		} else if ( '' !== this.state.value[ this.props.currentDevice ].style ) {
			currentStyle = this.state.value[ this.props.currentDevice ].style
		}
		if ( styles.length === 1 ) {
			return ( <Button
					className="is-active is-single"
					isSmall
					disabled
			>{ Icons[ currentStyle ] }</Button> );
		}
		return <ToolbarGroup
			isCollapsed={ true }
			icon={ Icons[ currentStyle ] }
			label={ __( 'Style', 'avanam' ) }
			controls={ styles.map( ( style ) => this.createResponsiveStyleControlToolbar( style ) ) }
		/> 
	}
	createResponsiveStyleControlToolbar( style ) {
		return [ {
			icon: Icons[ style ],
			isActive: ( undefined !== this.state.value[ this.props.currentDevice ] && undefined !== this.state.value[ this.props.currentDevice ].style && this.state.value[ this.props.currentDevice ].style === style ),
			onClick: () => {
				let value = this.state.value;
				if ( undefined === value[ this.props.currentDevice ] ) {
					value[ this.props.currentDevice ] = {
						'width': '',
						'unit': '',
						'style': '',
						'color': '',
					}
				}
				value[ this.props.currentDevice ].style = style;
				this.updateValues( value );
			},
		} ];
	};
	getStyleButtons() {
		let self = this;
		const { styles } = this.controlParams;
		let currentStyle;
		currentStyle = ( undefined !== this.state.value.style ? this.state.value.style : 'none' );
		if ( styles.length === 1 ) {
			return ( <Button
					className="is-active is-single"
					isSmall
					disabled
			>{ Icons[ currentStyle ] }</Button> );
		}
		return <ToolbarGroup
			isCollapsed={ true }
			icon={ Icons[ currentStyle ] }
			label={ __( 'Style', 'avanam' ) }
			controls={ styles.map( ( style ) => this.createStyleControlToolbar( style ) ) }
		/> 
	}
	createStyleControlToolbar( style ) {
		return [ {
			icon: Icons[ style ],
			isActive: ( undefined !== this.state.value && undefined !== this.state.value.style && this.state.value.style === style ),
			onClick: () => {
				let value = this.state.value;
				value.style = style;
				this.updateValues( value );
			},
		} ];
	};
	createResponsiveLevelControlToolbar( unit ) {
		return [ {
			icon: ( unit === '%' ? Icons.percent : Icons[ unit ] ),
			isActive: ( undefined !== this.state.value[ this.props.currentDevice ] && undefined !== this.state.value[ this.props.currentDevice ].unit && this.state.value[ this.props.currentDevice ].unit === unit ),
			onClick: () => {
				let value = this.state.value;
				if ( undefined === value[ this.props.currentDevice ] ) {
					value[ this.props.currentDevice ] = {
						'width': '',
						'unit': '',
						'style': '',
						'color': '',
					}
				}
				value[ this.props.currentDevice ].unit = unit;
				this.updateValues( value );
			},
		} ];
	};
	getResponsiveUnitButtons() {
		let self = this;
		const { units } = this.controlParams;
		let currentUnit;
		if ( undefined === self.state.value[ self.state.currentDevice ] ) {
			let largerDevice = ( self.state.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
			if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].unit ) {
				currentUnit = this.state.value[largerDevice].unit;
			} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].unit ) {
				currentUnit = this.state.value['desktop'].unit;
			}
		} else if ( undefined !== self.state.value[ self.state.currentDevice ].unit ) {
			let largerDevice = ( self.state.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
			if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].unit ) {
				currentUnit = this.state.value[largerDevice].unit;
			} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].unit ) {
				currentUnit = this.state.value['desktop'].unit;
			}
		} else if ( '' === self.state.value[ self.state.currentDevice ].unit ) {
			let largerDevice = ( self.state.currentDevice === 'mobile' ? 'tablet' : 'desktop' );
			if ( undefined !== this.state.value[largerDevice] && this.state.value[largerDevice].unit ) {
				currentUnit = this.state.value[largerDevice].unit;
			} else if ( 'tablet' === largerDevice && undefined !== this.state.value['desktop'] && this.state.value['desktop'].unit ) {
				currentUnit = this.state.value['desktop'].unit;
			}
		} else if ( '' !== self.state.value[ self.state.currentDevice ].unit ) {
			currentUnit = self.state.value[ self.state.currentDevice ].unit
		}
		if ( units.length === 1 ) {
			return ( <Button
					className="is-active is-single"
					isSmall
					disabled
			>{ ( '%' === currentUnit ? Icons.percent : Icons[ currentUnit ] ) }</Button> );
		}
		return <ToolbarGroup
			isCollapsed={ true }
			icon={ ( '%' === currentUnit ? Icons.percent : Icons[ currentUnit ] ) }
			label={ __( 'Unit', 'avanam' ) }
			controls={ units.map( ( unit ) => this.createResponsiveLevelControlToolbar( unit ) ) }
		/> 
	}
	createLevelControlToolbar( unit ) {
		return [ {
			icon: ( unit === '%' ? Icons.percent : Icons[ unit ] ),
			isActive: ( undefined !== this.state.value && undefined !== this.state.value.unit && this.state.value.unit === unit ),
			onClick: () => {
				let value = this.state.value;
				value.unit = unit;
				this.updateValues( value );
			},
		} ];
	};
	getUnitButtons() {
		let self = this;
		const { units } = this.controlParams;
		let currentUnit;
		currentUnit = ( undefined !== this.state.value.unit ? this.state.value.unit : 'px' );
		if ( units.length === 1 ) {
			return ( <Button
					className="is-active is-single"
					isSmall
					disabled
			>{ ( '%' === currentUnit ? Icons.percent : Icons[ currentUnit ] ) }</Button> );
		}
		return <ToolbarGroup
			isCollapsed={ true }
			icon={ ( '%' === currentUnit ? Icons.percent : Icons[ currentUnit ] ) }
			label={ __( 'Unit', 'avanam' ) }
			controls={ units.map( ( unit ) => this.createLevelControlToolbar( unit ) ) }
		/> 
	}
	resetValues() {
		const value = JSON.parse( JSON.stringify( this.defaultValue ) );
		this.setState( { value: value } );
		if ( this.controlParams.responsive ) {
			value.flag = !this.props.control.settings[this.props.item].get().flag;
		}
		this.props.control.settings[this.props.item].set( {
			...value,
		} );
	}
	updateValues( value ) {
		this.setState( { value: value } );
		if ( this.controlParams.responsive ) {
			value.flag = !this.props.control.settings[this.props.item].get().flag;
		}
		this.props.control.settings[this.props.item].set( {
			...this.props.control.settings[this.props.item].get(),
			...value,
		} );
	}
}

SingleBorderComponent.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default SingleBorderComponent;
