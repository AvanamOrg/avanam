/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import Icons from './icons.js';
import capitalizeFirstLetter from './capitalize-first.js';

import { __ } from '@wordpress/i18n';

import { ButtonGroup, Dashicon, Tooltip, Button } from '@wordpress/components';

const { Component, Fragment } = wp.element;
class RadioIconComponent extends Component {
	constructor() {
		super( ...arguments );
		let baseDefault = 'default';
		this.defaultValue = this.props.default ? this.props.default : baseDefault;
		this.state = {
			value: this.props.value,
		};
	}
	render() {
		const controlLabel = (
			<Fragment>
				<Tooltip text={ __( 'Reset Values', 'avanam' ) }>
					<Button
						className="reset base-reset"
						disabled={ ( this.state.value === this.defaultValue ) }
						onClick={ () => {
							let value = this.defaultValue;
							this.setState( { value: this.defaultValue } );
							this.updateValues( value );
						} }
					>
						<Dashicon icon='image-rotate' />
					</Button>
				</Tooltip>
				{ this.props.label &&
					this.props.label
				}
			</Fragment>
		);
		return (
			<div className={ `base-control-field base-radio-icon-control${ ( this.props.customClass ? ' ' + this.props.customClass : '' ) }` }>
				{ this.props.label && (
					<div className="base-title-control-bar">
						<span className="customize-control-title">{ this.props.label }</span>
					</div>
				) }
				<ButtonGroup className="base-radio-container-control">
					{ Object.keys( this.props.options ).map( ( item ) => {
						return (
							<Fragment>
								{ this.props.options[ item ].tooltip && (
									<Tooltip text={ this.props.options[ item ].tooltip }>
										<Button
											isTertiary
											className={ ( item === this.state.value ?
													'active-radio ' :
													'' ) + 'radio-item-' + item + ( this.props.options[ item ].icon && this.props.options[ item ].name ? ' btn-flex-col' : '' ) }
											onClick={ () => {
												let value = this.state.value;
												value = item;
												this.setState( { value: item });
												this.props.onChange( value );
											} }
										>
											{ this.props.options[ item ].icon && (
												<span className="base-radio-icon">
													{ Icons[ this.props.options[ item ].icon ] }
												</span>
											) }
											{ this.props.options[ item ].name && (
													this.props.options[ item ].name
											) }
										</Button>
									</Tooltip>
								) }
								{ ! this.props.options[ item ].tooltip && (
									<Button
										isTertiary
										className={ ( item === this.state.value ?
												'active-radio ' :
												'' ) + 'radio-item-' + item + ( this.props.options[ item ].icon && this.props.options[ item ].name ? ' btn-flex-col' : '' ) }
										onClick={ () => {
											let value = this.state.value;
											value = item;
											this.setState( { value: item });
											this.props.onChange( value );
										} }
									>
										{ this.props.options[ item ].icon && (
											<span className="base-radio-icon">
												{ Icons[ this.props.options[ item ].icon ] }
											</span>
										) }
										{ this.props.options[ item ].name && (
												this.props.options[ item ].name
										) }
									</Button>
								) }
							</Fragment>
						);
					} )}
				</ButtonGroup>
			</div>
		);
	}
}

RadioIconComponent.propTypes = {
	control: PropTypes.object.isRequired
};

export default RadioIconComponent;
