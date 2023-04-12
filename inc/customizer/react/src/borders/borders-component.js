/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';

import ResponsiveControl from '../common/responsive.js';
import Icons from '../common/icons.js';
import SingleBorderComponent from './border-component';

import { __ } from '@wordpress/i18n';

const { ButtonGroup, Dashicon, Toolbar, Tooltip, Button } = wp.components;

/**
 * WordPress dependencies
 */
import { Component, Fragment } from '@wordpress/element';
class BordersComponent extends Component {
	constructor() {
		super( ...arguments );
		//this.updateValues = this.updateValues.bind( this );
		//this.resetValues = this.resetValues.bind( this );
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
	}
	render() {
		const data = this.props.control.params;
		const responsiveControlLabel = (
			<Fragment>
				{/* <Tooltip text={ __( 'Reset Values', 'avanam' ) }>
					<Button
						className="reset base-reset"
						disabled={ isDisabled }
						onClick={ () => {
							this.resetValues();
						} }
					>
						<Dashicon icon='image-rotate' />
					</Button>
				</Tooltip> */}
				{ data.label &&
					data.label
				}
			</Fragment>
		);
		const controlLabel = (
			<Fragment>
				{/* <Tooltip text={ __( 'Reset Values', 'avanam' ) }>
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
				</Tooltip> */}
				{ data.label &&
					data.label
				}
			</Fragment>
		);
		return (
			<div className="base-control-field base-border-control">
				{ this.controlParams.responsive && (
					<ResponsiveControl
						onChange={ ( currentDevice ) => this.setState( { currentDevice } ) }
						controlLabel={ responsiveControlLabel }
					>
						{ Object.keys( this.props.control.params.settings ).map( ( item ) => {
							return (
								<SingleBorderComponent
									currentDevice={ this.state.currentDevice }
									item={ item }
									name={ this.props.control.params.settings[item] }
									customizer={ this.props.customizer }
									control={ this.props.control }
								/>
							);
						} ) }
					</ResponsiveControl>
				) }
				{ ! this.controlParams.responsive && (
					<Fragment>
						<div className="base-responsive-control-bar">
							<span className="customize-control-title">{ controlLabel }</span>
						</div>
						<div className="base-responsive-controls-content">
							<SingleBorderComponent
								currentDevice={ currentDevice }
								item={ 'border_top' }
								customizer={ this.props.customizer }
							/>
						</div>
					</Fragment>
				) }
			</div>
		);
	}
	// resetValues() {
	// 	Object.keys( this.props.control.params.settings ).map( ( item ) => {
	// 		this.props.control.settings[item].set( {
	// 			responsiveDefault
	// 		} );
	// 	} );
	// }
	// updateValues( value ) {
	// 	this.setState( { value: value } );
	// 	if ( this.controlParams.responsive ) {
	// 		value.flag = !this.props.control.setting.get().flag;
	// 	}
	// 	this.props.control.setting.set( {
	// 		...this.props.control.setting.get(),
	// 		...value,
	// 	} );
	// }
}

BordersComponent.propTypes = {
	control: PropTypes.object.isRequired,
	customizer: PropTypes.object.isRequired
};

export default BordersComponent;
