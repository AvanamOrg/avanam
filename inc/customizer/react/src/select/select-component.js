/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';

import { __ } from '@wordpress/i18n';

const { SelectControl, Dashicon, Tooltip, Button } = wp.components;

const { Component, Fragment } = wp.element;
class SelectComponent extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		let value = this.props.control.setting.get();
		let defaultParams = {
			options: {
				standard: {
					name: __( 'Standard', 'avanam' ),
				},
				fullwidth: {
					name: __( 'Fullwidth', 'avanam' ),
				},
				contained: {
					name: __( 'Contained', 'avanam' ),
				},
			},
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		let baseDefault = 'standard';
		this.defaultValue = this.props.control.params.default ? this.props.control.params.default : baseDefault;
		value = value ? value : this.defaultValue;
		this.state = {
			value: value,
		};
	}
	render() {
		const controlLabel = (
			<Fragment>
				<Tooltip text={ __( 'Reset Value', 'avanam' ) }>
					<Button
						className="reset base-reset"
						disabled={ ( this.state.value === this.defaultValue ) }
						onClick={ () => {
							let value = this.defaultValue;
							this.updateValues( value );
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
		const selectOptions = Object.keys( this.controlParams.options ).map( ( item ) => {
			return ( { label: this.controlParams.options[ item ].name, value: item } );
		} );
		return (
			<div className="base-control-field base-select-control">
				<div className="base-responsive-control-bar">
					<span className="customize-control-title">{ controlLabel }</span>
				</div>
				<SelectControl
					value={ this.state.value }
					options={ selectOptions }
					onChange={ ( val ) => {
						this.updateValues( val );
					} }
				/>
			</div>
		);
	}

	updateValues( value ) {
		this.setState( { value: value } );
		this.props.control.setting.set( value );
	}
}

SelectComponent.propTypes = {
	control: PropTypes.object.isRequired
};

export default SelectComponent;
