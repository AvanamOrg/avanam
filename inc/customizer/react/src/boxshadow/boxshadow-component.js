/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';

import ResponsiveControl from '../common/responsive.js';
import ColorControl from '../common/color.js';
import Icons from '../common/icons.js';

import { __ } from '@wordpress/i18n';

const { ButtonGroup, Dashicon, Toolbar, Tooltip, Button, ToggleControl } = wp.components;

/**
 * WordPress dependencies
 */
import { createRef, Component, Fragment } from '@wordpress/element';
class BoxshadowComponent extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		this.handleChangeComplete = this.handleChangeComplete.bind( this );
		this.handleResponsiveChangeComplete = this.handleResponsiveChangeComplete.bind( this );
		let value = this.props.control.setting.get();
		let defaultParams = {
			responsive:false,
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		let responsiveDefault = {
			'desktop': {
				'color': 'rgba(0,0,0,0.05)',
				'hOffset': 0,
				'vOffset': 15,
				'blur': 15,
				'spread': -10,
				'inset': false,
			}
		};
		let noneResponsiveDefault = {
			'color': 'rgba(0,0,0,0.05)',
			'hOffset': 0,
			'vOffset': 15,
			'blur': 15,
			'spread': -10,
			'inset': false,
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
				'color': '',
				'hOffset': '',
				'vOffset': '',
				'blur': '',
				'spread': '',
				'inset': '',
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
		const onResponsiveInputChange = ( event, setting  ) => {
			const newValue = Number( event.target.value );
			let value = this.state.value;
			if ( undefined === value[ this.state.currentDevice ] ) {
				value[ this.state.currentDevice ] = {
					'color': '',
					'hOffset': '',
					'vOffset': '',
					'blur': '',
					'spread': '',
					'inset': '',
				}
			}
			value[ this.state.currentDevice ][ setting ] = newValue;
			this.updateValues( value );
		}
		const onInputChange = ( event, setting ) => {
			const newValue = ( '' !== event.target.value ? Number( event.target.value ) : '' );
			let value = this.state.value;
			value[ setting ] = newValue;
			this.updateValues( value );
		}
		const onInsetChange = ( newValue ) => {
			let value = this.state.value;
			value.inset = newValue;
			this.updateValues( value );
		}
		const responsiveControlLabel = (
			<Fragment>
				{ this.state.currentDevice !== 'desktop' && (
					<Tooltip text={ __( 'Reset Device Values', 'avanam' ) }>
						<Button
							className="reset base-reset"
							disabled={ ( undefined === this.state.value[this.state.currentDevice] ) }
							onClick={ () => {
								let value = this.state.value;
								delete value[this.state.currentDevice];
								this.updateValues( value );
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
		const controlLabel = (
			<Fragment>
				<Tooltip text={ __( 'Reset Values', 'avanam' ) }>
					<Button
						className="reset base-reset"
						disabled={ ( this.defaultValue === this.state.value ) }
						onClick={ () => {
							let value = this.state.value;
							value = this.defaultValue
							this.updateValues( value );
						} }
					>
						<Dashicon icon='image-rotate' />
					</Button>
				</Tooltip>
				{ data.label &&
					data.label
				}
			</Fragment>
		);
		return (
			<div ref={ this.anchorNodeRef } className="base-control-field base-boxshadow-control base-border-control">
				{ this.controlParams.responsive && (
					<ResponsiveControl
						onChange={ ( currentDevice) => this.setState( { currentDevice } ) }
						controlLabel={ responsiveControlLabel }
					>
						<div className="bt-box-color-settings bt-box-shadow-subset">
							<p className="bt-box-shadow-title">{ __( 'Color' ) }</p>
							<ColorControl
								presetColors={ this.state.colorPalette }
								color={ ( undefined !== this.state.value.color && this.state.value.color ? this.state.value.color : '' ) }
								usePalette={ true }
								tooltip={ __( 'Border Color' ) }
								onChangeComplete={ ( color, isPalette ) => this.handleChangeComplete( color, isPalette ) }
								customizer={ this.props.customizer }
								controlRef={ this.anchorNodeRef }
							/>
						</div>
						<div className="bt-box-x-settings bt-box-shadow-subset">
							<p className="bt-box-shadow-title">{ __( 'X' ) }</p>
							<div className="components-base-control bt-boxshadow-number-input">
								<div className="components-base-control__field">
									<input
										value={ ( undefined !== this.state.value && undefined !== this.state.value.hOffset ? this.state.value.hOffset : '' ) }
										onChange={ event => onInputChange( event, 'hOffset' ) }
										min={ -200 }
										max={ 200 }
										step={ 1 }
										type="number"
										className="components-text-control__input"
									/>
								</div>
							</div>
						</div>
						<div className="bt-box-y-settings bt-box-shadow-subset">
							<p className="bt-box-shadow-title">{ __( 'Y' ) }</p>
							<div className="components-base-control bt-boxshadow-number-input">
								<div className="components-base-control__field">
									<input
										value={ ( undefined !== this.state.value && undefined !== this.state.value.vOffset ? this.state.value.vOffset : '' ) }
										onChange={ event => onInputChange( event, 'vOffset' ) }
										min={ -200 }
										max={ 200 }
										step={ 1 }
										type="number"
										className="components-text-control__input"
									/>
								</div>
							</div>
						</div>
						<div className="bt-box-x-settings bt-box-shadow-subset">
							<p className="bt-box-shadow-title">{ __( 'Blur' ) }</p>
							<div className="components-base-control bt-boxshadow-number-input">
								<div className="components-base-control__field">
									<input
										value={ ( undefined !== this.state.value && undefined !== this.state.value.blur ? this.state.value.blur : '' ) }
										onChange={ event => onInputChange( event, 'blur' ) }
										min={ 0 }
										max={ 200 }
										step={ 1 }
										type="number"
										className="components-text-control__input"
									/>
								</div>
							</div>
						</div>
						<div className="bt-box-y-settings bt-box-shadow-subset">
							<p className="bt-box-shadow-title">{ __( 'Spread' ) }</p>
							<div className="components-base-control bt-boxshadow-number-input">
								<div className="components-base-control__field">
									<input
										value={ ( undefined !== this.state.value && undefined !== this.state.value.spread ? this.state.value.spread : '' ) }
										onChange={ event => onInputChange( event, 'spread' ) }
										min={ -200 }
										max={ 200 }
										step={ 1 }
										type="number"
										className="components-text-control__input"
									/>
								</div>
							</div>
						</div>
						<div className="bt-box-inset-settings">
							<ToggleControl
								label={ __( 'Inset' ) }
								checked={ ( undefined !== this.state.value && undefined !== this.state.value.inset ? this.state.value.inset : false ) }
								onChange={ value => this.props.onInsetChange( value ) }
							/>
						</div>
					</ResponsiveControl>
				) }
				{ ! this.controlParams.responsive && (
					<Fragment>
						<div className="base-responsive-control-bar">
							<span className="customize-control-title">{ controlLabel }</span>
						</div>
						<div className="base-responsive-controls-content">
							<Fragment>
								<div className="bt-box-color-settings bt-box-shadow-subset">
									<p className="bt-box-shadow-title">{ __( 'Color' ) }</p>
									<ColorControl
										presetColors={ this.state.colorPalette }
										color={ ( undefined !== this.state.value.color && this.state.value.color ? this.state.value.color : '' ) }
										usePalette={ true }
										tooltip={ __( 'Border Color' ) }
										onChangeComplete={ ( color, isPalette ) => this.handleChangeComplete( color, isPalette ) }
										customizer={ this.props.customizer }
										controlRef={ this.anchorNodeRef }
									/>
								</div>
								<div className="bt-box-x-settings bt-box-shadow-subset">
									<p className="bt-box-shadow-title">{ __( 'X' ) }</p>
									<div className="components-base-control bt-boxshadow-number-input">
										<div className="components-base-control__field">
											<input
												value={ ( undefined !== this.state.value && undefined !== this.state.value.hOffset ? this.state.value.hOffset : '' ) }
												onChange={ event => onInputChange( event, 'hOffset' ) }
												min={ -200 }
												max={ 200 }
												step={ 1 }
												type="number"
												className="components-text-control__input"
											/>
										</div>
									</div>
								</div>
								<div className="bt-box-y-settings bt-box-shadow-subset">
									<p className="bt-box-shadow-title">{ __( 'Y' ) }</p>
									<div className="components-base-control bt-boxshadow-number-input">
										<div className="components-base-control__field">
											<input
												value={ ( undefined !== this.state.value && undefined !== this.state.value.vOffset ? this.state.value.vOffset : '' ) }
												onChange={ event => onInputChange( event, 'vOffset' ) }
												min={ -200 }
												max={ 200 }
												step={ 1 }
												type="number"
												className="components-text-control__input"
											/>
										</div>
									</div>
								</div>
								<div className="bt-box-x-settings bt-box-shadow-subset">
									<p className="bt-box-shadow-title">{ __( 'Blur' ) }</p>
									<div className="components-base-control bt-boxshadow-number-input">
										<div className="components-base-control__field">
											<input
												value={ ( undefined !== this.state.value && undefined !== this.state.value.blur ? this.state.value.blur : '' ) }
												onChange={ event => onInputChange( event, 'blur' ) }
												min={ 0 }
												max={ 200 }
												step={ 1 }
												type="number"
												className="components-text-control__input"
											/>
										</div>
									</div>
								</div>
								<div className="bt-box-y-settings bt-box-shadow-subset">
									<p className="bt-box-shadow-title">{ __( 'Spread' ) }</p>
									<div className="components-base-control bt-boxshadow-number-input">
										<div className="components-base-control__field">
											<input
												value={ ( undefined !== this.state.value && undefined !== this.state.value.spread ? this.state.value.spread : '' ) }
												onChange={ event => onInputChange( event, 'spread' ) }
												min={ -200 }
												max={ 200 }
												step={ 1 }
												type="number"
												className="components-text-control__input"
											/>
										</div>
									</div>
								</div>
							</Fragment>
						</div>
						<div className="base-responsive-controls-content">
							<div className="bt-box-inset-settings">
								<ToggleControl
									label={ __( 'Inset' ) }
									checked={ ( undefined !== this.state.value && undefined !== this.state.value.inset && this.state.value.inset ? true : false ) }
									onChange={ value => onInsetChange( value ) }
								/>
							</div>
						</div>
					</Fragment>
				) }
			</div>
		);
	}
	updateValues( value ) {
		this.setState( { value: value } );
		if ( this.controlParams.responsive ) {
			value.flag = !this.props.control.setting.get().flag;
		}
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
		} );
	}
}

BoxshadowComponent.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default BoxshadowComponent;
