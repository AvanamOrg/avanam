import PropTypes from 'prop-types';

import { __ } from '@wordpress/i18n';
const { Component } = wp.element;
const { ToggleControl } = wp.components;

class SwitchComponent extends Component {
	constructor(props) {
		super( props );
		let value = props.control.setting.get();
		this.state = {
			value
		};
		this.defaultValue = props.control.params.default || '';
		this.updateValues = this.updateValues.bind( this );
	}

	render() {
		//console.log( this.props.control.params );
		return (
				<div className="base-control-field base-switch-control">
					<ToggleControl
						label={ this.props.control.params.label ? this.props.control.params.label : undefined }
						checked={ this.state.value }
						help={ this.props.control.params.input_attrs && this.props.control.params.input_attrs.help ? this.props.control.params.input_attrs.help : undefined }
						onChange={ (value) => {
							this.updateValues( value );
						} }
					/>
				</div>
		);
	}

	updateValues(value) {
		this.setState( { value: value } );
		this.props.control.setting.set( value );
	}
}

SwitchComponent.propTypes = {
	control: PropTypes.object.isRequired
};

export default SwitchComponent;
