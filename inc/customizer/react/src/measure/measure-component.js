/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';

import ResponsiveControl from '../common/responsive.js';
import Icons from '../common/icons.js';

import { __ } from '@wordpress/i18n';

const { RangeControl, Dashicon, Tooltip, Button, Toolbar, TextControl, ToolbarGroup } = wp.components;

const { Component, Fragment } = wp.element;
class MeasureComponent extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		this.getUnitButtons = this.getUnitButtons.bind( this );
		this.createLevelControlToolbar = this.createLevelControlToolbar.bind( this );
		this.createResponsiveLevelControlToolbar = this.createResponsiveLevelControlToolbar.bind( this );
		this.getResponsiveUnitButtons = this.getResponsiveUnitButtons.bind( this );
		let value = this.props.control.setting.get();
		let baseDefault = {
			'unit': {
				'desktop': 'px'
			},
			'size': {
				'desktop': [ 0, 0, 0, 0 ]
			},
			'locked': {
				'desktop': true
			}
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
			min: {
				px: '0',
				em: '0',
				rem: '0',
			},
			max: {
				px: '120',
				em: '12',
				rem: '12',
			},
			step: {
				px: '1',
				em: '0.01',
				rem: '0.01',
			},
			units: ['px', 'em', 'rem' ],
			responsive: true,
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		this.state = {
			currentDevice: 'desktop',
			size: value.size,
			unit: value.unit,
			locked: value.locked
		};
	}
	render() {
		const responsiveControlLabel = (
			<Fragment>
				{ this.controlParams.responsive && (
					<Fragment>
						<Tooltip text={ __( 'Reset Device Values', 'avanam' ) }>
							<Button
								className="reset base-reset"
								disabled={ ( this.state.size[this.state.currentDevice] === this.defaultValue.size[this.state.currentDevice] ) && ( this.state.unit[this.state.currentDevice] === this.defaultValue.unit[this.state.currentDevice] ) }
								onClick={ () => {
									let value = this.state.size;
									value[this.state.currentDevice] = this.defaultValue.size[this.state.currentDevice];
									let svalue = this.state.unit;
									svalue[this.state.currentDevice] = this.defaultValue.unit[this.state.currentDevice];
									let lvalue = this.state.unit;
									lvalue[this.state.currentDevice] = this.defaultValue.locked[this.state.currentDevice];
									this.setState( { size: value, unit: svalue, locked: lvalue } );
									this.updateValues( { size: value, unit: svalue, locked: lvalue } );
								} }
							>
								<Dashicon icon='image-rotate' />
							</Button>
						</Tooltip>
						{ this.props.control.params.label &&
							this.props.control.params.label
						}
					</Fragment>
				) }
			</Fragment>
		);
		const controlLabel = (
			<Fragment>
				<Tooltip text={ __( 'Reset Values', 'avanam' ) }>
					<Button
						className="reset base-reset"
						disabled={ ( this.state.size === this.defaultValue.size ) && ( this.state.unit === this.defaultValue.unit ) }
						onClick={ () => {
							let value = this.state.size;
							value = this.defaultValue.size;
							let svalue = this.state.unit;
							svalue = this.defaultValue.unit;
							let lvalue = this.state.locked;
							lvalue = this.defaultValue.locked;
							this.setState( { size: value, unit: svalue, locked: lvalue } );
							this.updateValues( { size: value, unit: svalue, locked: lvalue } );
						} }
					>
						<Dashicon icon='image-rotate' />
					</Button>
				</Tooltip>
				{ this.props.control.params.label &&
					this.props.control.params.label
				}
			</Fragment>
		);
		return (
			<div className="base-control-field base-range-control">
				{ this.controlParams.responsive && (
					<ResponsiveControl
						onChange={ ( currentDevice) => this.setState( { currentDevice } ) }
						controlLabel={ responsiveControlLabel }
					>
						{ this.state.locked[this.state.currentDevice] && (
							<RangeControl
								initialPosition={ ( this.state.size[this.state.currentDevice] && this.state.size[this.state.currentDevice][0] ? this.state.size[this.state.currentDevice][0] : '' ) }
								value={ ( this.state.size[this.state.currentDevice] && this.state.size[this.state.currentDevice][0] ? this.state.size[this.state.currentDevice][0] : '' ) }
								onChange={ (val) => {
									let value = this.state.size;
									value[ this.state.currentDevice ] = [ val, val, val, val ];
									this.setState( { size: value } );
									this.updateValues( { size: value } );
								} }
								min={this.controlParams.min[this.state.unit[this.state.currentDevice]]}
								max={this.controlParams.max[this.state.unit[this.state.currentDevice]]}
								step={this.controlParams.step[this.state.unit[this.state.currentDevice]]}
							/>
						) }
						{ ! this.state.locked[this.state.currentDevice] && (
							<Fragment>
								<TextControl
									label={ __( 'Top', 'avanam' ) }
									hideLabelFromVision={ true }
									type="number"
									className="measure-inputs"
									value={( this.state.size[this.state.currentDevice] && this.state.size[this.state.currentDevice][0] ? this.state.size[this.state.currentDevice][0] : '' )}
									onChange={ (val) => {
										let value = this.state.size;
										if ( undefined === value[ this.state.currentDevice ] ) {
											value[ this.state.currentDevice ] = [ 0, 0, 0, 0 ];
										}
										value[ this.state.currentDevice ][0] = val;
										this.setState( { size: value } );
										this.updateValues( { size: value } );
									} }
									min={this.controlParams.min[this.state.unit[this.state.currentDevice]]}
									max={this.controlParams.max[this.state.unit[this.state.currentDevice]]}
									step={this.controlParams.step[this.state.unit[this.state.currentDevice]]}
								/>
								<TextControl
									label={ __( 'Right', 'avanam' ) }
									hideLabelFromVision={ true }
									type="number"
									className="measure-inputs"
									value={( this.state.size[this.state.currentDevice] && this.state.size[this.state.currentDevice][1] ? this.state.size[this.state.currentDevice][1] : '' )}
									onChange={ (val) => {
										let value = this.state.size;
										if ( undefined === value[ this.state.currentDevice ] ) {
											value[ this.state.currentDevice ] = [ 0, 0, 0, 0 ];
										}
										value[ this.state.currentDevice ][1] = val;
										this.setState( { size: value } );
										this.updateValues( { size: value } );
									} }
									min={this.controlParams.min[this.state.unit[this.state.currentDevice]]}
									max={this.controlParams.max[this.state.unit[this.state.currentDevice]]}
									step={this.controlParams.step[this.state.unit[this.state.currentDevice]]}
								/>
								<TextControl
									label={ __( 'Bottom', 'avanam' ) }
									hideLabelFromVision={ true }
									type="number"
									className="measure-inputs"
									value={( this.state.size[this.state.currentDevice] && this.state.size[this.state.currentDevice][2] ? this.state.size[this.state.currentDevice][2] : '' )}
									onChange={ (val) => {
										let value = this.state.size;
										if ( undefined === value[ this.state.currentDevice ] ) {
											value[ this.state.currentDevice ] = [ 0, 0, 0, 0 ];
										}
										value[ this.state.currentDevice ][2] = val;
										this.setState( { size: value } );
										this.updateValues( { size: value } );
									} }
									min={this.controlParams.min[this.state.unit[this.state.currentDevice]]}
									max={this.controlParams.max[this.state.unit[this.state.currentDevice]]}
									step={this.controlParams.step[this.state.unit[this.state.currentDevice]]}
								/>
								<TextControl
									label={ __( 'Left', 'avanam' ) }
									hideLabelFromVision={ true }
									type="number"
									className="measure-inputs"
									value={( this.state.size[this.state.currentDevice] && this.state.size[this.state.currentDevice][3] ? this.state.size[this.state.currentDevice][3] : '' )}
									onChange={ (val) => {
										let value = this.state.size;
										if ( undefined === value[ this.state.currentDevice ] ) {
											value[ this.state.currentDevice ] = [ 0, 0, 0, 0 ];
										}
										value[ this.state.currentDevice ][3] = val;
										this.setState( { size: value } );
										this.updateValues( { size: value } );
									} }
									min={this.controlParams.min[this.state.unit[this.state.currentDevice]]}
									max={this.controlParams.max[this.state.unit[this.state.currentDevice]]}
									step={this.controlParams.step[this.state.unit[this.state.currentDevice]]}
								/>
							</Fragment>
						) }
						<div className="base-units base-locked">
							{ this.getResponsiveLockedButtons() }
						</div>
						{ this.controlParams.units && (
							<div className="base-units">
								{ this.getResponsiveUnitButtons() }
							</div>
						) }
					</ResponsiveControl>
				) }
				{ ! this.controlParams.responsive && (
					<Fragment>
						<div className="base-responsive-control-bar">
							<span className="customize-control-title">{ controlLabel }</span>
						</div>
						<div className="base-responsive-controls-content">
							{ this.state.locked && (
								<RangeControl
									initialPosition={ this.state.size[0] }
									value={this.state.size[0]}
									onChange={ (val) => {
										let value = this.state.size;
										value = [ val, val, val, val ];
										this.setState( { size: value } );
										this.updateValues( { size: value } );
									} }
									min={this.controlParams.min[this.state.unit]}
									max={this.controlParams.max[this.state.unit]}
									step={this.controlParams.step[this.state.unit]}
								/>
							) }
							{ ! this.state.locked && (
								<Fragment>
									<div className="measure-input-wrap">
										<input
											value={this.state.size[0]}
											onChange={ ( event ) => {
												const val = ( '' !== event.target.value ? Number( event.target.value ) : '' );
												let value = this.state.size;
												value[0] = val;
												if ( val !== '' ) {
													if ( '' === value[1] ) {
														value[1] = 0;
													}
													if ( '' === value[2] ) {
														value[2] = 0;
													}
													if ( '' === value[3] ) {
														value[3] = 0;
													}
												}
												this.setState( { size: value } );
												this.updateValues( { size: value } );
											} }
											min={this.controlParams.min[this.state.unit]}
											max={this.controlParams.max[this.state.unit]}
											step={this.controlParams.step[this.state.unit]}
											type="number"
											className="measure-inputs"
										/>
										<small>{ __( 'Top', 'avanam' ) }</small>
									</div>
									<div className="measure-input-wrap">
										<input
											value={this.state.size[1]}
											onChange={ ( event ) => {
												const val = ( '' !== event.target.value ? Number( event.target.value ) : '' );
												let value = this.state.size;
												value[1] = val;
												if ( val !== '' ) {
													if ( '' === value[0] ) {
														value[0] = 0;
													}
													if ( '' === value[2] ) {
														value[2] = 0;
													}
													if ( '' === value[3] ) {
														value[3] = 0;
													}
												}
												this.setState( { size: value } );
												this.updateValues( { size: value } );
											} }
											min={this.controlParams.min[this.state.unit]}
											max={this.controlParams.max[this.state.unit]}
											step={this.controlParams.step[this.state.unit]}
											type="number"
											className="measure-inputs"
										/>
										<small>{ __( 'Right', 'avanam' ) }</small>
									</div>
									<div className="measure-input-wrap">
										<input
											value={this.state.size[2]}
											onChange={ ( event ) => {
												const val = ( '' !== event.target.value ? Number( event.target.value ) : '' );
												let value = this.state.size;
												value[2] = val;
												if ( val !== '' ) {
													if ( '' === value[0] ) {
														value[0] = 0;
													}
													if ( '' === value[1] ) {
														value[1] = 0;
													}
													if ( '' === value[3] ) {
														value[3] = 0;
													}
												}
												this.setState( { size: value } );
												this.updateValues( { size: value } );
											} }
											min={this.controlParams.min[this.state.unit]}
											max={this.controlParams.max[this.state.unit]}
											step={this.controlParams.step[this.state.unit]}
											type="number"
											className="measure-inputs"
										/>
										<small>{ __( 'Bottom', 'avanam' ) }</small>
									</div>
									<div className="measure-input-wrap">
										<input
											value={this.state.size[3]}
											onChange={ ( event ) => {
												const val = ( '' !== event.target.value ? Number( event.target.value ) : '' );
												let value = this.state.size;
												value[3] = val;
												if ( val !== '' ) {
													if ( '' === value[0] ) {
														value[0] = 0;
													}
													if ( '' === value[1] ) {
														value[1] = 0;
													}
													if ( '' === value[2] ) {
														value[2] = 0;
													}
												}
												this.setState( { size: value } );
												this.updateValues( { size: value } );
											} }
											min={this.controlParams.min[this.state.unit]}
											max={this.controlParams.max[this.state.unit]}
											step={this.controlParams.step[this.state.unit]}
											type="number"
											className="measure-inputs"
										/>
										<small>{ __( 'Left', 'avanam' ) }</small>
									</div>
								</Fragment>
							) }
							<div className="base-units base-locked">
								{ this.getLockedButtons() }
							</div>
							{ this.controlParams.units && (
								<div className="base-units">
									{ this.getUnitButtons() }
								</div>
							) }
						</div>
					</Fragment>
				) }
			</div>
		);
	}
	getLockedButtons() {
		const { locked } = this.state;
		if ( locked ) {
			return ( <Button
					className="is-single"
					onClick={ () => {
						let value = this.state.locked;
						value = false;
						this.setState( { locked: value } );
						this.updateValues( { locked: value } );
					} }
					isSmall
				>{ Icons['locked'] }</Button> );
		}
		return ( <Button
					className="is-single"
					isSmall
					onClick={ () => {
						let value = this.state.locked;
						value = true;
						this.setState( { locked: value } );
						this.updateValues( { locked: value } );
					} }
				>{ Icons['unlocked'] }</Button> );
	}
	getUnitButtons() {
		let self = this;
		const { units } = this.controlParams;
		if ( units.length === 1 ) {
			return ( <Button
					className="is-active is-single"
					isSmall
					disabled
			>{ ( '%' === self.state.unit ? Icons.percent : Icons[ self.state.unit ] ) }</Button> );
		}
		return <ToolbarGroup
					isCollapsed={ true }
					icon={ ( '%' === self.state.unit ? Icons.percent : Icons[ self.state.unit ] ) }
					label={ __( 'Unit', 'avanam' ) }
					controls={ units.map( (unit) => this.createLevelControlToolbar( unit ) ) }
				/> 
	}
	createLevelControlToolbar( unit ) {
		return [ {
			icon: ( unit === '%' ? Icons.percent : Icons[ unit ] ),
			isActive: this.state.unit === unit,
			onClick: () => {
				let value = this.state.unit;
				value = unit;
				this.setState( { unit: value } );
				this.updateValues( { unit: value } );
			},
		} ];
	};
	createResponsiveLevelControlToolbar( unit ) {
		return [ {
			icon: ( unit === '%' ? Icons.percent : Icons[ unit ] ),
			isActive: this.state.unit[this.state.currentDevice] === unit,
			onClick: () => {
				let value = this.state.unit;
				value[ this.state.currentDevice ] = unit;
				this.setState( { unit: value } );
				this.updateValues( { unit: value } );
			},
		} ];
	};
	getResponsiveLockedButtons() {
		let self = this;
		const { locked } = this.state;
		if ( locked[ self.state.currentDevice ] ) {
			return ( <Button
					className="is-single"
					isSmall
					onClick={ () => {
						let value = this.state.locked;
						value[ this.state.currentDevice ] = false;
						this.setState( { locked: value } );
						this.updateValues( { locked: value } );
					} }
				>{ Icons['locked'] }</Button> );
		}
		return ( <Button
					className="is-single"
					isSmall
					onClick={ () => {
						let value = this.state.locked;
						value[ this.state.currentDevice ] = true;
						this.setState( { locked: value } );
						this.updateValues( { locked: value } );
					} }
				>{ Icons['unlocked'] }</Button> );
	}
	getResponsiveUnitButtons() {
		let self = this;
		const { units } = this.controlParams;
		if ( units.length === 1 ) {
			return ( <Button
					className="is-active is-single"
					isSmall
					disabled
			>{ ( '%' === self.state.unit[ self.state.currentDevice ] ? Icons.percent : Icons[ self.state.unit[ self.state.currentDevice ] ] ) }</Button> );
		}
		return <ToolbarGroup
					isCollapsed={ true }
					icon={ ( '%' === self.state.unit[ self.state.currentDevice ] ? Icons.percent : Icons[ self.state.unit[ self.state.currentDevice ] ] ) }
					label={ __( 'Unit', 'avanam' ) }
					controls={ units.map( (unit) => this.createResponsiveLevelControlToolbar( unit ) ) }
				/> 
	}
	updateValues( value ) {
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
			flag: !this.props.control.setting.get().flag
		} );
	}
}

MeasureComponent.propTypes = {
	control: PropTypes.object.isRequired
};

export default MeasureComponent;
