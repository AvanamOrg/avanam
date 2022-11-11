/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';

import ResponsiveControl from '../common/responsive.js';
import BaseRange from '../common/range.js';
import Icons from '../common/icons.js';

import { __ } from '@wordpress/i18n';

const { RangeControl, Dashicon, Tooltip, Button, ToolbarGroup } = wp.components;

const { Component, Fragment } = wp.element;
class RangeComponent extends Component {
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
				'mobile': 'px',
				'tablet': 'px',
				'desktop': 'px'
			},
			'size': {
				'mobile': 70,
				'tablet': 70,
				'desktop': 140
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
				vh: '0'
			},
			max: {
				px: '300',
				em: '12',
				rem: '12',
				vh: '40'
			},
			step: {
				px: '1',
				em: '0.01',
				rem: '0.01',
				vh: '1'
			},
			units: ['px', 'em', 'rem', 'vh'],
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
									let unit = this.state.unit;
									if ( typeof value !== 'object' ) {
										value =  {
											'mobile': '',
											'tablet': '',
											'desktop': '',
										};
										this.setState( { size: value } );
										if ( typeof unit !== 'object' ) {
											unit =  {
												'mobile': '',
												'tablet': '',
												'desktop': '',
											};
											this.setState( { unit: unit } );
										}
									}
									value[this.state.currentDevice] = this.defaultValue.size[this.state.currentDevice];
									unit[this.state.currentDevice] = this.defaultValue.unit[this.state.currentDevice];
									this.setState( { size: value, unit: unit } );
									this.updateValues( { size: value, unit: unit } );
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
							this.setState( { size: value, unit: svalue } );
							this.updateValues( { size: value, unit: svalue } );
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
						<BaseRange
								initialPosition={ ( ! this.state.size[this.state.currentDevice] ? this.defaultValue.size[this.state.currentDevice] : undefined ) }
								value={ ( undefined !== this.state.size[this.state.currentDevice] ? this.state.size[this.state.currentDevice] : undefined ) }
								onChange={ (val) => {
									let value = this.state.size;
									if ( typeof value !== 'object' ) {
										value =  {
											'mobile': '',
											'tablet': '',
											'desktop': '',
										};
										this.setState( { size: value } );
										let unit = this.state.unit;
										if ( typeof unit !== 'object' ) {
											unit =  {
												'mobile': '',
												'tablet': '',
												'desktop': '',
											};
											this.setState( { unit: unit } );
										}
									}
									value[ this.state.currentDevice ] = val;
									this.setState( { size: value } );
									this.updateValues( { size: value } );
								} }
								min={this.controlParams.min[this.state.unit[this.state.currentDevice]]}
								max={this.controlParams.max[this.state.unit[this.state.currentDevice]]}
								step={this.controlParams.step[this.state.unit[this.state.currentDevice]]}
						/>
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
							<BaseRange
								initialPosition={ ( ! this.state.size ? this.defaultValue.size : undefined ) }
								value={this.state.size}
								onChange={ (val) => {
									let value = this.state.size;
									value = val;
									this.setState( { size: value } );
									this.updateValues( { size: value } );
								} }
								min={this.controlParams.min[this.state.unit]}
								max={this.controlParams.max[this.state.unit]}
								step={this.controlParams.step[this.state.unit]}
							/>
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
				if ( typeof value !== 'object' ) {
					value =  {
						'mobile': '',
						'tablet': '',
						'desktop': '',
					};
					this.setState( { unit: value } );
					let size = this.state.size;
					if ( typeof size !== 'object' ) {
						size =  {
							'mobile': '',
							'tablet': '',
							'desktop': '',
						};
						this.setState( { size: size } );
					}
				}
				value[ this.state.currentDevice ] = unit;
				this.setState( { unit: value } );
				this.updateValues( { unit: value } );
			},
		} ];
	};
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
		if ( this.controlParams.responsive ) {
			value.flag = !this.props.control.setting.get().flag;
		}
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
		} );
	}
}

RangeComponent.propTypes = {
	control: PropTypes.object.isRequired
};

export default RangeComponent;
