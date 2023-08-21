import PropTypes from 'prop-types';
import { __ } from '@wordpress/i18n';
const { SelectControl } = wp.components;
import ColorControl from '../common/color.js';
/**
 * WordPress dependencies
 */
import { createRef, Component, Fragment } from '@wordpress/element';

class ColorLinkComponent extends Component {
	constructor(props) {
		super( props );
		this.handleChangeComplete = this.handleChangeComplete.bind( this );
		this.updateValues = this.updateValues.bind( this );
		let value = this.props.control.setting.get();
		let baseDefault = {
			'highlight': '',
			'highlight-alt': '',
			'highlight-alt2': '',
			'style': 'standard',
		};
		this.defaultValue = this.props.control.params.default ? {
			...baseDefault,
			...this.props.control.params.default
		} : baseDefault;
		value = value ? {
			...this.defaultValue,
			...value
		} : this.defaultValue;
		let defaultParams = {
			colors: {
				'highlight': {
					tooltip: __( 'Initial', 'avanam' ),
					palette: true,
				},
				'highlight-alt': {
					tooltip: __( 'Hover', 'avanam' ),
					palette: true,
				},
				'highlight-alt2': {
					tooltip: __( 'Text Alt', 'avanam' ),
					palette: true,
				},
			},
			styles: {
				'standard': {
					label: __( 'Standard (underline)', 'avanam' ),
				},
				'color-underline': {
					label: __( 'Highlight Underline', 'avanam' ),
				},
				'no-underline': {
					label: __( 'No Underline', 'avanam' ),
				},
				'hover-background': {
					label: __( 'Background on Hover', 'avanam' ),
				},
				'offset-background': {
					label: __( 'Offset Background', 'avanam' ),
				},
			},
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		const palette = JSON.parse( this.props.customizer.control( 'base_color_palette' ).setting.get() );
		this.state = {
			value: value,
			colorPalette: palette,
		};
		this.anchorNodeRef = createRef();
	}
	handleChangeComplete( color, isPalette, item ) {
		let value = this.state.value;
		if ( isPalette ) {
			value[ item ] = isPalette;
		} else if ( undefined !== color.rgb && undefined !== color.rgb.a && 1 !== color.rgb.a ) {
			value[ item ] = 'rgba(' +  color.rgb.r + ',' +  color.rgb.g + ',' +  color.rgb.b + ',' + color.rgb.a + ')';
		} else {
			value[ item ] = color.hex;
		}
		document.documentElement.style.setProperty('--global-palette-' + item, value[ item ] );
		this.updateValues( value );
	}

	render() {
		const styleOptions = Object.keys( this.controlParams.styles ).map( ( item ) => { 
			return ( { label: this.controlParams.styles[ item ].label, value: item } );
		} );
		const hoverBackgroundColors = {
			'highlight': {
				tooltip: __( 'Initial/Background', 'avanam' ),
				palette: true,
			},
			'highlight-alt': {
				tooltip: __( 'Unused', 'avanam' ),
				palette: true,
			},
			'highlight-alt2': {
				tooltip: __( 'Text Hover', 'avanam' ),
				palette: true,
			},
		};

		// Use special color labels for hover background;
		const colorsToUse = this.state.value.style === 'hover-background' ? hoverBackgroundColors : this.controlParams.colors;

		return (
			<Fragment>
				<div className="base-control-field base-color-control base-link-color-control">
					<span className="customize-control-title">
							{ __( 'Link Style', 'avanam' ) }
					</span>
					<SelectControl
						value={ this.state.value.style }
						options={ styleOptions }
						onChange={ ( val ) => {
							let value = this.state.value;
							value.style = val;
							this.updateValues( value );
						} }
					/>
				</div>
				<div ref={ this.anchorNodeRef } className="base-control-field base-color-control base-link-color-control">
					{
						this.props.control.params.label &&
						<span className="customize-control-title">
							{ this.props.control.params.label }
						</span>
					}
					{ Object.keys( colorsToUse ).map( ( item ) => {
						if ( ( this.state.value.style === 'standard' || this.state.value.style === 'color-underline' || this.state.value.style === 'no-underline' ) && item === 'highlight-alt2' ) {
							return;
						}
						return (
							<ColorControl
								key={ item }
								presetColors={ this.state.colorPalette }
								color={ ( undefined !== this.state.value[ item ] && this.state.value[ item ] ? this.state.value[ item ] : '' ) }
								usePalette={ ( undefined !== colorsToUse[ item ] && undefined !== colorsToUse[ item ].palette && '' !== colorsToUse[ item ].palette ? colorsToUse[ item ].palette : true ) }
								tooltip={ ( undefined !== colorsToUse[ item ] && undefined !== colorsToUse[ item ].tooltip ? colorsToUse[ item ].tooltip : '' ) }
								onChangeComplete={ ( color, isPalette ) => this.handleChangeComplete( color, isPalette, item ) }
								customizer={ this.props.customizer }
								controlRef={ this.anchorNodeRef }
							/>
						)
					} ) }
				</div>
			</Fragment>
		);
	}

	updateValues( value ) {
		this.setState( { value: value } );
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
		} );
	}
}

ColorLinkComponent.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default ColorLinkComponent;
