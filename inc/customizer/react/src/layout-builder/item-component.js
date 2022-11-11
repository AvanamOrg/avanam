/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import ResponsiveControl from '../common/responsive.js';
import Icons from '../common/icons.js';

import { __ } from '@wordpress/i18n';

const { ButtonGroup, Dashicon, Tooltip, Button } = wp.components;

const { Component, Fragment } = wp.element;
class ItemComponent extends Component {
	constructor() {
		super( ...arguments );
		this.choices = ( baseCustomizerControlsData && baseCustomizerControlsData.choices && baseCustomizerControlsData.choices[ this.props.controlParams.group ] ? baseCustomizerControlsData.choices[ this.props.controlParams.group ] : [] );
	}
	render() {
		return (
			<div className="base-builder-item" data-id={ this.props.item } data-section={ undefined !== this.choices[ this.props.item ] && undefined !== this.choices[ this.props.item ].section ? this.choices[ this.props.item ].section : '' } key={ this.props.item }>
				<span
					className="base-builder-item-icon base-move-icon"
				>
					{ Icons['drag'] }
				</span>
				<span
					className="base-builder-item-text"
				>
					{ ( undefined !== this.choices[ this.props.item ] && undefined !== this.choices[ this.props.item ].name ? this.choices[ this.props.item ].name : '' ) }
				</span>
				<Button
					className="base-builder-item-focus-icon base-builder-item-icon"
					aria-label={ __( 'Setting settings for', 'avanam' ) + ' ' + ( undefined !== this.choices[ this.props.item ] && undefined !== this.choices[ this.props.item ].name ? this.choices[ this.props.item ].name : '' ) }
					onClick={ () => {
						this.props.focusItem( undefined !== this.choices[ this.props.item ] && undefined !== this.choices[ this.props.item ].section ? this.choices[ this.props.item ].section : '' );
					} }
				>
					<Dashicon icon="admin-generic"/>
				</Button>
				{ baseCustomizerControlsData.blockWidgets && this.props.item.includes('widget') && 'toggle-widget' !== this.props.item && (
					<Button
						className="base-builder-item-focus-icon base-builder-item-icon"
						aria-label={ __( 'Setting settings for', 'avanam' ) + ' ' + ( undefined !== this.choices[ this.props.item ] && undefined !== this.choices[ this.props.item ].name ? this.choices[ this.props.item ].name : '' ) }
						onClick={ () => {
							this.props.focusItem( undefined !== this.choices[ this.props.item ] && undefined !== this.choices[ this.props.item ].section ? 'base_customizer_' + this.choices[ this.props.item ].section : '' );
						} }
					>
						<Dashicon icon="admin-settings"/>
					</Button>
				) }
				{ baseCustomizerControlsData.blockWidgets && 'toggle-widget' === this.props.item && (
					<Button
						className="base-builder-item-focus-icon base-builder-item-icon"
						aria-label={ __( 'Setting settings for', 'avanam' ) + ' ' + ( undefined !== this.choices[ this.props.item ] && undefined !== this.choices[ this.props.item ].name ? this.choices[ this.props.item ].name : '' ) }
						onClick={ () => {
							this.props.focusItem( 'base_customizer_sidebar-widgets-header2' );
						} }
					>
						<Dashicon icon="admin-settings"/>
					</Button>
				) }
				<Button
					className="base-builder-item-icon"
					aria-label={ __( 'Remove', 'avanam' ) + ' ' + ( undefined !== this.choices[ this.props.item ] && undefined !== this.choices[ this.props.item ].name ? this.choices[ this.props.item ].name : '' ) }
					onClick={ () => {
						this.props.removeItem( this.props.item );
					} }
				>
					<Dashicon icon="no-alt"/>
				</Button>
			</div>
		);
	}
}
export default ItemComponent;
