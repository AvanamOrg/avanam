import PropTypes from 'prop-types';

import { __ } from '@wordpress/i18n';
const { Component } = wp.element;
const { TextControl } = wp.components;

class TextComponent extends Component {
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
		return (
			<div className="base-control-field base-text-control">
				<TextControl
					label={ this.props.control.params.label ? this.props.control.params.label : undefined }
					value={ this.state.value }
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

TextComponent.propTypes = {
	control: PropTypes.object.isRequired
};

export default TextComponent;
