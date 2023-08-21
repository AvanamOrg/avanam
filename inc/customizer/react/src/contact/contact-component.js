/* jshint esversion: 6 */
import PropTypes from 'prop-types';
import classnames from 'classnames';
import ResponsiveControl from '../common/responsive.js';
import Icons from '../common/icons.js';
import { ReactSortable } from "react-sortablejs";
import uniqueId from 'lodash/uniqueId';

import ItemComponent from './item-component';

import { __ } from '@wordpress/i18n';

const { ButtonGroup, Dashicon, Tooltip, Popover, Button, SelectControl } = wp.components;

const { Component, Fragment } = wp.element;
class ContactComponent extends Component {
	constructor() {
		super( ...arguments );
		this.updateValues = this.updateValues.bind( this );
		this.onDragEnd = this.onDragEnd.bind( this );
		this.onDragStart = this.onDragStart.bind( this );
		this.onDragStop = this.onDragStop.bind( this );
		this.removeItem = this.removeItem.bind( this );
		this.saveArrayUpdate = this.saveArrayUpdate.bind( this );
		this.toggleEnableItem = this.toggleEnableItem.bind( this );
		this.onChangeIcon = this.onChangeIcon.bind( this );
		this.onChangeTitle = this.onChangeTitle.bind( this );
		this.onChangeLabel = this.onChangeLabel.bind( this );
		this.onChangeLink = this.onChangeLink.bind( this );
		this.onChangeURL = this.onChangeURL.bind( this );
		this.onChangeAttachment = this.onChangeAttachment.bind( this );
		this.onChangeWidth = this.onChangeWidth.bind( this );
		this.onChangeSource = this.onChangeSource.bind( this );
		this.addItem = this.addItem.bind( this );
		let value = this.props.control.setting.get();
		let baseDefault = {
			'items': [
				{
					'id': 'phone',
					'enabled': true,
					'source': 'icon',
					'url': '',
					'imageid': '',
					'width': 24,
					'link': '',
					'icon': 'mobile',
					'title': 'Phone',
					'label': '444-546-8765',
				},
				{
					'id': 'hours',
					'enabled': true,
					'source': 'icon',
					'url': '',
					'imageid': '',
					'width': 24,
					'link': '',
					'icon': 'hours',
					'title': 'Hours',
					'label': 'Mon - Fri: 8AM - 5PM',
				}
			],
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
			'group' : 'contact_item_group',
			'options': [
				{ value: 'phone', label: __( 'Phone', 'avanam' ), title: __( 'Phone', 'avanam' ), content: __( '444-546-8765', 'avanam' ) },
				{ value: 'hours', label: __( 'Hours', 'avanam' ), title: __( 'Hours', 'avanam' ), content: __( 'Mon - Fri: 8AM - 5PM', 'avanam' ) },
				{ value: 'email', label: __( 'Email', 'avanam' ), title: __( 'Email', 'avanam' ), content: __( 'example@example.com', 'avanam' ) },
				{ value: 'location', label: __( 'Address', 'avanam' ), title: __( 'Address', 'avanam' ), content: __( '4560 example street', 'avanam' ) },
			],
		};
		this.controlParams = this.props.control.params.input_attrs ? {
			...defaultParams,
			...this.props.control.params.input_attrs,
		} : defaultParams;
		let availibleContactOptions = [];
		this.controlParams.options.map( ( option ) => {
			if ( ! value.items.some( obj => obj.id === option.value ) ) {
				availibleContactOptions.push( option );
			}
		} );
		this.state = {
			value: value,
			isVisible: false,
			control: ( undefined !== availibleContactOptions[0] && undefined !== availibleContactOptions[0].value ? availibleContactOptions[0].value : '' ),
		};
	}
	onDragStart() {
		var dropzones = document.querySelectorAll( '.base-builder-area' );
		var i;
		for (i = 0; i < dropzones.length; ++i) {
			dropzones[i].classList.add( 'base-dragging-dropzones' );
		}
	}
	onDragStop() {
		var dropzones = document.querySelectorAll( '.base-builder-area' );
		var i;
		for (i = 0; i < dropzones.length; ++i) {
			dropzones[i].classList.remove( 'base-dragging-dropzones' );
		}
	}
	saveArrayUpdate( value, index ) {
		let updateState = this.state.value;
		let items = updateState.items;

		const newItems = items.map( ( item, thisIndex ) => {
			if ( index === thisIndex ) {
				item = { ...item, ...value };
			}

			return item;
		} );
		updateState.items = newItems;
		this.setState( { value: updateState } );
		this.updateValues( updateState );
	}
	toggleEnableItem( value, itemIndex ) {
		this.saveArrayUpdate( { enabled: value }, itemIndex );
	}
	onChangeTitle( value, itemIndex ) {
		this.saveArrayUpdate( { title: value }, itemIndex );
	}
	onChangeLabel( value, itemIndex ) {
		this.saveArrayUpdate( { label: value }, itemIndex );
	}
	onChangeLink( value, itemIndex ) {
		this.saveArrayUpdate( { link: value }, itemIndex );
	}
	onChangeIcon( value, itemIndex ) {
		this.saveArrayUpdate( { icon: value }, itemIndex );
	}
	onChangeURL( value, itemIndex ) {
		this.saveArrayUpdate( { url: value }, itemIndex );
	}
	onChangeAttachment( value, itemIndex ) {
		this.saveArrayUpdate( { imageid: value }, itemIndex );
	}
	onChangeWidth( value, itemIndex ) {
		this.saveArrayUpdate( { width: value }, itemIndex );
	}
	onChangeSource( value, itemIndex ) {
		this.saveArrayUpdate( { source: value }, itemIndex );
	}
	removeItem( itemIndex ) {
		let updateState = this.state.value;
		let update = updateState.items;
		let updateItems = [];
		{ update.length > 0 && (
			update.map( ( old, index ) => {
				if ( itemIndex !== index ) {
					updateItems.push( old );
				}
			} )
		) };
		updateState.items = updateItems;
		this.setState( { value: updateState } );
		this.updateValues( updateState );
	}
	addItem() {
		const itemControl = this.state.control;
		this.setState( { isVisible: false } );
		if ( itemControl ) {
			let updateState = this.state.value;
			let update = updateState.items;
			const itemTitle = this.controlParams.options.filter(function(o){return o.value === itemControl;} );
			const itemLabel = this.controlParams.options.filter(function(o){return o.value === itemControl;} );
			let newItem = {
				'id': itemControl,
				'enabled': true,
				'source': 'icon',
				'url': '',
				'imageid': '',
				'width': 24,
				'link': '',
				'icon': itemControl,
				'title': itemTitle[0].title,
				'label': itemLabel[0].content,
			};
			update.push( newItem );
			updateState.items = update;
			let availibleContactOptions = [];
			this.controlParams.options.map( ( option ) => {
				if ( ! update.some( obj => obj.id === option.value ) ) {
					availibleContactOptions.push( option );
				}
			} );
			this.setState( { control: ( undefined !== availibleContactOptions[0] && undefined !== availibleContactOptions[0].value ? availibleContactOptions[0].value : '' ) } );
			this.setState( { value: updateState } );
			this.updateValues( updateState );
		}
	}
	onDragEnd( items ) {
		let updateState = this.state.value;
		let update = updateState.items;
		let updateItems = [];
		{ items.length > 0 && (
			items.map( ( item ) => {
				update.filter( obj => {
					if ( obj.id === item.id ) {
						updateItems.push( obj );
					}
				} )
			} )
		) };
		if ( ! this.arraysEqual( update, updateItems ) ) {
			update.items = updateItems;
			updateState.items = updateItems;
			this.setState( { value: updateState } );
			this.updateValues( updateState );
		}
	}
	arraysEqual( a, b ) {
		if (a === b) return true;
		if (a == null || b == null) return false;
		if (a.length != b.length) return false;		
		for (var i = 0; i < a.length; ++i) {
			if (a[i] !== b[i]) return false;
		}
		return true;
	}
	render() {
		const currentList = ( typeof this.state.value != "undefined" && this.state.value.items != null && this.state.value.items.length != null && this.state.value.items.length > 0 ? this.state.value.items : [] );
		let theItems = [];
		{ currentList.length > 0 && (
			currentList.map( ( item ) => {
				theItems.push(
					{
						id: item.id,
					}
				)
			} )
		) };
		const availibleContactOptions = [];
		this.controlParams.options.map( ( option ) => {
			if ( ! theItems.some( obj => obj.id === option.value ) ) {
				availibleContactOptions.push( option );
			}
		} )
		const toggleClose = () => {
			if ( this.state.isVisible === true ) {
				this.setState( { isVisible: false } );
			}
		};
		return (
			<div className="base-control-field base-sorter-items">
				<div className="base-sorter-row">
					<ReactSortable animation={100} onStart={ () => this.onDragStop() } onEnd={ () => this.onDragStop() } group={ this.controlParams.group } className={ `base-sorter-drop base-sorter-sortable-panel base-sorter-drop-${ this.controlParams.group } base-sorter-drop-social_item_group` } handle={ '.base-sorter-item-panel-header' } list={ theItems } setList={ ( newState ) => this.onDragEnd( newState ) } >
						{ currentList.length > 0 && (
							currentList.map( ( item, index ) => {
								return <ItemComponent removeItem={ ( remove ) => this.removeItem( remove ) } toggleEnabled={ ( enable, itemIndex ) => this.toggleEnableItem( enable, itemIndex ) } onChangeTitle={ ( title, itemIndex ) => this.onChangeTitle( title, itemIndex ) } onChangeLabel={ ( label, itemIndex ) => this.onChangeLabel( label, itemIndex ) } onChangeLink={ ( link, itemIndex ) => this.onChangeLink( link, itemIndex ) } onChangeSource={ ( source, itemIndex ) => this.onChangeSource( source, itemIndex ) } onChangeWidth={ ( width, itemIndex ) => this.onChangeWidth( width, itemIndex ) } onChangeURL={ ( url, itemIndex ) => this.onChangeURL( url, itemIndex ) } onChangeAttachment={ ( imageid, itemIndex ) => this.onChangeAttachment( imageid, itemIndex ) } onChangeIcon={ ( icon, itemIndex ) => this.onChangeIcon( icon, itemIndex ) } key={ item.id } index={ index } item={ item } controlParams={ this.controlParams } />;
							} )
						) }
					</ReactSortable>
				</div>
				{ undefined !== availibleContactOptions[0] && undefined !== availibleContactOptions[0].value && (
					<div className="base-contact-add-area base-social-add-area">
						{ this.state.isVisible && (
							<Popover position="top right" className="base-popover-color base-popover-contact base-customizer-popover" onClose={ toggleClose }>
								<div className="base-popover-contact-list base-popover-social-list">
									<ButtonGroup className="base-radio-container-control">
										{ availibleContactOptions.map( ( item, index ) => {
											return (
												<Fragment>
													<Button
														isTertiary
														className={ 'contact-radio-btn' }
														onClick={ () => {
															this.setState( { control: availibleContactOptions[index].value } );
															this.state.control = availibleContactOptions[index].value;
															this.addItem();
														} }
													>
														{ availibleContactOptions[index].label && (
															availibleContactOptions[index].label
														) }
													</Button>
												</Fragment>
											);
										} ) }
									</ButtonGroup>
								</div>
							</Popover>
						) }
						<Button
							className="base-sorter-add-item"
							isPrimary
							onClick={ () => {
								this.setState( { isVisible: true } );
							} }
						>
							{ __( 'Add Contact', 'avanam' ) }
							<Dashicon icon="plus"/>
						</Button>
					</div>
				) }
			</div>
		);
	}
	updateValues( value ) {
		this.props.control.setting.set( {
			...this.props.control.setting.get(),
			...value,
			flag: !this.props.control.setting.get().flag
		} );
	}
}

ContactComponent.propTypes = {
	control: PropTypes.object.isRequired,
};

export default ContactComponent;
